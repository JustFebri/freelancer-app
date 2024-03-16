<!DOCTYPE html>
<html>

<head>
    <title>Order Revision Request</title>
</head>

<body>
    <p>Hello {{ $clientName }},</p>

    <p>Your revision request for the project with order ID: "{{ $orderId }}" has been rejected by the freelancer.
    </p>

    <p><strong>Rejection Reason:</strong></p>
    <p>{{ $rejectionReason }}</p>

    <p>We understand that revisions are important, and we encourage you to communicate with the freelancer to resolve
        any concerns or provide additional information for a successful collaboration.</p>

    <p>If you have any further questions or need assistance, feel free to reach out to our support team.</p>

    <p>Best regards,</p>
    <p>Admin</p>
</body>

</html>
