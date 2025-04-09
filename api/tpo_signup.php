<?php
include 'db.php';

// Define TPO credentials
$username = 'tpo';
$password = 'tpo123';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
$role = 'tpo';

try {
    // Insert TPO into the tpo table (only if it doesn't exist)
    $stmt = $pdo->prepare("INSERT INTO tpo (username, password, role) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE username=username");
    $stmt->execute([$username, $hashedPassword, $role]);

    echo json_encode(["status" => "success", "message" => "TPO account set up successfully"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
