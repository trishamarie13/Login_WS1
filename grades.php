<?php

session_start();

include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (
    $_SESSION["role"] !== "Super_admin" &&
    $_SESSION["role"] !== "Teacher" &&
    $_SESSION["role"] !== "Student"
) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["role"] === "Student") {

    $student_id = $_SESSION["user_id"];

    $sql = "SELECT
                grades.id,
                grades.subject,
                grades.grade,
                grades.semester,
                grades.school_year,
                users.firstname,
                users.lastname
            FROM grades
            INNER JOIN users
                ON grades.student_id = users.id
            WHERE grades.student_id = ?
            ORDER BY grades.id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql = "SELECT
                grades.id,
                grades.subject,
                grades.grade,
                grades.semester,
                grades.school_year,
                users.firstname,
                users.lastname
            FROM grades
            INNER JOIN users
                ON grades.student_id = users.id
            WHERE users.role = 'Student'
            ORDER BY users.lastname ASC, grades.subject ASC";

    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Grades</title>

    <link rel="stylesheet" href="dashstyle.css">

</head>

<body>

<div class="dashboard">

    <h1>Grades</h1>

    <?php if ($_SESSION["role"] === "Student"): ?>

        <p>
            Your grades
        </p>

    <?php else: ?>

        <p>
            Student grades
        </p>

        <a href="teacher_dashboard.php" class="logout-btn">
            Add Grade
        </a>

    <?php endif; ?>


    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <?php if ($_SESSION["role"] !== "Student"): ?>

                        <th>Student</th>

                    <?php endif; ?>

                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Semester</th>
                    <th>School Year</th>

                    <?php if ($_SESSION["role"] !== "Student"): ?>

                        <th>Action</th>

                    <?php endif; ?>

                </tr>

            </thead>

            <tbody>

                <?php if ($result && $result->num_rows > 0): ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <?php if ($_SESSION["role"] !== "Student"): ?>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $row["firstname"] . " " . $row["lastname"]
                                    );
                                    ?>
                                </td>

                            <?php endif; ?>

                            <td>
                                <?php
                                echo htmlspecialchars($row["subject"]);
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars($row["grade"]);
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars($row["semester"]);
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars($row["school_year"]);
                                ?>
                            </td>

                            <?php if ($_SESSION["role"] !== "Student"): ?>

                                <td>

                                    <a href="teacher_dashboard.php?id=<?php echo $row["id"]; ?>">
                                        Edit
                                    </a>

                                </td>

                            <?php endif; ?>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="<?php echo $_SESSION["role"] === "Student" ? 4 : 6; ?>">

                            No grades found.

                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>


    <br>

    <a href="dashboard.php">
        ← Back to Dashboard
    </a>

</div>

</body>

</html>