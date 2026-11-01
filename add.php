<?php
include 'auth.php';
include 'db.php';
include 'functions.php';

$errors = [];
// Start with empty values so the form can be shown the first time
$student_name = $matric_no = $subject_name = $subject_code = '';
$test1 = $test2 = $final_exam = '';

// Runs only after the Submit button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $matric_no    = trim($_POST['matric_no'] ?? '');
    $subject_name = trim($_POST['subject_name'] ?? '');
    $subject_code = trim($_POST['subject_code'] ?? '');
    $test1        = trim($_POST['test1'] ?? '');
    $test2        = trim($_POST['test2'] ?? '');
    $final_exam   = trim($_POST['final_exam'] ?? '');

    // ---- Validation ----
    if ($student_name === '' || $matric_no === '' || $subject_name === '' || $subject_code === '') {
        $errors[] = 'Please fill in all text fields.';
    }
    foreach (['Test 1' => $test1, 'Test 2' => $test2, 'Final Exam' => $final_exam] as $label => $value) {
        if (!is_numeric($value) || $value < 0 || $value > 100) {
            $errors[] = "$label must be a number between 0 and 100.";
        }
    }

    // ---- Calculate and save ----
    if (empty($errors)) {
        $t1 = (float)$test1;
        $t2 = (float)$test2;
        $fe = (float)$final_exam;

        $total = calculateTotal($t1, $t2, $fe);
        $grade = getGrade($total);

        $stmt = $conn->prepare(
            "INSERT INTO results
             (student_name, matric_no, subject_name, subject_code, test1, test2, final_exam, total_Marks, grade)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        // s = string, d = decimal number (9 values, same order as above)
        $stmt->bind_param("ssssdddds", $student_name, $matric_no, $subject_name, $subject_code,
                          $t1, $t2, $fe, $total, $grade);
        $stmt->execute();

        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Result</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="index.php">Ravenshade</a>
        <div class="who">
            Logged in as <?= htmlspecialchars($_SESSION['username'] ?? '') ?>
            <a class="btn-ghost" href="logout.php">Log out</a>
        </div>
    </header>

    <main class="page narrow">
    <div class="card form-card">
        <h1>Add result</h1>

        <?php if ($errors): ?>
            <div class="error"><ul>
                <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul></div>
        <?php endif; ?>

        <form method="POST" action="add.php">
            <label>Student Name</label>
            <input type="text" name="student_name" value="<?= htmlspecialchars($student_name) ?>" required>

            <label>Matric Number</label>
            <input type="text" name="matric_no" value="<?= htmlspecialchars($matric_no) ?>" required>

            <label>Subject Name</label>
            <input type="text" name="subject_name" value="<?= htmlspecialchars($subject_name) ?>" required>

            <label>Subject Code</label>
            <input type="text" name="subject_code" value="<?= htmlspecialchars($subject_code) ?>" required>

            <label>Test 1 (20%) - mark out of 100</label>
            <input type="number" name="test1" min="0" max="100" step="0.01" value="<?= htmlspecialchars($test1) ?>" required>

            <label>Test 2 (30%) - mark out of 100</label>
            <input type="number" name="test2" min="0" max="100" step="0.01" value="<?= htmlspecialchars($test2) ?>" required>

            <label>Final Examination (50%) - mark out of 100</label>
            <input type="number" name="final_exam" min="0" max="100" step="0.01" value="<?= htmlspecialchars($final_exam) ?>" required>

            <input type="submit" value="Save result">
        </form>

        <p class="back"><a class="btn-ghost" href="index.php">Back to results</a></p>
    </div>
    </main>
</body>
</html>