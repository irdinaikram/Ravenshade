<?php
include 'auth.php';
include 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM results WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Go back to the list. The "Are you sure?" popup is in index.php.
header("Location: index.php");
exit;