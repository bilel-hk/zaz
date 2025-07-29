<?php
// Simple front-end for bulk mailer
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>High-Performance PHP Mailer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">High-Performance PHP Mailer</h1>

    <form action="send.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <h4 class="mt-3">Message Details</h4>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">From Name</label>
                <input type="text" name="from_name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">From Email</label>
                <input type="email" name="from_email" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Reply-To (optional)</label>
                <input type="email" name="reply_to" class="form-control">
            </div>
            <div class="col-md-12">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" required>
            </div>
            <div class="col-md-12">
                <label class="form-label">HTML Content</label>
                <textarea name="html_content" class="form-control" rows="10" required></textarea>
            </div>
        </div>

        <h4 class="mt-4">Recipients</h4>
        <p class="text-muted">Either paste e-mail addresses (one per line / comma-separated) <em>or</em> upload a CSV with a column labelled <code>email</code>.</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Paste addresses here</label>
                <textarea name="recipients_text" class="form-control" rows="6"></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Upload CSV</label>
                <input type="file" name="recipients_file" accept=".csv" class="form-control">
            </div>
            <div class="col-md-12">
                <label class="form-label">CC (comma-separated)</label>
                <input type="text" name="cc" class="form-control" placeholder="e.g. cc1@example.com, cc2@example.com">
            </div>
            <div class="col-md-12">
                <label class="form-label">BCC (comma-separated)</label>
                <input type="text" name="bcc" class="form-control" placeholder="e.g. hidden@example.com">
            </div>
        </div>

        <h4 class="mt-4">SMTP Configuration</h4>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Host</label>
                <input type="text" name="smtp_host" value="dev.thenannypages.ca" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Port</label>
                <input type="number" name="smtp_port" value="587" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Username</label>
                <input type="text" name="smtp_username" value="emaildevthen@dev.thenannypages.ca" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Password</label>
                <input type="password" name="smtp_password" value="uEv%I9*1p10N5" class="form-control" required>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg">Send Emails</button>
        </div>
    </form>
</div>
<script>
// Bootstrap validation
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>
</body>
</html>