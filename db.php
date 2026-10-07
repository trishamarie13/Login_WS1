<?php

$host = "sql200.infinityfree.com";
$username = "if0_42933426";
$password = "trishaMarie123";
$database = "if0_42933426_login_system";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>