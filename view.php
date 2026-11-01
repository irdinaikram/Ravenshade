<?php
include 'auth.php';
include 'db.php';

// Read the id from the URL (view.php?id=3). (int) forces it to be a number.
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM results WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View result - Ravenshade</title>
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
            <?php if ($row): ?>
                <?php $grade = htmlspecialchars(strtoupper($row['grade'])); ?>
                <div class="result-head">
                    <div>
                        <h1 class="who-name"><?= htmlspecialchars($row['student_name']) ?></h1>
                        <p class="meta">
                            <?= htmlspecialchars($row['matric_no']) ?>, <?= htmlspecialchars($row['subject_name']) ?> (<?= htmlspecialchars($row['subject_code']) ?>)
                        </p>
                    </div>
                    <span class="grade big-grade grade-<?= $grade ?>"><?= $grade ?></span>
                </div>

                <table class="marks">
                    <tr><th>Test 1 (20%)</th><td><?= number_format($row['test1'], 2) ?></td></tr>
                    <tr><th>Test 2 (30%)</th><td><?= number_format($row['test2'], 2) ?></td></tr>
                    <tr><th>Final exam (50%)</th><td><?= number_format($row['final_exam'], 2) ?></td></tr>
                    <tr class="final"><th>Total marks</th><td><?= number_format($row['total_Marks'], 2) ?></td></tr>
                </table>

                <p class="back">
                    <a class="btn-ghost" href="edit.php?id=<?= (int)$row['id'] ?>">Edit this result</a>
                    <a class="btn-ghost" href="index.php">Back to results</a>
                </p>
            <?php else: ?>
                <h1>Result not found</h1>
                <p class="sub">This result may have been deleted.</p>
                <p class="back"><a class="btn-ghost" href="index.php">Back to results</a></p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>