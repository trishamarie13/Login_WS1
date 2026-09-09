<?php

session_start();

include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "Super_admin") {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: teachers.php");
    exit();
}

$user_id = $_GET["id"];

$message = "";
$error = "";


/* =========================
   CHANGE PASSWORD
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];


    if ($password !== $confirmPassword) {

        $error = "Passwords do not match.";

    } elseif (strlen($password) < 8) {

        $error = "Password must be at least 8 characters.";

    } else {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );


        $sql = "UPDATE users
                SET password = ?
                WHERE id = ?
                AND role = 'Teacher'";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "si",
            $hashedPassword,
            $user_id
        );


        if ($stmt->execute()) {

            $message = "Teacher password changed successfully.";

        } else {

            $error = "Password could not be changed.";

        }

        $stmt->close();

    }

}


/* =========================
   GET TEACHER NAME
========================= */

$sql = "SELECT firstname, lastname
        FROM users
        WHERE id = ?
        AND role = 'Teacher'";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $teacher = $result->fetch_assoc();

} else {

    header("Location: teachers.php");
    exit();

}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Change Teacher Password</title>

    <link rel="stylesheet" href="change_users.css">

</head>

<body>

<div class="container">

    <h1>Change Teacher Password</h1>

    <p>
        Teacher:
        <?php
        echo htmlspecialchars(
            $teacher["firstname"] . " " . $teacher["lastname"]
        );
        ?>
    </p>


    <?php if ($message != ""): ?>

        <p style="color: green;">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <?php if ($error != ""): ?>

        <p style="color: red;">
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php endif; ?>


    <form method="POST">

        <label>New Password</label>

        <input
            type="password"
            name="password"
            minlength="8"
            required>


        <label>Confirm New Password</label>

        <input
            type="password"
            name="confirmPassword"
            minlength="8"
            required>


        <button type="submit">
            Change Password
        </button>

    </form>


    <br>

    <a href="teachers.php">
        ← Back to Teachers
    </a>

</div>

</body>

</html>