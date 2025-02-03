<?php
session_start(); // Start or resume the session

// Destroy all session data
session_unset();
session_destroy();

// Redirect to the home page or login page
header("Location: Home.php");
exit();
?>
