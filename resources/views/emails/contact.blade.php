<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Contact Message</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #111827; }
        .container { max-width: 640px; margin: 0 auto; padding: 16px; }
        .box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #ffffff; }
        .label { color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; }
        .value { margin: 4px 0 12px; font-size: 14px; }
        .message { white-space: pre-wrap; background: #f9fafb; border-radius: 6px; padding: 12px; }
    </style>
    </head>
<body>
    <div class="container">
        <h2>New Contact Message</h2>
        <div class="box">
            <div>
                <div class="label">Name</div>
                <div class="value">{{ $data['name'] ?? '—' }}</div>
            </div>
            <div>
                <div class="label">Email</div>
                <div class="value">{{ $data['email'] ?? '—' }}</div>
            </div>
            <div>
                <div class="label">Subject</div>
                <div class="value">{{ $data['subject'] ?? '—' }}</div>
            </div>
            <div>
                <div class="label">Message</div>
                <div class="message">{{ $data['message'] ?? '' }}</div>
            </div>
        </div>
    </div>
</body>
</html>

