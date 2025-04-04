<?php
session_start();
$host = "localhost";
$db_user = "root"; // Change to your MySQL username
$db_pass = "";     // Change to your MySQL password
$db_name = "greenthumb_db";

$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
