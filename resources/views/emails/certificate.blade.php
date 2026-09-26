<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate PortaBKN</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin:0 auto; padding: 20px; border:1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #2c3e50;">Hello {{ $user->name }},</h2>
        <p>Congratulations! You have successfully completed the course <strong>{{ $event->title }}</strong> and earned your certificate.</p>

        <p>You can download your certificate.</p>

        <br>
        <p>Best regards,<br>
        <strong>The PortaBKN Team</strong></p>
    </div>
</body>
</html>