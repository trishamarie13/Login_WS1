<?php

session_start();

include "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "Super_admin") {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: teachers.php");
    exit();
}

$user_id = $_GET["id"];

$message = "";
$error = "";


/* =========================
   UPDATE TEACHER
========================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $username = $_POST["username"];

    $employee_number = $_POST["employee_number"];
    $department = $_POST["department"];
    $position = $_POST["position"];


    $sql = "UPDATE users
            SET firstname = ?,
                lastname = ?,
                email = ?,
                username = ?
            WHERE id = ?
            AND role = 'Teacher'";

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

        $teacherSql = "UPDATE teachers
                       SET employee_number = ?,
                           department = ?,
                           position = ?
                       WHERE user_id = ?";

        $teacherStmt = $conn->prepare($teacherSql);

        $teacherStmt->bind_param(
            "sssi",
            $employee_number,
            $department,
            $position,
            $user_id
        );

        if ($teacherStmt->execute()) {

            $message = "Teacher information updated successfully.";

        } else {

            $error = "Teacher information could not be updated.";

        }

        $teacherStmt->close();

    } else {

        $error = "Account information could not be updated.";

    }

    $stmt->close();
}


/* =========================
   GET TEACHER INFORMATION
========================= */

$sql = "SELECT
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
        WHERE users.id = ?
        AND users.role = 'Teacher'";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $teacher = $result->fetch_assoc();

} else {

    header("Location: teachers.php");
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

    <title>Edit Teacher</title>

    <link rel="stylesheet" href="teacher_users.css">

</head>

<body>

<div class="container">

    <h1>Edit Teacher</h1>

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
            value="<?php echo htmlspecialchars($teacher["firstname"]); ?>"
            required>


        <label>Last Name</label>

        <input
            type="text"
            name="lastname"
            value="<?php echo htmlspecialchars($teacher["lastname"]); ?>"
            required>


        <label>Email</label>

        <input
            type="email"
            name="email"
            value="<?php echo htmlspecialchars($teacher["email"]); ?>"
            required>


        <label>Username</label>

        <input
            type="text"
            name="username"
            value="<?php echo htmlspecialchars($teacher["username"]); ?>"
            required>


        <label>Employee Number</label>

        <input
            type="text"
            name="employee_number"
            value="<?php echo htmlspecialchars($teacher["employee_number"]); ?>"
            required>


        <label>Department</label>

        <input
            type="text"
            name="department"
            value="<?php echo htmlspecialchars($teacher["department"]); ?>"
            required>


        <label>Position</label>

        <input
            type="text"
            name="position"
            value="<?php echo htmlspecialchars($teacher["position"]); ?>"
            required>


        <button type="submit">
            Save Changes
        </button>

    </form>


    <br>

    <a href="teachers.php">
        ← Back to Teachers
    </a>

</div>

</body>

</html>