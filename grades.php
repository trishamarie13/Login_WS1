<?php
session_start();
require_once "db.php";

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "Teacher"
) {
    header("Location: teachers_dashboard.php");
    exit();
}

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "delete") {
        $id = filter_input(
            INPUT_POST,
            "id",
            FILTER_VALIDATE_INT
        );

        if ($id) {
            $stmt = $conn->prepare(
                "DELETE FROM grades WHERE id = ?"
            );
            $stmt->bind_param("i", $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $message = "Grade deleted successfully.";
                $messageType = "success";
            } else {
                $message = "Grade not found or could not be deleted.";
                $messageType = "error";
            }

            $stmt->close();
        }
    }

    if ($action === "update") {
        $id = filter_input(
            INPUT_POST,
            "id",
            FILTER_VALIDATE_INT
        );

        $subject = trim($_POST["subject"] ?? "");
        $grade = $_POST["grade"] ?? "";
        $semester = trim($_POST["semester"] ?? "");
        $school_year = trim($_POST["school_year"] ?? "");

        if (
            !$id ||
            $subject === "" ||
            $grade === "" ||
            !is_numeric($grade) ||
            (float)$grade < 0 ||
            (float)$grade > 100 ||
            $semester === "" ||
            $school_year === ""
        ) {
            $message = "Please enter valid grade information.";
            $messageType = "error";
        } else {
            $numericGrade = (float)$grade;

            $stmt = $conn->prepare(
                "UPDATE grades
                 SET subject = ?, grade = ?,
                     semester = ?, school_year = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "sdssi",
                $subject,
                $numericGrade,
                $semester,
                $school_year,
                $id
            );

            if ($stmt->execute()) {
                $message = "Grade updated successfully.";
                $messageType = "success";
            } else {
                $message = "Unable to update grade: " . $stmt->error;
                $messageType = "error";
            }

            $stmt->close();
        }
    }
}

$editGrade = null;

if (isset($_GET["edit"])) {
    $editId = filter_input(
        INPUT_GET,
        "edit",
        FILTER_VALIDATE_INT
    );

    if ($editId) {
        $stmt = $conn->prepare(
            "SELECT id, student_id, subject, grade,
                    semester, school_year
             FROM grades
             WHERE id = ?"
        );

        $stmt->bind_param("i", $editId);
        $stmt->execute();

        $editGrade = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

$sql = "
    SELECT
        g.id,
        g.student_id,
        g.subject,
        g.grade,
        g.semester,
        g.school_year,
        u.firstname,
        u.lastname
    FROM grades g
    LEFT JOIN users u ON g.student_id = u.id
    ORDER BY g.id DESC
";

$result = $conn->query($sql);

if (!$result) {
    die("Unable to load grades: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades</title>

    <style>
       * {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 25px;
    font-family: Arial, sans-serif;
    background: #fff0f5;
    color: #333333;
}

.container {
    max-width: 1200px;
    margin: auto;
}

.header,
.panel {
    background: #ffffff;
    padding: 22px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 3px 10px rgba(214, 51, 132, 0.10);
    border: 1px solid #f5c6d8;
}

h1,
h2 {
    margin-top: 0;
    color: #c2185b;
}

.header p {
    color: #7a5264;
}

a {
    color: #d63384;
    text-decoration: none;
}

a:hover {
    color: #ad1457;
    text-decoration: underline;
}

.message {
    padding: 12px 15px;
    border-radius: 6px;
    overflow-wrap: anywhere;
}

.success {
    background: #d8f3dc;
    color: #1b5e20;
}

.error {
    background: #fce4ec;
    color: #880e4f;
}

.table-wrap {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 750px;
    background: #ffffff;
}

th,
td {
    text-align: left;
    padding: 13px;
    border-bottom: 1px solid #f3d5e1;
}

th {
    background: #d63384;
    color: #ffffff;
    font-weight: 600;
}

tr:hover td {
    background: #fff0f5;
}

input,
select {
    width: 100%;
    padding: 11px 12px;
    margin: 6px 0 14px;
    border: 1px solid #e8b4c8;
    border-radius: 6px;
    background: #ffffff;
    color: #333333;
    font-size: 14px;
}

input:focus,
select:focus {
    outline: none;
    border-color: #d63384;
    box-shadow: 0 0 0 3px rgba(214, 51, 132, 0.12);
}

button,
.button {
    display: inline-block;
    border: none;
    border-radius: 6px;
    padding: 10px 16px;
    color: #ffffff;
    background: #d63384;
    text-decoration: none;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.2s ease;
}

button:hover,
.button:hover {
    background: #ad1457;
    color: #ffffff;
    text-decoration: none;
}

.delete {
    background: #b42318;
}

.delete:hover {
    background: #8f1d14;
}

.cancel {
    color: #d63384;
    margin-left: 10px;
}

.actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.actions form {
    margin: 0;
}

@media (max-width: 600px) {
    body {
        padding: 12px;
    }

    .header,
    .panel {
        padding: 15px;
    }

    h1 {
        font-size: 24px;
    }
}
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Manage Student Grades</h1>
        <p>View, edit, and delete recorded student grades.</p>

        <a class="button" href="add_grades.php">+ Add Grade</a>
        <a class="button" href="teacher_dashboard.php">Dashboard</a>
    </div>

    <?php if ($message !== ""): ?>
        <p class="message <?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <?php if ($editGrade): ?>
        <div class="panel">
            <h2>Edit Grade</h2>

            <form method="POST" action="grades.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id"
                       value="<?= (int)$editGrade["id"] ?>">

                <label for="subject">Subject</label>
                <input id="subject" name="subject" type="text"
                       required
                       value="<?= htmlspecialchars($editGrade["subject"]) ?>">

                <label for="grade">Grade (0–100)</label>
                <input id="grade" name="grade" type="number"
                       min="0" max="100" step="0.01" required
                       value="<?= htmlspecialchars((string)$editGrade["grade"]) ?>">

                <label for="semester">Semester</label>
                <select id="semester" name="semester" required>
                    <?php
                    $semesters = [
                        "1st Semester",
                        "2nd Semester",
                        "Summer"
                    ];

                    foreach ($semesters as $sem):
                    ?>
                        <option value="<?= htmlspecialchars($sem) ?>"
                            <?= $editGrade["semester"] === $sem
                                ? "selected" : "" ?>>
                            <?= htmlspecialchars($sem) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="school_year">School Year</label>
                <input id="school_year" name="school_year"
                       type="text" required
                       value="<?= htmlspecialchars($editGrade["school_year"]) ?>">

                <button type="submit">Save Changes</button>
                <a class="cancel" href="grades.php">Cancel</a>
            </form>
        </div>
    <?php endif; ?>

    <div class="panel">
        <h2>Grade Records</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>Grade</th>
                        <th>Semester</th>
                        <th>School Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$row["id"] ?></td>

                            <td>
                                <?= htmlspecialchars(
                                    trim(
                                        ($row["firstname"] ?? "") . " " .
                                        ($row["lastname"] ?? "")
                                    ) ?: "Student ID " . $row["student_id"]
                                ) ?>
                            </td>

                            <td><?= htmlspecialchars($row["subject"]) ?></td>

                            <td><?= htmlspecialchars((string)$row["grade"]) ?></td>

                            <td><?= htmlspecialchars($row["semester"]) ?></td>

                            <td><?= htmlspecialchars($row["school_year"]) ?></td>

                            <td>
                                <div class="actions">
                                    <a class="button"
                                       href="grades.php?edit=<?= (int)$row["id"] ?>">
                                        Edit
                                    </a>

                                    <form method="POST" action="grades.php"
                                          onsubmit="return confirm('Delete this grade record?');">
                                        <input type="hidden" name="action"
                                               value="delete">
                                        <input type="hidden" name="id"
                                               value="<?= (int)$row["id"] ?>">
                                        <button class="delete" type="submit">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            No grades have been added yet.
                            <a href="add_grades.php">Add the first grade</a>.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
