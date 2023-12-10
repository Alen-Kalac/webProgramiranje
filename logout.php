<?php
session_start(); // Start session for user authentication

// Destroy the session to log out the user
session_destroy();

// Reload the current page after logging out
header("Location: {$_SERVER['HTTP_REFERER']}");
exit();
?>
