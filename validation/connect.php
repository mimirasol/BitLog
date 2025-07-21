<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "bitlog";
$port = "3306";

$conn = mysqli_connect($servername, $username, $password, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>