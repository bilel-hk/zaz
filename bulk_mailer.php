<?php
session_start();

// Configuration
define('ADMIN_PASSWORD', 'mailer2024!@#'); // Change this password
define('MAX_EMAILS_PER_BATCH', 50);
define('DELAY_BETWEEN_EMAILS', 1); // seconds

// Email configuration for platphorma.com
$email_config = [
    'smtp_host' => 'mail.platphorma.com',
    'smtp_port' => 587,
    'smtp_secure' => 'tls', // or 'ssl' depending on server
    'username' => 'supports@platphorma.com',
    'password' => 'w^L93K*Sw-?%/',
    'from_email' => 'supports@platphorma.com',
    'from_name' => 'Platphorma Support'
];

// Check if user is authenticated
function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

// Handle authentication
if (isset($_POST['login'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['authenticated'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $login_error = "Invalid password!";
    }
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// PHPMailer integration (using built-in mail function as fallback)
function sendBulkEmails($recipients, $subject, $message, $isHTML = true) {
    global $email_config;
    
    $sent = 0;
    $failed = 0;
    $errors = [];
    
    // Check if PHPMailer is available
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return sendWithPHPMailer($recipients, $subject, $message, $isHTML);
    } else {
        return sendWithBuiltInMail($recipients, $subject, $message, $isHTML);
    }
}

function sendWithBuiltInMail($recipients, $subject, $message, $isHTML) {
    global $email_config;
    
    $sent = 0;
    $failed = 0;
    $errors = [];
    
    $headers = "From: {$email_config['from_name']} <{$email_config['from_email']}>\r\n";
    $headers .= "Reply-To: {$email_config['from_email']}\r\n";
    
    if ($isHTML) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    } else {
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    }
    
    foreach ($recipients as $email) {
        $email = trim($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (mail($email, $subject, $message, $headers)) {
                $sent++;
            } else {
                $failed++;
                $errors[] = "Failed to send to: $email";
            }
            
            // Delay between emails to avoid spam filters
            if (DELAY_BETWEEN_EMAILS > 0) {
                sleep(DELAY_BETWEEN_EMAILS);
            }
        } else {
            $failed++;
            $errors[] = "Invalid email: $email";
        }
    }
    
    return ['sent' => $sent, 'failed' => $failed, 'errors' => $errors];
}

// Handle bulk email sending
if (isAuthenticated() && isset($_POST['send_bulk'])) {
    $recipients_text = $_POST['recipients'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $is_html = isset($_POST['is_html']);
    
    // Parse recipients
    $recipients = [];
    $lines = explode("\n", $recipients_text);
    foreach ($lines as $line) {
        $emails = explode(',', $line);
        foreach ($emails as $email) {
            $email = trim($email);
            if (!empty($email)) {
                $recipients[] = $email;
            }
        }
    }
    
    if (!empty($recipients) && !empty($subject) && !empty($message)) {
        // Limit emails per batch
        if (count($recipients) > MAX_EMAILS_PER_BATCH) {
            $recipients = array_slice($recipients, 0, MAX_EMAILS_PER_BATCH);
            $batch_warning = "Limited to " . MAX_EMAILS_PER_BATCH . " emails per batch for server performance.";
        }
        
        $result = sendBulkEmails($recipients, $subject, $message, $is_html);
        $send_result = "Sent: {$result['sent']}, Failed: {$result['failed']}";
        
        if (!empty($result['errors'])) {
            $send_errors = implode('<br>', $result['errors']);
        }
    } else {
        $send_error = "Please fill in all required fields.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platphorma Bulk Mailer</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        
        .content {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input[type="password"],
        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        input[type="password"]:focus,
        input[type="text"]:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .textarea-large {
            min-height: 200px;
        }
        
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        .btn-logout {
            background: #dc3545;
            float: right;
            margin-top: -10px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        
        .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .stats {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .login-form {
            max-width: 400px;
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Platphorma Bulk Mailer</h1>
            <p>Professional Email Marketing Tool</p>
        </div>
        
        <div class="content">
            <?php if (!isAuthenticated()): ?>
                <!-- Login Form -->
                <div class="login-form">
                    <h2>Authentication Required</h2>
                    <?php if (isset($login_error)): ?>
                        <div class="alert alert-error"><?php echo $login_error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="password">Admin Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit" name="login">🔐 Login</button>
                    </form>
                </div>
            
            <?php else: ?>
                <!-- Main Mailer Interface -->
                <form method="POST" style="float: right;">
                    <button type="submit" name="logout" class="btn-logout">Logout</button>
                </form>
                
                <div style="clear: both;"></div>
                
                <div class="stats">
                    <h3>📊 System Information</h3>
                    <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_NAME']; ?></p>
                    <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                    <p><strong>Max Emails per Batch:</strong> <?php echo MAX_EMAILS_PER_BATCH; ?></p>
                    <p><strong>Email Account:</strong> <?php echo $email_config['from_email']; ?></p>
                </div>
                
                <?php if (isset($send_result)): ?>
                    <div class="alert alert-success">
                        <strong>📬 Sending Complete!</strong><br>
                        <?php echo $send_result; ?>
                        <?php if (isset($batch_warning)): ?>
                            <br><small><?php echo $batch_warning; ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($send_errors)): ?>
                    <div class="alert alert-warning">
                        <strong>⚠️ Some Issues Occurred:</strong><br>
                        <?php echo $send_errors; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($send_error)): ?>
                    <div class="alert alert-error">
                        <strong>❌ Error:</strong> <?php echo $send_error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="recipients">📧 Email Recipients:</label>
                        <textarea id="recipients" name="recipients" class="textarea-large" required placeholder="Enter email addresses separated by commas or new lines:&#10;user1@example.com, user2@example.com&#10;user3@example.com"></textarea>
                        <div class="help-text">Enter one email per line or separate multiple emails with commas. Max <?php echo MAX_EMAILS_PER_BATCH; ?> emails per batch.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">📝 Email Subject:</label>
                        <input type="text" id="subject" name="subject" required placeholder="Enter your email subject">
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_html" name="is_html" checked>
                            <label for="is_html">Enable HTML formatting</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">💌 Email Message:</label>
                        <textarea id="message" name="message" class="textarea-large" required placeholder="Enter your email message (HTML supported when checkbox is checked)"></textarea>
                        <div class="help-text">
                            HTML Example: &lt;h1&gt;Hello!&lt;/h1&gt;&lt;p&gt;This is &lt;strong&gt;bold&lt;/strong&gt; text.&lt;/p&gt;
                        </div>
                    </div>
                    
                    <button type="submit" name="send_bulk">🚀 Send Bulk Emails</button>
                </form>
                
                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
                    <h3>📋 Quick HTML Templates</h3>
                    <details>
                        <summary style="cursor: pointer; font-weight: bold;">Newsletter Template</summary>
                        <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; overflow-x: auto;"><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;title&gt;Newsletter&lt;/title&gt;
&lt;/head&gt;
&lt;body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;"&gt;
    &lt;div style="max-width: 600px; margin: 0 auto; padding: 20px;"&gt;
        &lt;h1 style="color: #667eea;"&gt;Welcome to Our Newsletter!&lt;/h1&gt;
        &lt;p&gt;Hello there,&lt;/p&gt;
        &lt;p&gt;We're excited to share our latest updates with you.&lt;/p&gt;
        &lt;div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;"&gt;
            &lt;h3&gt;What's New:&lt;/h3&gt;
            &lt;ul&gt;
                &lt;li&gt;New features and improvements&lt;/li&gt;
                &lt;li&gt;Special offers just for you&lt;/li&gt;
                &lt;li&gt;Upcoming events&lt;/li&gt;
            &lt;/ul&gt;
        &lt;/div&gt;
        &lt;p&gt;Best regards,&lt;br&gt;The Platphorma Team&lt;/p&gt;
    &lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
                    </details>
                    
                    <details style="margin-top: 10px;">
                        <summary style="cursor: pointer; font-weight: bold;">Promotional Template</summary>
                        <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 10px; overflow-x: auto;"><code>&lt;div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;"&gt;
    &lt;div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;"&gt;
        &lt;h1 style="margin: 0; font-size: 2em;"&gt;🎉 Special Offer!&lt;/h1&gt;
        &lt;p style="margin: 10px 0 0 0; font-size: 1.2em;"&gt;Limited Time Only&lt;/p&gt;
    &lt;/div&gt;
    &lt;div style="padding: 30px; background: white; border-radius: 0 0 10px 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"&gt;
        &lt;p&gt;Dear Valued Customer,&lt;/p&gt;
        &lt;p&gt;We're excited to offer you an exclusive deal that you won't want to miss!&lt;/p&gt;
        &lt;div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 5px; text-align: center; margin: 20px 0;"&gt;
            &lt;h2 style="color: #856404; margin: 0 0 10px 0;"&gt;50% OFF&lt;/h2&gt;
            &lt;p style="margin: 0; font-size: 1.1em;"&gt;Use code: &lt;strong&gt;SAVE50&lt;/strong&gt;&lt;/p&gt;
        &lt;/div&gt;
        &lt;p style="text-align: center;"&gt;
            &lt;a href="#" style="background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;"&gt;Shop Now&lt;/a&gt;
        &lt;/p&gt;
        &lt;p&gt;Thanks for being a loyal customer!&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                    </details>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Auto-save form data to localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[method="POST"]');
            if (!form || form.querySelector('input[name="login"]')) return;
            
            const inputs = form.querySelectorAll('input[type="text"], textarea');
            
            // Load saved data
            inputs.forEach(input => {
                const saved = localStorage.getItem('mailer_' + input.name);
                if (saved && input.name !== 'recipients') {
                    input.value = saved;
                }
            });
            
            // Save data on input
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    localStorage.setItem('mailer_' + this.name, this.value);
                });
            });
        });
    </script>
</body>
</html>