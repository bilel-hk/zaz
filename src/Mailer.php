<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Spatie\Async\Pool;

class Mailer
{
    private array $smtpConfig;
    private int $concurrency;
    private ?int $microsecondsBetweenSends;

    public function __construct(array $smtpConfig, int $concurrency = 100, ?int $microsecondsBetweenSends = null)
    {
        $this->smtpConfig = $smtpConfig;
        $this->concurrency = $concurrency;
        $this->microsecondsBetweenSends = $microsecondsBetweenSends; // null = unlimited
    }

    /**
     * Send an HTML email to many recipients in parallel.
     *
     * @param string      $fromEmail
     * @param string      $fromName
     * @param string      $subject
     * @param string      $htmlBody
     * @param array       $recipients List of recipient e-mail addresses
     * @param string|null $replyTo
     * @param array       $cc
     * @param array       $bcc
     * @return array [
     *              'sent'   => int,
     *              'failed' => int,
     *              'details'=> array of [email, status, error]
     *            ]
     */
    public function sendBulk(
        string $fromEmail,
        string $fromName,
        string $subject,
        string $htmlBody,
        array $recipients,
        ?string $replyTo = null,
        array $cc = [],
        array $bcc = []
    ): array {
        $results = [];

        $pool = Pool::create()->concurrency($this->concurrency);

        $smtp = $this->smtpConfig;
        foreach ($recipients as $email) {
            $pool->add(function () use (
                $email,
                $fromEmail,
                $fromName,
                $subject,
                $htmlBody,
                $replyTo,
                $cc,
                $bcc,
                $smtp
            ) {
                $mailer = new PHPMailer(true);

                // SMTP settings
                $mailer->isSMTP();
                $mailer->Host       = $smtp['host'];
                $mailer->SMTPAuth   = true;
                $mailer->Username   = $smtp['username'];
                $mailer->Password   = $smtp['password'];
                $mailer->Port       = $smtp['port'] ?? 587;
                $mailer->SMTPSecure = $smtp['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;
                $mailer->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ];

                // Enable SMTP pipelining for efficiency (if supported)
                $mailer->SMTPDebug = SMTP::DEBUG_OFF;
                $mailer->do_verp   = false;

                // Message
                $mailer->setFrom($fromEmail, $fromName);
                $mailer->clearAddresses();
                $mailer->addAddress($email);

                if ($replyTo) {
                    $mailer->addReplyTo($replyTo);
                }
                foreach ($cc as $ccEmail) {
                    $mailer->addCC($ccEmail);
                }
                foreach ($bcc as $bccEmail) {
                    $mailer->addBCC($bccEmail);
                }

                $mailer->Subject = $subject;
                $mailer->isHTML(true);
                $mailer->Body    = $htmlBody;

                if ($this->microsecondsBetweenSends) {
                    usleep($this->microsecondsBetweenSends);
                }

                $mailer->send();

                return ['email' => $email, 'status' => 'sent'];
            })->then(function ($output) use (&$results) {
                $results[] = $output;
            })->catch(function (\Throwable $e) use (&$results, $email) {
                $results[] = ['email' => $email, 'status' => 'failed', 'error' => $e->getMessage()];
            });
        }

        // Wait for all tasks
        $pool->wait();

        $sent   = count(array_filter($results, static fn($r) => $r['status'] === 'sent'));
        $failed = count($results) - $sent;

        return [
            'sent'    => $sent,
            'failed'  => $failed,
            'details' => $results,
        ];
    }
}