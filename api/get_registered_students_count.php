<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Include the database connection
include('db.php');

try {
    // Query to count total registered students
    $query = "SELECT COUNT(*) AS total_registered FROM students";
    $stmt = $pdo->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the count as JSON
    echo json_encode([
        'status' => 'success',
        'total_registered' => (int)$result['total_registered']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
