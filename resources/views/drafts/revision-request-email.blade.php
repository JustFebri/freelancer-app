<!DOCTYPE html>
<html>

<head>
    <title>Order Revision Request</title>
</head>

<body>
    <p>Hello {{ $freelancerName }},</p>

    <p>Your client, {{ $clientName }}, has requested a revision for project with order ID: "{{ $orderId }}."</p>

    <p><strong>Revision Details:</strong></p>
    <p>{{ $revisionDetails }}</p>

    <p>Please review the revision request and communicate with the client to ensure a clear understanding of their
        requirements. Once you are ready to proceed, you can accept or reject the revision through your dashboard.</p>

    <p>Best regards,</p>
    <p>Admin</p>
</body>

</html>
