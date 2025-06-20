<?php
// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get values and sanitize
    $name = htmlspecialchars(strip_tags(trim($_POST["name"])));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(strip_tags(trim($_POST["subject"])));
    $message = htmlspecialchars(strip_tags(trim($_POST["message"])));

    // Check for empty fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        http_response_code(400);
        echo "All fields are required.";
        exit;
    }

    // Set your email address here
    $to = "your_email@example.com"; // Change this to your email

    // Email content
    $email_subject = "New message from: $name - $subject";
    $email_body = "You received a new message:\n\n";
    $email_body .= "Name: $name\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Subject: $subject\n";
    $email_body .= "Message:\n$message\n";

    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";

    // Try to send email
    if (mail($to, $email_subject, $email_body, $headers)) {
        http_response_code(200);
        echo "Message sent successfully.";
    } else {
        http_response_code(500);
        echo "Failed to send message.";
    }
} else {
    http_response_code(403);
    echo "Access forbidden.";
}
?>
