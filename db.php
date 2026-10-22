<?php

$host = getenv("DB_HOST");
$username = getenv("DB_USERNAME");
$password = getenv("DB_PASSWORD");
$database = getenv("DB_DATABASE");

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    exit("Database connection failed. Check the server configuration.");
}

$conn->set_charset("utf8mb4");
