<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: logIn.php");
    exit();
}

// Optional: You can include this at the top of any protected page
?>