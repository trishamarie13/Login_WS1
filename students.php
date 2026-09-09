<?php

session_start();

include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "Super_admin") {
    header("Location: login.php");
    exit();
}

$sql = "SELECT
            users.id,
            users.firstname,
            users.lastname,
            users.email,
            users.username,
            students.student_number,
            students.course,
            students.year_level,
            students.section
        FROM users
        INNER JOIN students
            ON users.id = students.user_id
        WHERE users.role = 'Student'
        ORDER BY users.id DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Manage Students</title>

    <link rel="stylesheet" href="manage_users.css">

</head>

<body>

<div class="container">

    <h1>Manage Students</h1>

    <p>
        View and manage student accounts.
    </p>

    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>Student Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Section</th>
                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($student = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["student_number"]
                                );
                                ?>
                            </td>

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

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["username"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["course"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["year_level"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $student["section"]
                                );
                                ?>
                            </td>

                            <td>

                                <a
                                    href="edit_student.php?id=<?php echo $student["id"]; ?>"
                                    class="edit-btn">
                                    Edit
                                </a>

                                <a
                                    href="change_student_password.php?id=<?php echo $student["id"]; ?>"
                                    class="password-btn">
                                    Password
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="8">
                            No students found.
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

    <a href="dashboard.php" class="back-btn">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>