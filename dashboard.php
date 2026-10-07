<?php

session_start();

include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$student_sql = "SELECT
                    id,
                    firstname,
                    lastname,
                    email,
                    username
                FROM users
                WHERE role = 'Student'
                ORDER BY id DESC";

$student_result = $conn->query($student_sql);

$teacher_sql = "SELECT
                    id,
                    firstname,
                    lastname,
                    email,
                    username
                FROM users
                WHERE role = 'Teacher'
                ORDER BY id DESC";

$teacher_result = $conn->query($teacher_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Dashboard</title>

    <link rel="stylesheet" href="dashstyle.css?v=4">

</head>

<body>

<?php if ($_SESSION["role"] == "Super_admin"): ?>

    <div class="admin-layout">

        <aside class="sidebar">

            <div class="sidebar-logo">

                <img
                    src="images/logo.png"
                    alt="Logo">

                <h2>School System</h2>

            </div>


            <div class="sidebar-profile">

                <div class="profile-circle">

                    <?php
                    echo strtoupper(
                        substr($_SESSION["firstname"], 0, 1)
                    );
                    ?>

                </div>

                <div>

                    <h3>
                        <?php
                        echo htmlspecialchars(
                            $_SESSION["firstname"] . " " .
                            $_SESSION["lastname"]
                        );
                        ?>
                    </h3>

                    <p>Admin</p>

                </div>

            </div>


            <nav class="sidebar-menu">

                <a
                    href="dashboard.php"
                    class="sidebar-item active">

                    <span></span>
                    <span>Dashboard</span>

                </a>


                <a
                    href="#students"
                    class="sidebar-item">

                    <span></span>
                    <span>Students</span>

                </a>


                <a
                    href="#teachers"
                    class="sidebar-item">

                    <span></span>
                    <span>Teachers</span>

                </a>

            </nav>


            <div class="sidebar-bottom">

                <a
                    href="logout.php"
                    class="sidebar-item logout-item">

                    <span></span>
                    <span>Logout</span>

                </a>

            </div>

        </aside>


        <div class="dashboard-content">

<?php endif; ?>


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


        <div class="accounts-container">

            <div
                class="accounts-box"
                id="students">

                <h2>Registered Students</h2>

                <p class="description">
                    Students who have registered their accounts.
                </p>

                <div class="table-container">

                    <table class="accounts-table">

                        <thead>

                            <tr>

                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if ($student_result->num_rows > 0): ?>

                                <?php while ($student = $student_result->fetch_assoc()): ?>

                                    <tr>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $student["firstname"] . " " .
                                                $student["lastname"]
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $student["email"]
                                            );
                                            ?>
                                        </td>

                                        <td class="username">
                                            <?php
                                            echo htmlspecialchars(
                                                $student["username"]
                                            );
                                            ?>
                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="3"
                                        class="no-accounts">

                                        No registered students found.

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <div
                class="accounts-box"
                id="teachers">

                <h2>Registered Teachers</h2>

                <p class="description">
                    Teachers who have registered their accounts.
                </p>

                <div class="table-container">

                    <table class="accounts-table">

                        <thead>

                            <tr>

                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if ($teacher_result->num_rows > 0): ?>

                                <?php while ($teacher = $teacher_result->fetch_assoc()): ?>

                                    <tr>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $teacher["firstname"] . " " .
                                                $teacher["lastname"]
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $teacher["email"]
                                            );
                                            ?>
                                        </td>

                                        <td class="username">
                                            <?php
                                            echo htmlspecialchars(
                                                $teacher["username"]
                                            );
                                            ?>
                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="3"
                                        class="no-accounts">

                                        No registered teachers found.

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


    <?php elseif ($_SESSION["role"] == "Student"): ?>

        <h1>Welcome, Student!</h1>

        <p>
            <?php
            echo htmlspecialchars(
                $_SESSION["firstname"] . " " . $_SESSION["lastname"]
            );
            ?>
        </p>

        <img
            src="images/logo.png"
            alt="Logo">

        <div class="cards">

            <div class="card">

                <h2>Student Dashboard</h2>

                <p>
                    Welcome to your student dashboard.
                </p>

            </div>

        </div>


    <?php elseif ($_SESSION["role"] == "Teacher"): ?>

        <h1>Welcome, Teacher!</h1>

        <p>
            <?php
            echo htmlspecialchars(
                $_SESSION["firstname"] . " " . $_SESSION["lastname"]
            );
            ?>
        </p>

        <img
            src="images/logo.png"
            alt="Logo">

        <div class="cards">

            <div class="card">

                <h2>Teacher Dashboard</h2>

                <p>
                    Welcome to your teacher dashboard.
                </p>

            </div>

        </div>


    <?php endif; ?>

</div>


<?php if ($_SESSION["role"] == "Super_admin"): ?>

        </div>

    </div>

<?php endif; ?>

</body>

</html>