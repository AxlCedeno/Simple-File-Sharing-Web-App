<?php
#used to logout link 
require_once 'shared.php';
# destroys the current session which logs the user out, if they were logged in and redirects to the login page
session_destroy(); 
header("Location: login.php"); #go back to login page after logging out
exit;
?>