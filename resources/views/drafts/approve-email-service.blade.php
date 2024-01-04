<!DOCTYPE html>
<html>

<head>
    <title>Service Approval Notification</title>
</head>

<body>
    <p>Dear {{ $freelancerName }},</p>

    <p>This is to inform you that the following service has been approved for sale:</p>

    <p><strong>Service Title:</strong> {{ $serviceTitle }}</p>
    <p><strong>Category:</strong> {{ $category }}</p>
    <p><strong>Sub-Category:</strong> {{ $subCategory }}</p>

    <p>You can now list this service on our platform for users to purchase. If you have any questions or need further details, please feel free to contact the freelancer or our support team.</p>

    <p>Thank you for your diligence in reviewing and approving services for our platform.</p>

    <p>Best regards,</p>
    <p>Admin</p>
</body>

</html>
