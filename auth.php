<?php
// Put "include 'auth.php';" at the very top of every page that needs a login.
// If the user is not logged in, they are sent to the login page.
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}