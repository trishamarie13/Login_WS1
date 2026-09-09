<?php

session_start();

include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "Super_admin") {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: students.php");
    exit();
}

$user_id = $_GET["id"];

$message = "";
$error = "";


/* =========================
   UPDATE STUDENT
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $username = $_POST["username"];

    $student_number = $_POST["student_number"];
    $course = $_POST["course"];
    $year_level = $_POST["year_level"];
    $section = $_POST["section"];


    $sql = "UPDATE users
            SET firstname = ?,
                lastname = ?,
                email = ?,
                username = ?
            WHERE id = ?
            AND role = 'Student'";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssi",
        $firstname,
        $lastname,
        $email,
        $username,
        $user_id
    );

    if ($stmt->execute()) {

        $studentSql = "UPDATE students
                       SET student_number = ?,
                           course = ?,
                           year_level = ?,
                           section = ?
                       WHERE user_id = ?";

        $studentStmt = $conn->prepare($studentSql);

        $studentStmt->bind_param(
            "ssssi",
            $student_number,
            $course,
            $year_level,
            $section,
            $user_id
        );

        if ($studentStmt->execute()) {

            $message = "Student information updated successfully.";

        } else {

            $error = "Student information could not be updated.";

        }

        $studentStmt->close();

    } else {

        $error = "Account information could not be updated.";

    }

    $stmt->close();
}


/* =========================
   GET STUDENT INFORMATION
========================= */

$sql = "SELECT
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
        WHERE users.id = ?
        AND users.role = 'Student'";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $student = $result->fetch_assoc();

} else {

    header("Location: students.php");
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

    <title>Edit Student</title>

    <link rel="stylesheet" href="teacher_users.css">

</head>

<body>

<div class="container">

    <h1>Edit Student</h1>

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


        <label>First Name</label>

        <input
            type="text"
            name="firstname"
            value="<?php echo htmlspecialchars($student["firstname"]); ?>"
            required>


        <label>Last Name</label>

        <input
            type="text"
            name="lastname"
            value="<?php echo htmlspecialchars($student["lastname"]); ?>"
            required>


        <label>Email</label>

        <input
            type="email"
            name="email"
            value="<?php echo htmlspecialchars($student["email"]); ?>"
            required>


        <label>Username</label>

        <input
            type="text"
            name="username"
            value="<?php echo htmlspecialchars($student["username"]); ?>"
            required>


        <label>Student Number</label>

        <input
            type="text"
            name="student_number"
            value="<?php echo htmlspecialchars($student["student_number"]); ?>"
            required>


        <label>Course</label>

        <input
            type="text"
            name="course"
            value="<?php echo htmlspecialchars($student["course"]); ?>"
            required>


        <label>Year Level</label>

        <select name="year_level" required>

            <option value="1st Year"
                <?php if ($student["year_level"] == "1st Year") echo "selected"; ?>>
                1st Year
            </option>

            <option value="2nd Year"
                <?php if ($student["year_level"] == "2nd Year") echo "selected"; ?>>
                2nd Year
            </option>

            <option value="3rd Year"
                <?php if ($student["year_level"] == "3rd Year") echo "selected"; ?>>
                3rd Year
            </option>

            <option value="4th Year"
                <?php if ($student["year_level"] == "4th Year") echo "selected"; ?>>
                4th Year
            </option>

        </select>


        <label>Section</label>

        <input
            type="text"
            name="section"
            value="<?php echo htmlspecialchars($student["section"]); ?>"
            required>


        <button type="submit">
            Save Changes
        </button>

    </form>


    <br>

    <a href="students.php">
        ← Back to Students
    </a>

</div>

</body>

</html>