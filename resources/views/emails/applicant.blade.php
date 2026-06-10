<!DOCTYPE html>
<html lang="uk">
<head><meta charset="utf-8"></head>
<body style="margin:0;padding:24px;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;">
        <div style="background:#16223f;padding:18px 24px;">
            <h1 style="margin:0;font-size:18px;color:#ffffff;">Нова заявка абітурієнта</h1>
        </div>
        <div style="padding:24px;">
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <tr>
                    <td style="padding:8px 0;color:#64748b;width:140px;">ПІБ</td>
                    <td style="padding:8px 0;font-weight:bold;">{{ $applicant->name }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#64748b;">Телефон</td>
                    <td style="padding:8px 0;font-weight:bold;"><a href="tel:{{ $applicant->phone }}">{{ $applicant->phone }}</a></td>
                </tr>
                @if ($applicant->email)
                    <tr>
                        <td style="padding:8px 0;color:#64748b;">Email</td>
                        <td style="padding:8px 0;">{{ $applicant->email }}</td>
                    </tr>
                @endif
                @if ($applicant->specialty)
                    <tr>
                        <td style="padding:8px 0;color:#64748b;">Спеціальність</td>
                        <td style="padding:8px 0;">{{ $applicant->specialty->title }}</td>
                    </tr>
                @endif
                @if ($applicant->message)
                    <tr>
                        <td style="padding:8px 0;color:#64748b;vertical-align:top;">Питання</td>
                        <td style="padding:8px 0;white-space:pre-line;">{{ $applicant->message }}</td>
                    </tr>
                @endif
            </table>
            <p style="margin:20px 0 0;font-size:12px;color:#94a3b8;">
                Надіслано з форми «Залишити заявку» на сайті. IP: {{ $applicant->ip }} · {{ $applicant->created_at->format('d.m.Y H:i') }}
            </p>
        </div>
    </div>
</body>
</html>
