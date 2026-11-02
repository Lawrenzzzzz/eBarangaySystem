#!/usr/bin/env python3
"""Send a single email over SMTP (Gmail by default).

Reads a JSON payload from stdin:
    {"to": "user@example.com", "subject": "...", "text": "...", "html": "..."}

SMTP settings are read from the environment:
    SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_FROM_NAME, SMTP_USE_SSL

Writes a JSON result to stdout:
    {"sent": true}
    {"sent": false, "error": "..."}
"""

import json
import os
import smtplib
import ssl
import sys
from email.message import EmailMessage
from email.utils import formataddr


def fail(message: str) -> None:
    print(json.dumps({"sent": False, "error": message}))
    sys.exit(1)


def main() -> None:
    try:
        payload = json.load(sys.stdin)
    except (ValueError, OSError) as exc:
        fail("Invalid JSON payload: %s" % exc)

    recipient = str(payload.get("to", "")).strip()
    if not recipient:
        fail("Missing recipient email address.")

    host = os.environ.get("SMTP_HOST", "smtp.gmail.com").strip()
    try:
        port = int(os.environ.get("SMTP_PORT", "587"))
    except ValueError:
        port = 587
    user = os.environ.get("SMTP_USER", "").strip()
    password = os.environ.get("SMTP_PASS", "")
    from_name = os.environ.get("SMTP_FROM_NAME", "e-Barangay San Isidro").strip()
    use_ssl = os.environ.get("SMTP_USE_SSL", "").strip().lower() in ("1", "true", "yes")

    if not user or not password:
        fail("SMTP credentials are not configured.")

    message = EmailMessage()
    message["Subject"] = str(payload.get("subject", "e-Barangay verification"))
    message["From"] = formataddr((from_name, user))
    message["To"] = recipient
    message.set_content(str(payload.get("text", "")))

    html = payload.get("html")
    if html:
        message.add_alternative(str(html), subtype="html")

    context = ssl.create_default_context()
    try:
        if use_ssl:
            with smtplib.SMTP_SSL(host, port, timeout=25, context=context) as server:
                server.login(user, password)
                server.send_message(message)
        else:
            with smtplib.SMTP(host, port, timeout=25) as server:
                server.ehlo()
                server.starttls(context=context)
                server.ehlo()
                server.login(user, password)
                server.send_message(message)
    except Exception as exc:  # noqa: BLE001 - surface any SMTP failure to PHP
        fail("%s: %s" % (type(exc).__name__, exc))

    print(json.dumps({"sent": True}))


if __name__ == "__main__":
    main()
