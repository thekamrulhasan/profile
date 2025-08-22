<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form Message</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .footer { margin-top: 20px; padding: 10px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Form Message</h2>
            <p>You have received a new message from your portfolio website.</p>
        </div>
        
        <div class="content">
            <p><strong>From:</strong> {{ $senderName }} ({{ $senderEmail }})</p>
            <p><strong>Message:</strong></p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;">
                {!! nl2br(e($messageContent)) !!}
            </div>
        </div>
        
        <div class="footer">
            <p>This message was sent from the contact form on your portfolio website.</p>
            <p>Reply directly to this email to respond to {{ $senderName }}.</p>
        </div>
    </div>
</body>
</html>
