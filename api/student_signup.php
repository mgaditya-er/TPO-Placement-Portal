<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data['enrollment_no']) || empty($data['name']) || empty($data['email']) || empty($data['password'])) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

$enrollment_no = $data['enrollment_no'];
$name = $data['name'];
$email = $data['email'];
$password = $data['password'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format"]);
    exit;
}

// Validate password strength (minimum 8 characters)
if (strlen($password) < 8) {
    echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters long"]);
    exit;
}

try {
    // Check if enrollment_no already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE enrollment_no = ?");
    $stmt->execute([$enrollment_no]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(["status" => "error", "message" => "Enrollment No already exists"]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user into the database
    $stmt = $pdo->prepare("INSERT INTO users (enrollment_no, name, email, password, role) VALUES (?, ?, ?, ?, 'student')");
    $stmt->execute([$enrollment_no, $name, $email, $hashedPassword]);

    echo json_encode(["status" => "success", "message" => "Signup successful"]);
} catch (PDOException $e) {
    // Catch any database-related exceptions
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
