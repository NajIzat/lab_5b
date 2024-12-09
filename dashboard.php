<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['matric'])) {
    header("Location: login.php");
    exit();
}

// If the user is logged in, redirect to display.php
header("Location: display.php");
exit();
?>
