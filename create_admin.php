<?php
// RUN THIS in your browser: localhost/Ravenshade/create_admin.php
// It creates the "users" table and sets ONE login account.
// Running it again replaces the old account with the one below.
// Delete this file when you are done.
include 'db.php';

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)");

$username = 'Adib';
$password = 'Adib123';
$hash = password_hash($password, PASSWORD_DEFAULT); // never store the plain password

// Remove any old accounts (for example the old "admin"), then add the new one
$conn->query("DELETE FROM users");

$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hash);
$stmt->execute();

echo "Done! Login with username: <b>$username</b> and password: <b>$password</b>.<br>";
echo "Now DELETE this file (create_admin.php). <a href='login.php'>Go to login</a>";