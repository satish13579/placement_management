<?php
session_start();
session_unset();
session_destroy();
include 'conn.php';
header("Location:".$BASE_URL.'login.html');
?>
