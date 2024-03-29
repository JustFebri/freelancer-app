<!DOCTYPE html>
<html>

<head>
    <title>New Order Notification: Order ID</title>
</head>

<body>
    <p>Dear {{ $freelancerName }},</p>
    <p>I hope this email finds you well.</p>

    <p>I am writing to inform you that a new order has been placed for your services on our platform. The details of the order are as follows:</p>

    <ul>
        <li><strong>Order ID:</strong> {{ $orderId }}</li>
        <li><strong>Client's Name:</strong> {{ $clientName }}</li>
    </ul>

    <p>Please review the order details and ensure that you are able to fulfill the client's requirements within the specified deadline. If you have any questions or concerns regarding the order, please don't hesitate to reach out to us.</p>

    <p>Best regards, Admin</p>
</body>

</html>