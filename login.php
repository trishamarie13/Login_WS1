
<?php

session_start();

include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["firstname"] = $user["firstname"];
            $_SESSION["lastname"] = $user["lastname"];
            $_SESSION["role"] = $user["role"];


            if ($user["role"] === "Super_admin") {

                header("Location: dashboard.php");
                exit();

            } elseif ($user["role"] === "Teacher") {

                header("Location: teacher_dashboard.php");
                exit();

            } elseif ($user["role"] === "Student") {

                header("Location: student_dashboard.php");
                exit();

            } else {

                $error = "Invalid account role.";

            }

        } else {

            $error = "Incorrect password.";

        }

    } else {

        $error = "Username not found.";

    }

    $stmt->close();
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>User Login</title>

    <link rel="stylesheet" href="loginstyle.css">

</head>

<body>

<div class="container">

    <div class="login-box">

        <h1>Welcome Back</h1>

        <p>Please login to your account</p>


        <?php if ($error != ""): ?>

            <p style="color: red;">

                <?php echo htmlspecialchars($error); ?>

            </p>

        <?php endif; ?>


        <form
            action="login.php"
            method="POST">


            <div class="input-box">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter username"
                    required>

            </div>


            <div class="input-box">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    required>

            </div>


            <button type="submit">
                Login
            </button>


        </form>


        <div class="links">

            <a href="register.php">
                Create Account
            </a>

            <a href="password.php">
                Forgot Password?
            </a>

        </div>


    </div>

</div>

</body>

</html>
