<?php
session_start();

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Database connection
    include "db.inc.php";

    $con = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    if ($con->connect_error) {
        die("Connection error " . $con->connect_error);
    }

    // Check if the email exists in the database
    $sql = "SELECT * FROM re_table WHERE `email`=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Email exists, fetch username
        $row = $result->fetch_assoc();
        $username = $row['username'];

        // Store username in session
        $_SESSION['username'] = $username;

        // Redirect to welcome page or any other page
        header("Location: get_details.php");
        exit;
    } else {
        // Email doesn't exist, redirect to registration page with an alert
        echo '<script>
                alert("Email not registered. Redirecting to registration page.");
                window.location.href = "registration.html";
              </script>';
        exit;
    }
} else {
    // Email parameter not provided, redirect to index page with an alert
    echo '<script>
            alert("Email parameter not provided.");
            window.location.href = "login.html";
          </script>';
    exit;
}
?>
