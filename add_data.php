<?php
session_start(); // Start session to access session variables

include "db.inc.php";

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to create table if not exists
function createTable($conn, $tableName) {
    $sql = "CREATE TABLE IF NOT EXISTS $tableName (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        userName VARCHAR(50) NOT NULL,
        day VARCHAR(20) NOT NULL,
        time_slot_1 VARCHAR(50),
        time_slot_2 VARCHAR(50),
        time_slot_3 VARCHAR(50),
        time_slot_4 VARCHAR(50),
        time_slot_5 VARCHAR(50),
        time_slot_6 VARCHAR(50)
    )";

    if ($conn->query($sql) === TRUE) {
        return "Table $tableName created successfully.";
    } else {
        return "Error creating table: " . $conn->error;
    }
}

// Function to insert or update data
function insertOrUpdateData($conn, $tableName, $data) {
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        return "Error: Username not set in session.";
    }

    $userName = $_SESSION['username'];
    $day = $data['day'];

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT * FROM $tableName WHERE userName=? AND day=?");
    $stmt->bind_param("ss", $userName, $day);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE $tableName SET
            time_slot_1=?, time_slot_2=?, time_slot_3=?,
            time_slot_4=?, time_slot_5=?, time_slot_6=?
            WHERE userName=? AND day=?");
        $stmt->bind_param("ssssssss", $data['timeSlot1'], $data['timeSlot2'], $data['timeSlot3'],
            $data['timeSlot4'], $data['timeSlot5'], $data['timeSlot6'], $userName, $day);
        if ($stmt->execute()) {
            return "Record for day $day updated successfully.";
        } else {
            return "Error updating record: " . $stmt->error;
        }
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO $tableName (userName, day, time_slot_1, time_slot_2, time_slot_3,
            time_slot_4, time_slot_5, time_slot_6) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $userName, $day, $data['timeSlot1'], $data['timeSlot2'], $data['timeSlot3'],
            $data['timeSlot4'], $data['timeSlot5'], $data['timeSlot6']);
        if ($stmt->execute()) {
            return "New record inserted successfully.";
        } else {
            return "Error inserting record: " . $stmt->error;
        }
    }
}

// Check if the table exists
$tableName = "Faculty_TimeTable";
if (!tableExists($conn, $tableName)) {
    echo json_encode(["error" => createTable($conn, $tableName)]);
    exit;
}

// Get JSON data from request body
$data = json_decode(file_get_contents('php://input'), true);

// Insert or update data
$response = insertOrUpdateData($conn, $tableName, $data);

// Send response
echo json_encode(["message" => $response]);

// Close database connection
$conn->close();

// Function to check if table exists
function tableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}
?>
