<?php
// Start the session
session_start();

// Destroy all session variables
session_unset(); 

// Destroy the session
session_destroy();

// Redirect the user to the login page
header('Location: 1index.html'); // Change this to your actual login page
exit();
?>
