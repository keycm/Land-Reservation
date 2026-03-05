<?php
// config.php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "eco_land";

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Helper: Check if logged in
function checkLogin(){
    if(!isset($_SESSION['user_id'])){
        header("Location: index.php");
        exit();
    }
}

// Helper: Check if Admin
function checkAdmin(){
    checkLogin();
    if($_SESSION['role'] !== 'ADMIN'){
        die("ACCESS DENIED");
    }
}
?>