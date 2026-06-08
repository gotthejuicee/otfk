<!DOCTYPE html>
<html lang="uk">
<head><meta charset="utf-8"><title>Нове звернення</title></head>
<body style="font-family: Arial, sans-serif; color: #1e293b; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 24px;">
        <h2 style="color: #0f2a52; margin-bottom: 4px;">Нове звернення з сайту</h2>
        <p style="color: #64748b; margin-top: 0;">ОТФК ОНТУ · форма зворотного звʼязку</p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
            <tr><td style="padding: 6px 0; color:#64748b; width: 130px;">Імʼя</td><td style="padding: 6px 0; font-weight: 600;">{{ $feedback->name }}</td></tr>
            @if ($feedback->email)
                <tr><td style="padding: 6px 0; color:#64748b;">Email</td><td style="padding: 6px 0;"><a href="mailto:{{ $feedback->email }}">{{ $feedback->email }}</a></td></tr>
            @endif
            @if ($feedback->phone)
                <tr><td style="padding: 6px 0; color:#64748b;">Телефон</td><td style="padding: 6px 0;">{{ $feedback->phone }}</td></tr>
            @endif
            @if ($feedback->subject)
                <tr><td style="padding: 6px 0; color:#64748b;">Тема</td><td style="padding: 6px 0;">{{ $feedback->subject }}</td></tr>
            @endif
        </table>

        <div style="margin-top: 16px; padding: 16px; background: #f1f5f9; border-radius: 8px;">
            <p style="margin: 0; white-space: pre-wrap;">{{ $feedback->message }}</p>
        </div>

        <p style="color: #94a3b8; font-size: 12px; margin-top: 20px;">
            Надіслано {{ $feedback->created_at?->format('d.m.Y H:i') ?? now()->format('d.m.Y H:i') }} · IP: {{ $feedback->ip }}
        </p>
    </div>
</body>
</html>
