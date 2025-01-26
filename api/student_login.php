<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$enrollment_no = $data['enrollment_no'];
$password = $data['password'];

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE enrollment_no = ? AND role = 'student'");
    $stmt->execute([$enrollment_no]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(["status" => "success", "message" => "Login successful", "user" => $user]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
