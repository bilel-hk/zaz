<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Mailer;

set_time_limit(0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Helper to sanitize and fetch input
function post(string $key, $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

$fromName  = post('from_name');
$fromEmail = post('from_email');
$subject   = post('subject');
$htmlBody  = post('html_content');
$replyTo   = post('reply_to') ?: null;
$ccRaw     = post('cc', '');
$bccRaw    = post('bcc', '');

// Build recipient list
$recipients = [];

$paste = post('recipients_text', '');
if ($paste) {
    $paste = str_replace(["\r", ';'], "\n", $paste);
    $lines = explode("\n", $paste);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $recipients[] = $line;
        }
    }
}

if (!empty($_FILES['recipients_file']['tmp_name'])) {
    $csvPath = $_FILES['recipients_file']['tmp_name'];
    if (($handle = fopen($csvPath, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            foreach ($data as $cell) {
                $cell = trim($cell);
                if (filter_var($cell, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = $cell;
                }
            }
        }
        fclose($handle);
    }
}

$recipients = array_unique($recipients);

if (empty($recipients)) {
    exit('No valid recipients provided.');
}

$cc  = array_filter(array_map('trim', explode(',', $ccRaw)));
$bcc = array_filter(array_map('trim', explode(',', $bccRaw)));

$smtpConfig = [
    'host'       => post('smtp_host'),
    'port'       => (int) post('smtp_port', 587),
    'username'   => post('smtp_username'),
    'password'   => post('smtp_password'),
    'encryption' => 'tls',
];

$mailer = new Mailer($smtpConfig, concurrency: 100);

$result = $mailer->sendBulk(
    fromEmail: $fromEmail,
    fromName:  $fromName,
    subject:   $subject,
    htmlBody:  $htmlBody,
    recipients: $recipients,
    replyTo:   $replyTo,
    cc:        $cc,
    bcc:       $bcc
);

// Save log
$timestamp = date('Ymd-His');
$logDir = __DIR__ . '/../storage/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}
$logFile = sprintf('%s/%s.csv', $logDir, $timestamp);
$fp = fopen($logFile, 'w');
fputcsv($fp, ['email', 'status', 'error']);
foreach ($result['details'] as $row) {
    fputcsv($fp, [$row['email'], $row['status'], $row['error'] ?? '']);
}
fclose($fp);

// Output summary
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-3">Send Results</h2>
    <p>
        Sent: <strong><?= $result['sent'] ?></strong><br>
        Failed: <strong><?= $result['failed'] ?></strong><br>
        Log file: <code><?= basename($logFile) ?></code>
    </p>

    <table class="table table-bordered table-sm">
        <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Email</th>
            <th>Status</th>
            <th>Error</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($result['details'] as $index => $row): ?>
            <tr class="<?= $row['status'] === 'sent' ? 'table-success' : 'table-danger' ?>">
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= htmlspecialchars($row['error'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>