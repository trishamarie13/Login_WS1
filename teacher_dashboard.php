<?php

session_start();

include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "Teacher") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT
            users.firstname,
            users.lastname,
            users.email,
            teachers.employee_number,
            teachers.department,
            teachers.position
        FROM users
        INNER JOIN teachers
            ON users.id = teachers.user_id
        WHERE users.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $teacher = $result->fetch_assoc();
} else {
    echo "Teacher information not found.";
    exit();
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Teacher Dashboard</title>

    <link rel="stylesheet" href="teacher_dashboard.css?v=5">

</head>

<body>

    <div class="sidebar">

        <div class="sidebar-logo">

            <img src="images/logo.png" alt="Logo">

            <h2>Teacher Portal</h2>

        </div>


        <div class="sidebar-profile">

            <div class="profile-circle">
                <?php echo strtoupper(substr($teacher["firstname"], 0, 1)); ?>
            </div>

            <div class="profile-details">

                <h3>
                    <?php
                    echo htmlspecialchars(
                        $teacher["firstname"] . " " . $teacher["lastname"]
                    );
                    ?>
                </h3>

                <p>Teacher</p>

            </div>

        </div>


        <div class="sidebar-menu">

            <a href="teacher_dashboard.php" class="sidebar-item active">

                <span></span>

                <span>Dashboard</span>

            </a>


            <a href="#information" class="sidebar-item information-item">

                <span></span>

                <span>My Information</span>

            </a>

        </div>


        <div class="sidebar-bottom">

            <a href="logout.php" class="sidebar-item logout-item">

                <span></span>

                <span>Logout</span>

            </a>

        </div>

    </div>


    <main class="teacher-content">

        <div class="container">

            <div class="welcome">

                <h1>
                    Welcome,
                    <?php echo htmlspecialchars($teacher["firstname"]); ?>!
                </h1>

                <p>Welcome to your teacher dashboard.</p>

            </div>


            <div class="card" id="information">

                <h2>My Information</h2>


                <div class="info">

                    <strong>Full Name:</strong>

                    <span>
                        <?php
                        echo htmlspecialchars(
                            $teacher["firstname"] . " " . $teacher["lastname"]
                        );
                        ?>
                    </span>

                </div>


                <div class="info">

                    <strong>Email:</strong>

                    <span>
                        <?php echo htmlspecialchars($teacher["email"]); ?>
                    </span>

                </div>


                <div class="info">

                    <strong>Employee Number:</strong>

                    <span>
                        <?php echo htmlspecialchars($teacher["employee_number"]); ?>
                    </span>

                </div>


                <div class="info">

                    <strong>Department:</strong>

                    <span>
                        <?php echo htmlspecialchars($teacher["department"]); ?>
                    </span>

                </div>


                <div class="info">

                    <strong>Position:</strong>

                    <span>
                        <?php echo htmlspecialchars($teacher["position"]); ?>
                    </span>

                </div>

            </div>

        </div>

    </main>

</body>

</html>