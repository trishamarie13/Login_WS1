<?php

include "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];

    if ($newPassword !== $confirmPassword) {

        $error = "Passwords do not match.";

    } else {

        $checkSql = "SELECT id FROM users WHERE username = ?";

        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();

        $result = $checkStmt->get_result();

        if ($result->num_rows == 0) {

            $error = "Username not found.";

        } else {

            $hashedPassword = password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );

            $sql = "UPDATE users SET password = ? WHERE username = ?";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "ss",
                $hashedPassword,
                $username
            );

            if ($stmt->execute()) {

                $message = "Password successfully changed!";

            } else {

                $error = "Something went wrong. Please try again.";

            }

            $stmt->close();
        }

        $checkStmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Reset Password</title>

    <link rel="stylesheet" href="stylepass.css">

</head>

<body>

<div class="container">

    <div class="password-box">

        <h1>Reset Password</h1>

        <p>Create a new password for your account.</p>


        <?php if ($message != ""): ?>

            <p style="color: green;">
                <?php echo $message; ?>
            </p>

            <p>
                <a href="login.php">
                    Go to Login
                </a>
            </p>

        <?php endif; ?>


        <?php if ($error != ""): ?>

            <p style="color: red;">
                <?php echo $error; ?>
            </p>

        <?php endif; ?>


        <?php if ($message == ""): ?>

        <form
            id="resetForm"
            action="password.php"
            method="POST">

            <div class="input-box">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
                    required>

            </div>


            <div class="input-box">

                <label for="newPassword">
                    New Password
                </label>

                <input
                    type="password"
                    id="newPassword"
                    name="newPassword"
                    placeholder="Enter new password"
                    required
                    minlength="8">

            </div>


            <div class="input-box">

                <label for="confirmPassword">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirmPassword"
                    name="confirmPassword"
                    placeholder="Confirm your new password"
                    required
                    minlength="8">

            </div>


            <button type="submit">
                Reset Password
            </button>

        </form>

        <?php endif; ?>


        <div class="links">

            <a href="login.php">
                ← Back to Login
            </a>

        </div>

    </div>

</div>

</body>

</html>