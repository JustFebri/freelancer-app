<!DOCTYPE html>
<html>
<head>
    <title>Confirmation: Order ID Accepted and In Progress</title>
</head>
<body>
    <p>Dear {{ $clientName }},</p>
    <p>I hope this email finds you well.</p>

    <p>I am pleased to inform you that your order has been accepted by our freelancer and is now in progress. We are excited to see your project come to life!</p>

    <p>Here are the details of the order:</p>

    <ul>
        <li><strong>Order ID:</strong> {{ $orderId }}</li>
        <li><strong>Freelancer's Name:</strong> {{ $freelancerName }}</li>
    </ul>

    <p>Our freelancer has acknowledged the requirements of your project and is committed to delivering high-quality work within the specified timeframe. We encourage you to maintain open communication with the freelancer throughout the project to ensure that your expectations are met.</p>

    <p>If you have any questions or additional information to provide, please feel free to reach out to us. We are here to support you every step of the way.</p>

    <p>Thank you for choosing our platform for your project needs. We look forward to delivering excellent results for you.</p>

    <p>Best regards, Admin</p>
</html>
