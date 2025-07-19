# Platphorma Bulk Mailer

A professional PHP bulk email marketing tool designed for cPanel hosting with HTML support and SMTP authentication.

## 🚀 Features

- **Secure Authentication** - Password-protected access
- **HTML Email Support** - Send rich formatted emails
- **SMTP Integration** - Enhanced delivery with PHPMailer
- **Batch Processing** - Configurable batch limits for server performance
- **Email Templates** - Pre-built newsletter and promotional templates
- **Test Function** - Verify configuration before bulk sending
- **Modern UI** - Clean, responsive interface
- **Auto-save** - Form data persistence
- **Error Handling** - Comprehensive error reporting
- **Rate Limiting** - Spam prevention with configurable delays

## 📦 Package Contents

1. **`bulk_mailer.php`** - Basic version using PHP's built-in mail function
2. **`phpmailer_bulk.php`** - Advanced version with PHPMailer support (recommended)
3. **`README.md`** - This installation guide

## 🛠️ Installation Instructions

### Step 1: Upload to cPanel

1. **Access cPanel File Manager**
   - Log into your cPanel account
   - Open "File Manager"
   - Navigate to `public_html` (or your domain's folder)

2. **Upload Scripts**
   - Upload `bulk_mailer.php` or `phpmailer_bulk.php` (or both)
   - Set file permissions to 644

### Step 2: Configure Email Settings

Edit the configuration section in your chosen PHP file:

```php
// Email configuration for platphorma.com
$email_config = [
    'smtp_host' => 'mail.platphorma.com',        // Your SMTP server
    'smtp_port' => 587,                          // SMTP port (587 for TLS, 465 for SSL)
    'smtp_secure' => 'tls',                      // 'tls' or 'ssl'
    'username' => 'supports@platphorma.com',     // Your email username
    'password' => 'w^L93K*Sw-?%/',              // Your email password
    'from_email' => 'supports@platphorma.com',   // Sender email
    'from_name' => 'Platphorma Support'          // Sender name
];
```

### Step 3: Change Admin Password

```php
define('ADMIN_PASSWORD', 'mailer2024!@#'); // Change this to your secure password
```

### Step 4: Adjust Settings (Optional)

```php
define('MAX_EMAILS_PER_BATCH', 50);    // Maximum emails per batch
define('DELAY_BETWEEN_EMAILS', 2);     // Delay in seconds between emails
```

## 🔧 Usage Guide

### Accessing the Mailer

1. Navigate to `https://yourdomain.com/bulk_mailer.php` (or `phpmailer_bulk.php`)
2. Enter your admin password
3. You'll see the main dashboard

### Sending Emails

1. **Test Configuration** (Recommended)
   - Use the test email section first
   - Enter your email address
   - Click "Send Test" to verify setup

2. **Compose Bulk Email**
   - Enter recipient email addresses (one per line or comma-separated)
   - Add subject line
   - Choose HTML formatting if needed
   - Compose your message
   - Click "Send Bulk Emails"

### Using Templates

1. Go to the "Templates" tab
2. Choose from pre-built templates
3. Click "Use This Template"
4. Customize the content as needed

## 📧 Email Configuration Help

### Finding Your SMTP Settings

**For cPanel/WHM hosting:**
- SMTP Host: Usually `mail.yourdomain.com`
- Port: 587 (TLS) or 465 (SSL)
- Security: TLS (recommended)

**Common Ports:**
- 25 (Plain, often blocked)
- 587 (TLS, recommended)
- 465 (SSL, secure)

### Testing SMTP Settings

If you're unsure about your SMTP settings:
1. Check your hosting provider's documentation
2. Contact your hosting support
3. Use the test email function to verify

## 🔒 Security Features

- **Password Protection** - Prevents unauthorized access
- **Session Management** - Secure login/logout
- **Input Validation** - Email address verification
- **Rate Limiting** - Prevents spam behavior
- **Error Logging** - Track sending issues

## ⚡ Performance Optimization

### Batch Size Recommendations

- **Shared Hosting**: 25-50 emails per batch
- **VPS/Dedicated**: 100-500 emails per batch
- **High-end servers**: 1000+ emails per batch

### Delay Settings

- **Conservative**: 2-5 seconds between emails
- **Standard**: 1-2 seconds between emails
- **Aggressive**: 0.5-1 seconds between emails

## 🚨 Important Notes

### Spam Prevention

1. **Authenticate your domain** with SPF, DKIM, and DMARC records
2. **Warm up your IP** by sending small batches initially
3. **Maintain clean lists** - remove bounced emails
4. **Follow CAN-SPAM** and GDPR regulations
5. **Use proper subject lines** - avoid spam trigger words

### Legal Compliance

- Only send to opted-in recipients
- Include unsubscribe links
- Add your physical address
- Respect unsubscribe requests
- Follow local email marketing laws

### Server Limits

- Check your hosting provider's email sending limits
- Monitor for bounce rates and spam complaints
- Consider using a dedicated email service for large volumes

## 🔧 Troubleshooting

### Common Issues

**Authentication Failed:**
- Verify SMTP credentials
- Check if 2FA is enabled on email account
- Try different ports (587, 465, 25)

**Emails Not Sending:**
- Test with a single email first
- Check server error logs
- Verify recipient email addresses
- Ensure firewall allows SMTP connections

**Slow Performance:**
- Reduce batch size
- Increase delay between emails
- Check server resources

**PHPMailer Not Working:**
- The script auto-downloads PHPMailer
- Ensure your server allows external connections
- Check if `curl` is enabled
- Fall back to built-in mail function

### Error Messages

**"Class 'PHPMailer' not found":**
- Use the auto-download feature
- Manually download PHPMailer to `/PHPMailer/` folder
- Switch to built-in mail function

**"SMTP connection failed":**
- Verify SMTP settings
- Check firewall rules
- Try different authentication methods

## 📊 Monitoring & Analytics

### Email Tracking

The script provides:
- Send success/failure counts
- Execution time tracking
- Error message logging
- Batch processing status

### Best Practices

1. **Start Small** - Test with 5-10 emails first
2. **Monitor Bounces** - Keep track of failed deliveries
3. **Check Spam Scores** - Use tools like Mail Tester
4. **Maintain Lists** - Regular cleanup of invalid emails
5. **Track Engagement** - Monitor open/click rates externally

## 🆘 Support

For technical support with the script:
1. Check the troubleshooting section
2. Review your hosting provider's email policies
3. Test with the basic version if advanced features fail

## 📝 Changelog

### Version 2.0 (Advanced)
- Added PHPMailer support
- Enhanced UI with tabs
- Test email functionality
- Template system
- Improved error handling
- Performance metrics

### Version 1.0 (Basic)
- Core bulk email functionality
- HTML support
- Basic authentication
- Simple UI

## ⚖️ License

This script is provided as-is for educational and legitimate email marketing purposes. Users are responsible for compliance with applicable laws and regulations.

---

**Remember**: Always respect email recipients and follow best practices for email marketing. Misuse of this tool for spam is strictly prohibited and may violate terms of service and laws.