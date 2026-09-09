<?php

session_start();

include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "Student") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT
            users.firstname,
            users.lastname,
            users.email,
            students.student_number,
            students.course,
            students.year_level,
            students.section
        FROM users
        INNER JOIN students
            ON users.id = students.user_id
        WHERE users.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $student = $result->fetch_assoc();
} else {
    echo "Student information not found.";
    exit();
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }

        .navbar {
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .logout {
            color: white;
            text-decoration: none;
            background: #e74c3c;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .container {
            padding: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .card h2 {
            margin-top: 0;
        }

        .info {
            margin: 12px 0;
        }

        .info strong {
            display: inline-block;
            width: 150px;
        }
    </style>
</head>

<body>

<div class="navbar">

    <h2>Student Dashboard</h2>

    <a href="logout.php" class="logout">Logout</a>

</div>

<div class="container">

    <h1>
        Welcome, <?php echo htmlspecialchars($student["firstname"]); ?>!
    </h1>

    <div class="card">

        <h2>My Information</h2>

        <div class="info">
            <strong>Full Name:</strong>
            <?php
                echo htmlspecialchars(
                    $student["firstname"] . " " . $student["lastname"]
                );
            ?>
        </div>

        <div class="info">
            <strong>Email:</strong>
            <?php echo htmlspecialchars($student["email"]); ?>
        </div>

        <div class="info">
            <strong>Student Number:</strong>
            <?php echo htmlspecialchars($student["student_number"]); ?>
        </div>

        <div class="info">
            <strong>Course:</strong>
            <?php echo htmlspecialchars($student["course"]); ?>
        </div>

        <div class="info">
            <strong>Year Level:</strong>
            <?php echo htmlspecialchars($student["year_level"]); ?>
        </div>

        <div class="info">
            <strong>Section:</strong>
            <?php echo htmlspecialchars($student["section"]); ?>
        </div>

    </div>

</div>

</body>
</html>