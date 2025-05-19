<?php
session_start();
session_destroy(); // Destroy the session
session_unset();
unset($_SESSION["email"]);
header("Location: logIn.php");
exit();
?>