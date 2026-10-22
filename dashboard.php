<?php
session_start();

include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$message = "";
$error = "";

if ($_SESSION["role"] === "Teacher" && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_grade"])) {
    $student_id = intval($_POST["student_id"]);
    $subject = trim($_POST["subject"]);
    $grade = floatval($_POST["grade"]);
    $grade_id = isset($_POST["grade_id"]) ? intval($_POST["grade_id"]) : 0;
    $teacher_id = $_SESSION["user_id"];

    if (!empty($student_id) && !empty($subject) && $grade >= 0 && $grade <= 100) {
        if ($grade_id > 0) {
            $update_sql = "UPDATE grades SET student_id = ?, subject = ?, grade = ? WHERE id = ? AND teacher_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("isdii", $student_id, $subject, $grade, $grade_id, $teacher_id);
            if ($stmt->execute()) {
                $message = "Grade updated successfully!";
            } else {
                $error = "Failed to update grade.";
            }
            $stmt->close();
        } else {
            $insert_sql = "INSERT INTO grades (student_id, teacher_id, subject, grade) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iisd", $student_id, $teacher_id, $subject, $grade);
            if ($stmt->execute()) {
                $message = "Grade added successfully!";
            } else {
                $error = "Failed to add grade.";
            }
            $stmt->close();
        }
    } else {
        $error = "Please fill in all fields with valid data (Grade 0–100).";
    }
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

if ($_SESSION["role"] === "Teacher") {
    $teacher_id = $_SESSION["user_id"];
    
    $grades_sql = "SELECT grades.id AS grade_id, grades.subject, grades.grade, users.firstname, users.lastname 
                   FROM grades 
                   INNER JOIN users ON grades.student_id = users.id 
                   WHERE grades.teacher_id = ? 
                   ORDER BY grades.id DESC";
    $stmt = $conn->prepare($grades_sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $grades_result = $stmt->get_result();

    $edit_grade = null;
    if (isset($_GET["edit_grade"])) {
        $edit_id = intval($_GET["edit_grade"]);
        $edit_sql = "SELECT * FROM grades WHERE id = ? AND teacher_id = ?";
        $edit_stmt = $conn->prepare($edit_sql);
        $edit_stmt->bind_param("ii", $edit_id, $teacher_id);
        $edit_stmt->execute();
        $edit_res = $edit_stmt->get_result();
        if ($edit_res->num_rows === 1) {
            $edit_grade = $edit_res->fetch_assoc();
        }
        $edit_stmt->close();
    }
}

if ($_SESSION["role"] === "Student") {
    $student_id = $_SESSION["user_id"];
    $my_grades_sql = "SELECT grades.subject, grades.grade, users.firstname AS teacher_first, users.lastname AS teacher_last 
                      FROM grades 
                      INNER JOIN users ON grades.teacher_id = users.id 
                      WHERE grades.student_id = ? 
                      ORDER BY grades.id DESC";
    $stmt = $conn->prepare($my_grades_sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $my_grades_result = $stmt->get_result();
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

        <div class="accounts-container" style="margin-top: 20px;">
            <div class="accounts-box">
                <h2>My Grades</h2>
                <div class="table-container">
                    <table class="accounts-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Grade</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($my_grades_result) && $my_grades_result->num_rows > 0): ?>
                                <?php while ($mg = $my_grades_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($mg["subject"]); ?></td>
                                        <td><?php echo htmlspecialchars($mg["grade"]); ?></td>
                                        <td><?php echo htmlspecialchars($mg["teacher_first"] . " " . $mg["teacher_last"]); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="no-accounts">No grades assigned yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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

        <?php if (!empty($message)): ?>
            <p style="color: green; margin-top: 15px;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <p style="color: red; margin-top: 15px;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <div class="accounts-container" style="margin-top: 20px;">
            <div class="accounts-box">
                <h2><?php echo isset($edit_grade) ? "Edit Grade" : "Add Grade"; ?></h2>
                <form action="dashboard.php" method="POST" style="display: flex; flex-direction: column; gap: 10px; margin-top: 10px;">
                    <?php if (isset($edit_grade)): ?>
                        <input type="hidden" name="grade_id" value="<?php echo $edit_grade['id']; ?>">
                    <?php endif; ?>

                    <label for="student_id">Select Student:</label>
                    <select name="student_id" id="student_id" required style="padding: 6px;">
                        <option value="">-- Choose Student --</option>
                        <?php 
                        if ($student_result->num_rows > 0) {
                            $student_result->data_seek(0);
                            while ($st = $student_result->fetch_assoc()) {
                                $selected = (isset($edit_grade) && $edit_grade['student_id'] == $st['id']) ? "selected" : "";
                                echo "<option value='" . $st['id'] . "' $selected>" . htmlspecialchars($st['firstname'] . " " . $st['lastname']) . "</option>";
                            }
                        }
                        ?>
                    </select>

                    <label for="subject">Subject:</label>
                    <input type="text" name="subject" id="subject" value="<?php echo isset($edit_grade) ? htmlspecialchars($edit_grade['subject']) : ''; ?>" required style="padding: 6px;">

                    <label for="grade">Grade:</label>
                    <input type="number" step="0.01" name="grade" id="grade" min="0" max="100" value="<?php echo isset($edit_grade) ? htmlspecialchars($edit_grade['grade']) : ''; ?>" required style="padding: 6px;">

                    <button type="submit" name="save_grade" style="padding: 8px; margin-top: 5px; cursor: pointer;">
                        <?php echo isset($edit_grade) ? "Update Grade" : "Submit Grade"; ?>
                    </button>
                    <?php if (isset($edit_grade)): ?>
                        <a href="dashboard.php" style="text-align: center; color: #666; font-size: 0.9em;">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="accounts-box">
                <h2>Manage Grades</h2>
                <div class="table-container">
                    <table class="accounts-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Grade</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($grades_result) && $grades_result->num_rows > 0): ?>
                                <?php while ($g = $grades_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($g["firstname"] . " " . $g["lastname"]); ?></td>
                                        <td><?php echo htmlspecialchars($g["subject"]); ?></td>
                                        <td><?php echo htmlspecialchars($g["grade"]); ?></td>
                                        <td>
                                            <a href="dashboard.php?edit_grade=<?php echo $g['grade_id']; ?>">Edit</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="no-accounts">No grades recorded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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