<!DOCTYPE html>
<html>

<head>
    <title>Order Rejected: Order ID</title>
</head>

<body>
    <p>Dear {{ $clientName }},</p>
    <p>We regret to inform you that the freelancer has rejected your order.</p>

    <p>Here are the details of the order:</p>

    <ul>
        <li><strong>Order ID:</strong> {{ $orderId }}</li>
        <li><strong>Freelancer's Name:</strong> {{ $freelancerName }}</li>
    </ul>

    <p>We apologize for any inconvenience this may cause. If you have any questions or need further assistance, please feel free to contact us.</p>

    <p>We appreciate your understanding and hope to assist you with your future projects.</p>

    <p>Best regards, Admin</p>
</body>

</html>