<?php
// TEMPORARY test file: shows what is inside the "users" table.
// Open: localhost/Ravenshade/check_login.php   (delete it when done)
include 'db.php';

$res = $conn->query("SELECT id, username, password FROM users");
echo "Rows in users table: " . $res->num_rows . "<br><br>";

while ($r = $res->fetch_assoc()) {
    echo "Username: " . htmlspecialchars($r['username']) . "<br>";
    echo "Hash length: " . strlen($r['password']) . " (should be 60)<br>";
    echo "Does 'Adib123' match? " . (password_verify('Adib123', $r['password']) ? 'YES' : 'NO') . "<br><br>";
}