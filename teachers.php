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
            teachers.employee_number,
            teachers.department,
            teachers.position
        FROM users
        INNER JOIN teachers
            ON users.id = teachers.user_id
        WHERE users.role = 'Teacher'
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

    <title>Manage Teachers</title>

    <link rel="stylesheet" href="manage_users.css">

</head>

<body>

<div class="container">

    <h1>Manage Teachers</h1>

    <p>
        View and manage teacher accounts.
    </p>

    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>Employee Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php while ($teacher = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $teacher["employee_number"]
                                );
                                ?>
                            </td>

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

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $teacher["username"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $teacher["department"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $teacher["position"]
                                );
                                ?>
                            </td>

                            <td>

                                <a
                                    href="edit_teacher.php?id=<?php echo $teacher["id"]; ?>"
                                    class="edit-btn">
                                    Edit
                                </a>

                                <a
                                    href="change_teacher_password.php?id=<?php echo $teacher["id"]; ?>"
                                    class="password-btn">
                                    Password
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="7">
                            No teachers found.
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