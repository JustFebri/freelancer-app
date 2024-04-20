<!DOCTYPE html>
<html>

<head>
    <title>Service Rejection Notification</title>
</head>

<body>
    <p>Dear {{ $freelancerName }},</p>

    <p>We regret to inform you that your service on our platform has been rejected. After a careful review, we found that the service does not meet our criteria for listing.</p>

    <p><strong>Service Title:</strong> {{ $serviceTitle }}</p>
    <p><strong>Category:</strong> {{ $category }}</p>
    <p><strong>Sub-Category:</strong> {{ $subCategory }}</p>

    <p><strong>Reason for Rejection:</strong> {{ $rejectionReason }}</p>

    <p>We appreciate your interest in joining our freelancer community. If you would like more details about the rejection or if you have made improvements to your service that you'd like us to reevaluate, please feel free to contact our support team at [Support Email].</p>

    <p>Thank you for considering our platform, and we hope you will continue to be a part of our community in the future.</p>

    <p>Best regards,</p>
    <p>Admin</p>
</body>

</html>
