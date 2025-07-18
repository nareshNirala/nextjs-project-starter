<?php
require_once 'config.php';
require_once 'php/auth.php';

// Logout user and destroy session
logoutUser();

// Redirect to login page with success message
header('Location: login.php?logged_out=1');
exit();
?>
