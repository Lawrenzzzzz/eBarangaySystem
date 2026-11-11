# SMTP service

Standalone Python helper that sends the e-Barangay verification emails. PHP calls
`send_email.py` and passes a JSON payload on stdin; SMTP settings come from the
environment.

## Gmail setup

1. Enable 2-Step Verification on the Google account.
2. Create an **App password** at <https://myaccount.google.com/apppasswords>.
3. Add these values to `backend/.env`:

```
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your.address@gmail.com
SMTP_PASS=the-16-character-app-password
SMTP_FROM_NAME=e-Barangay Bucana
SMTP_USE_SSL=0
PYTHON_BIN=python
```

Use port `465` with `SMTP_USE_SSL=1` if your host blocks STARTTLS.

## Manual test

```
echo '{"to":"someone@gmail.com","subject":"Test","text":"Hello"}' | python send_email.py
```
