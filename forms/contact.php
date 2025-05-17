<?php
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name    = strip_tags(trim($_POST["name"] ?? ''));
    $email   = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"] ?? ''));
    $message = trim($_POST["message"] ?? '');

    if (empty($name) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields correctly."]);
        exit;
    }

    // ✅ Corrected database name
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=painters.co", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
        exit;
    }

    // Email setup
    $to      = "raul.garcia.1627@gmail.com";
    $headers = "From: $name <$email>\r\n";
    $email_body = "You have received a new message from the contact form:\n\n"
                . "Name: $name\n"
                . "Email: $email\n"
                . "Subject: $subject\n\n"
                . "Message:\n$message\n";

    if (mail($to, $subject, $email_body, $headers)) {
        echo json_encode(["status" => "success", "message" => "Message sent and saved successfully."]);
    } else {
        echo json_encode(["status" => "warning", "message" => "Message saved, but email could not be sent."]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
