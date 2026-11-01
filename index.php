<?php
include 'auth.php';
include 'db.php';

// 1. Read what the user typed in the search box (from the URL).
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// 2. Choose the query: filtered if there is search text, otherwise everything.
if ($search !== '') {
    $like = "%" . $search . "%";
    $stmt = $conn->prepare(
        "SELECT * FROM results
         WHERE student_name LIKE ?
            OR matric_no LIKE ?
            OR subject_name LIKE ?
            OR subject_code LIKE ?"
    );
    $stmt->bind_param("ssss", $like, $like, $like, $like);
} else {
    $stmt = $conn->prepare("SELECT * FROM results");
}

$stmt->execute();

// Put all the rows into an array so we can count them AND show them
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$count = count($rows);

// 3. Simple statistics for the summary cards: average total and how many of each grade
$sum = 0;
$spread = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
foreach ($rows as $r) {
    $sum += (float)$r['total_Marks'];
    $g = strtoupper($r['grade']);
    if (isset($spread[$g])) {
        $spread[$g]++;
    }
}
$average = $count > 0 ? $sum / $count : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student results - Ravenshade</title>
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

    <main class="page">
        <div class="page-head">
            <div>
                <h1>Student results</h1>
                <p class="sub">
                    <?= $count ?> <?= $count === 1 ? 'result' : 'results' ?><?php if ($search !== ''): ?> matching “<?= htmlspecialchars($search) ?>”<?php endif; ?>
                </p>
            </div>
            <a class="btn" href="add.php">Add result</a>
        </div>

        <?php if ($count > 0): ?>
        <section class="summary">
            <div class="card stat-card average">
                <span class="stat-label">Average total marks</span>
                <span class="stat-value"><?= number_format($average, 2) ?></span>
            </div>
            <div class="card stat-card spread">
                <span class="stat-label">Grade spread</span>
                <div class="bar">
                    <?php foreach ($spread as $g => $n): ?>
                        <?php if ($n > 0): ?>
                            <span class="seg-<?= $g ?>" style="flex: <?= $n ?>"></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <ul class="legend">
                    <?php foreach ($spread as $g => $n): ?>
                        <?php if ($n > 0): ?>
                            <li><span class="dot seg-<?= $g ?>"></span>Grade <?= $g ?>: <?= $n ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
        <?php endif; ?>

        <!-- The search box MUST be inside a <form>, otherwise the Search button does nothing -->
        <form class="card toolbar" method="GET" action="index.php">
            <input type="text" name="search" id="search"
                   aria-label="Search results"
                   placeholder="Search by student name, matric number, subject or code"
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn">Search</button>
            <a class="btn-ghost" href="index.php">Show all results</a>
        </form>

        <div class="card table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Student</th>
                        <th>Subject</th>
                        <th class="num">Test 1</th>
                        <th class="num">Test 2</th>
                        <th class="num">Final exam</th>
                        <th class="num">Total marks</th>
                        <th>Grade</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                foreach ($rows as $row) {
                    $grade = htmlspecialchars(strtoupper($row['grade']));
                    $name = $row['student_name'];
                    $initial = function_exists('mb_substr') ? mb_strtoupper(mb_substr($name, 0, 1)) : strtoupper(substr($name, 0, 1));
                    $id = htmlspecialchars($row['id']);
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="student">
                                <span class="avatar"><?= htmlspecialchars($initial) ?></span>
                                <div>
                                    <div class="cell-title"><?= htmlspecialchars($name) ?></div>
                                    <div class="cell-sub"><?= htmlspecialchars($row['matric_no']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="cell-title"><?= htmlspecialchars($row['subject_name']) ?></div>
                            <div class="cell-sub"><?= htmlspecialchars($row['subject_code']) ?></div>
                        </td>
                        <td class="num"><?= number_format($row['test1'], 2) ?></td>
                        <td class="num"><?= number_format($row['test2'], 2) ?></td>
                        <td class="num"><?= number_format($row['final_exam'], 2) ?></td>
                        <td class="num total"><?= number_format($row['total_Marks'], 2) ?></td>
                        <td><span class="grade grade-<?= $grade ?>"><?= $grade ?></span></td>
                        <td class="actions">
                            <a class="act" href="view.php?id=<?= $id ?>">View</a>
                            <a class="act" href="edit.php?id=<?= $id ?>">Edit</a>
                            <a class="act danger" href="delete.php?id=<?= $id ?>" onclick="return confirm('Are you sure you want to delete this result?')">Delete</a>
                        </td>
                    </tr>
                    <?php
                }

                // Friendly message when there is nothing to show
                if ($count === 0) {
                    if ($search !== '') {
                        echo "<tr><td class='empty' colspan='9'>No results match “" . htmlspecialchars($search) . "”. Try a different name, matric number or subject.</td></tr>";
                    } else {
                        echo "<tr><td class='empty' colspan='9'>No results yet. Use “Add result” to enter the first one.</td></tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>