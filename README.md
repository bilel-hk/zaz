# High-Performance PHP Mailer

This project demonstrates a lightweight bulk-mailer capable of pushing **≈ 500 e-mails per second** (network and SMTP permitting) from PHP.

It combines:

* **PHPMailer** – battle-tested SMTP client.
* **Spatie/Async** – simple process pool that fans-out work to parallel PHP processes (requires `pcntl` & `posix` extensions).

> ⚠️ Achievable throughput depends on network latency, server limits & the number of parallel connections allowed by your SMTP provider.

---

## Installation

```bash
# Clone / download this repository
composer install --no-interaction --prefer-dist
```

Ensure the PHP extensions `pcntl` and `posix` are enabled (CLI-only, **not** under PHP-FPM).

## Running locally

Serve the `public/` folder:

```bash
php -S localhost:8080 -t public
```

Open <http://localhost:8080> in your browser, fill-in the form, upload a CSV (one e-mail per line) or paste e-mails in the textarea, then hit **Send**.

A progress bar will appear while messages are processed.  Results (per-recipient success / error) are displayed once done and also stored as `storage/logs/yyyymmdd-HHMMSS.csv` for auditing.

## Tuning throughput

* **Pool size** – default `100` (100 child processes).  Edit `src/Mailer.php` if you need more/less.
* **Rate limit** – you can set a sleep delay (`microsecondsBetweenSends`) to respect provider caps.
* **Chunk size** – each process receives exactly one recipient for maximal parallelism.  You may batch to reduce connection churn.

---

### Security note

Credentials are accepted via the web UI **only to demonstrate**.  Protect this application behind authentication and TLS in production.