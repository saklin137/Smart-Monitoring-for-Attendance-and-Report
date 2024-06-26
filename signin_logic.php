<?php
session_start(); 

include "db.inc.php";

$con = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);


if ($con->connect_error) {
    die("Connection error " . $con->connect_error);
}

if (isset($_POST['user']) && isset($_POST['pass'])) {
    $USER_NAME = $_POST['user'];
    $PASSWORD = $_POST['pass'];
    $hashedUserInputPassword = md5($PASSWORD);


    $sql = "SELECT * FROM re_table WHERE `username`=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $USER_NAME);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {

        $sql = "SELECT * FROM re_table WHERE `email`=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $USER_NAME);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();
        $hashedPasswordDB = $row['password']; 


        if ($hashedUserInputPassword === $hashedPasswordDB) {

            $_SESSION['username'] = $row['username'];

 
            header("Location: get_details.php");
            exit; 
        } else {
     
            echo '<script>
            window.location.href = "login.html";
            alert("Login Failed: Invalid username or password");
            </script>';
        }
    } else {

        echo '<script>
        window.location.href = "index.html";
        alert("Login Failed: Invalid username or email");
        </script>';
    }
} else {

    echo '<script>
    window.location.href = "index.html";
    alert("Form fields not set");
    </script>';
}

$con->close();
?>
