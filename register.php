<?php

include "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    $student_number = $_POST["student_number"];
    $course = $_POST["course"];
    $year_level = $_POST["year_level"];
    $section = $_POST["section"];

    $role = "Student";

    if ($password !== $confirmPassword) {

        $error = "Passwords do not match.";

    } else {

        $checkSql = "SELECT * FROM users WHERE username = ? OR email = ?";

        $checkStmt = $conn->prepare($checkSql);

        $checkStmt->bind_param(
            "ss",
            $username,
            $email
        );

        $checkStmt->execute();

        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {

            $error = "Username or email already exists.";

        } else {

            $checkStudentSql = "SELECT * FROM students WHERE student_number = ?";

            $checkStudentStmt = $conn->prepare($checkStudentSql);

            $checkStudentStmt->bind_param(
                "s",
                $student_number
            );

            $checkStudentStmt->execute();

            $studentResult = $checkStudentStmt->get_result();

            if ($studentResult->num_rows > 0) {

                $error = "Student number already exists.";

            } else {

                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $sql = "INSERT INTO users
                        (firstname, lastname, email, username, password, role)
                        VALUES (?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);

                $stmt->bind_param(
                    "ssssss",
                    $firstname,
                    $lastname,
                    $email,
                    $username,
                    $hashedPassword,
                    $role
                );

                if ($stmt->execute()) {

                    $user_id = $conn->insert_id;

                    $studentSql = "INSERT INTO students
                                   (user_id, student_number, course, year_level, section)
                                   VALUES (?, ?, ?, ?, ?)";

                    $studentStmt = $conn->prepare($studentSql);

                    $studentStmt->bind_param(
                        "issss",
                        $user_id,
                        $student_number,
                        $course,
                        $year_level,
                        $section
                    );

                    if ($studentStmt->execute()) {

                        $message = "Student account created successfully!";

                    } else {

                        $error = "Student information could not be saved.";

                        $deleteSql = "DELETE FROM users WHERE id = ?";

                        $deleteStmt = $conn->prepare($deleteSql);

                        $deleteStmt->bind_param(
                            "i",
                            $user_id
                        );

                        $deleteStmt->execute();

                        $deleteStmt->close();
                    }

                    $studentStmt->close();

                } else {

                    $error = "Something went wrong. Please try again.";
                }

                $stmt->close();
            }

            $checkStudentStmt->close();
        }

        $checkStmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Student Account</title>

    <link rel="stylesheet" href="regstyle.css">

</head>

<body>

<div class="container">

    <div class="register-box">

        <h1>Create Student Account</h1>

        <p>Fill in the information below</p>


        <?php if ($message != ""): ?>

            <p style="color: green;">
                <?php echo htmlspecialchars($message); ?>
            </p>

            <p>
                <a href="login.php">Go to Login</a>
            </p>

        <?php endif; ?>


        <?php if ($error != ""): ?>

            <p style="color: red;">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>


        <?php if ($message == ""): ?>

        <form action="register.php" method="POST">


            <!-- USER INFORMATION -->

            <div class="input-box">

                <label for="firstname">
                    First Name
                </label>

                <input
                    type="text"
                    id="firstname"
                    name="firstname"
                    placeholder="Enter your First Name"
                    required
                >

            </div>


            <div class="input-box">

                <label for="lastname">
                    Last Name
                </label>

                <input
                    type="text"
                    id="lastname"
                    name="lastname"
                    placeholder="Enter your Last Name"
                    required
                >

            </div>


            <div class="input-box">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <div class="input-box">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Choose a username"
                    required
                >

            </div>


            <div class="input-box">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Create a password"
                    minlength="8"
                    required
                >

            </div>


            <div class="input-box">

                <label for="confirmPassword">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirmPassword"
                    name="confirmPassword"
                    placeholder="Confirm your password"
                    minlength="8"
                    required
                >

            </div>


            <!-- STUDENT INFORMATION -->

            <div class="input-box">

                <label for="student_number">
                    Student Number
                </label>

                <input
                    type="text"
                    id="student_number"
                    name="student_number"
                    placeholder="Enter student number"
                    required
                >

            </div>


            <div class="input-box">

                <label for="course">
                    Course
                </label>

                <input
                    type="text"
                    id="course"
                    name="course"
                    placeholder="Example: BSIT"
                    required
                >

            </div>


            <div class="input-box">

                <label for="year_level">
                    Year Level
                </label>

                <select
                    id="year_level"
                    name="year_level"
                    required
                >

                    <option value="">
                        Select Year Level
                    </option>

                    <option value="1st Year">
                        1st Year
                    </option>

                    <option value="2nd Year">
                        2nd Year
                    </option>

                    <option value="3rd Year">
                        3rd Year
                    </option>

                    <option value="4th Year">
                        4th Year
                    </option>

                </select>

            </div>


            <div class="input-box">

                <label for="section">
                    Section
                </label>

                <input
                    type="text"
                    id="section"
                    name="section"
                    placeholder="Example: A"
                    required
                >

            </div>


            <button type="submit">
                Create Account
            </button>

        </form>

        <?php endif; ?>


        <div class="links">

            <a href="login.php">
                ← Back to Login
            </a>

        </div>

    </div>

</div>

</body>

</html>