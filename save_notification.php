<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

include 'db.php';

// Check if table exists, if not, create it
$conn->query("CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['message']) && !empty(trim($data['message']))) {
    $message = trim($data['message']);
    
    $stmt = $conn->prepare("INSERT INTO notifications (message) VALUES (?)");
    if ($stmt) {
        $stmt->bind_param("s", $message);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error"]);
        }
        $stmt->close();
    }
} else {
    echo json_encode(["status" => "error", "message" => "No message provided"]);
}
?>
