<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>

    <link rel="stylesheet" href="dashstyle.css">

</head>

<body>

<div class="dashboard">

    <?php if ($_SESSION["role"] == "Super_admin"): ?>

        <h1>Admin</h1>

        <p>
            Welcome, Admin
            <?php
            echo htmlspecialchars(
                $_SESSION["firstname"] . " " . $_SESSION["lastname"]
            );
            ?>!
        </p>

        <img
            src="images/logo.png"
            alt="Logo">


        <div class="cards">


            <!-- MANAGE STUDENTS -->

            <div class="card">

                <h2>Manage Students</h2>

                <p>
                    View, edit, and manage student accounts.
                </p>

                <a href="students.php">
                    Manage Students
                </a>

            </div>


            <!-- MANAGE TEACHERS -->

            <div class="card">

                <h2>Manage Teachers</h2>

                <p>
                    View, edit, and manage teacher accounts.
                </p>

                <a href="teachers.php">
                    Manage Teachers
                </a>

            </div>


        </div>


    <?php else: ?>


        <h1>Welcome!</h1>

        <p>
            You have successfully logged in.
        </p>

        <img
            src="images/logo.png"
            alt="Logo">

        <p>
            <?php
            echo htmlspecialchars(
                $_SESSION["firstname"] . " " . $_SESSION["lastname"]
            );
            ?>
        </p>

        <div class="cards">

            <!-- You can add dashboard cards here -->

        </div>


    <?php endif; ?>


    <a
        href="logout.php"
        class="logout-btn">
        Logout
    </a>

</div>

</body>

</html>