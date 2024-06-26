<?php
session_start();
header('Content-Type: application/json');

// Function to handle errors and convert them to JSON response
function handleError($errno, $errstr, $errfile, $errline) {
    echo json_encode(['error' => "$errstr in $errfile on line $errline"]);
    exit();
}

// Set custom error handler
set_error_handler('handleError');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    include "db.inc.php";

    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
//     $servername = "sql306.infinityfree.com";
// $username_db = "if0_35470851";
// $password_db = "FvBSMuYcBuCSbc";
// $dbname = "if0_35470851_all_data";

    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Invalid JSON input']);
        exit();
    }

    // Get the parameters from the input
    $startDate = $input['startDate'] ?? null;
    $endDate = $input['endDate'] ?? null;
    $subjectCode = $input['subjectCode'] ?? null;
    $section = $input['section'] ?? null;
    $userName = $_SESSION['username'] ?? null;  
    if (!$startDate || !$endDate || !$subjectCode || !$section || !$userName) {
        echo json_encode(['error' => 'Missing required parameters']);
        exit();
    }

    // Create a new PDO instance
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL query
        $stmt = $pdo->prepare("
            SELECT 
                date, 
                time_Slot,
                Student_Roll, 
                Student_Name, 
                Status, 
                Attendance_Count

            FROM 
                studentattendance 
            WHERE 
                Subject_Code = :subjectCode AND 
                Section = :section AND 
                date BETWEEN :startDate AND :endDate AND 
                User_Name = :userName
        ");

        // Bind the parameters
        $stmt->bindParam(':subjectCode', $subjectCode);
        $stmt->bindParam(':section', $section);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->bindParam(':userName', $userName);

        // Execute the query
        $stmt->execute();

        // Fetch the results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the results as a JSON response
        echo json_encode($results);

    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
