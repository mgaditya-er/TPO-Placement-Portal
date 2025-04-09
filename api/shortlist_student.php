<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = 'localhost';
$dbname = 'training_placement';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Base query with subquery filter
    $query = "SELECT * FROM student_profile 
              WHERE enrollment_no IN (SELECT enrollment_no FROM job_applications)";

    // Add job_id filter to subquery if provided
    if (isset($_GET['job_id']) && !empty($_GET['job_id'])) {
        $query .= " AND job_id = :job_id";
    }
    // Optional filters
    if (isset($_GET['course'])) {
        $course = $_GET['course'];
        $query .= " AND course = :course";
    }

    if (isset($_GET['year'])) {
        $year = $_GET['year'];
        $query .= " AND year = :year";
    }

    if (isset($_GET['percentage'])) {
        $percentage = $_GET['percentage'];
        $query .= " AND percentage >= :percentage";
    }

    $stmt = $pdo->prepare($query);
 // Bind parameters
 if (isset($_GET['job_id']) && !empty($_GET['job_id'])) {
    $stmt->bindParam(':job_id', $_GET['job_id']);
}
    if (isset($course)) $stmt->bindParam(':course', $course);
    if (isset($year)) $stmt->bindParam(':year', $year);
    if (isset($percentage)) $stmt->bindParam(':percentage', $percentage);

    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($students ?: []);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
