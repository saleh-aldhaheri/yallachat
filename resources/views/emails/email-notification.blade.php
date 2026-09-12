<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Notification</title>
</head>
<body style="margin:0;padding:0;background-color:#0d1117;font-family:'JetBrains Mono',ui-monospace,Menlo,monospace;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#0d1117;padding:48px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="max-width:480px;width:100%;background-color:#161b22;border:1px solid #30363d;border-radius:12px;">
                    <tr>
                        <td style="padding:40px 32px;">
                            <div style="font-size:20px;font-weight:700;color:#3fb950;letter-spacing:0.5px;">$ yalla-chat</div>

                            <div style="font-size:14px;color:#c9d1d9;line-height:1.6;margin:16px 0 28px;">
                                Hi {{ $receiverName }},<br>
                                This is an urgent message from {{ $senderName }}:
                            </div>

                            <div style="font-size:16px;color:#c9d1d9;line-height:1.7;margin-bottom:28px;">
                                {!! nl2br(e($content)) !!}
                            </div>

                            <div style="font-size:12px;color:#8b949e;line-height:1.6;">
                                Urgent message from YallaChat.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
