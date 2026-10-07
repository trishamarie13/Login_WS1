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

    <link rel="stylesheet" href="student_dashboard.css?v=4">

</head>

<body>

    <aside class="sidebar">

        <div class="sidebar-logo">

            <img src="images/logo.png" alt="Logo">

            <h2>Student Portal</h2>

        </div>


        <div class="sidebar-profile">

            <div class="profile-circle">

                <?php echo strtoupper(substr($student["firstname"], 0, 1)); ?>

            </div>

            <div>

                <h3>
                    <?php
                    echo htmlspecialchars(
                        $student["firstname"] . " " . $student["lastname"]
                    );
                    ?>
                </h3>

                <p>Student</p>

            </div>

        </div>


        <nav class="sidebar-menu">

            <a href="student_dashboard.php" class="sidebar-item active">

                <span></span>

                <span>Dashboard</span>

            </a>


            <a href="#information" class="sidebar-item">

                <span></span>

                <span>My Information</span>

            </a>

        </nav>


        <div class="sidebar-bottom">

            <a href="logout.php" class="sidebar-item logout-item">

                <span></span>

                <span>Logout</span>

            </a>

        </div>

    </aside>


    <main class="dashboard-content">

        <div class="container">

            <div class="welcome">

                <h1>
                    Welcome,
                    <?php echo htmlspecialchars($student["firstname"]); ?>!
                </h1>

                <p>Welcome to your student dashboard.</p>

            </div>


            <div class="card" id="information">

                <h2>My Information</h2>


                <div class="info">

                    <strong>Full Name:</strong>

                    <span>
                        <?php
                        echo htmlspecialchars(
                            $student["firstname"] . " " . $student["lastname"]
                        );
                        ?>
                    </span>

                </div>


                <div class="info">

                    <strong>Email:</strong>

                    <span>
                        <?php echo htmlspecialchars($student["email"]); ?>
                    </span>

                </div>


                <div class="info">

                    <strong>Student Number:</strong>

                    <span>
                        <?php echo htmlspecialchars($student["student_number"]); ?>
                    </span>

                </div>


                <div class="info">

                    <strong>Course:</strong>

                    <span>
                        <?php echo htmlspecialchars($student["course"]); ?>
                    </span>

                </div>


                <div class="info">

                    <strong>Year Level:</strong>

                    <span>
                        <?php echo htmlspecialchars($student["year_level"]); ?>
                    </span>

                </div>


                <div class="info">

                    <strong>Section:</strong>

                    <span>
                        <?php echo htmlspecialchars($student["section"]); ?>
                    </span>

                </div>

            </div>

        </div>

    </main>

</body>

</html>