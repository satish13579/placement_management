<?php 
include '../conn.php';
session_start();
if(!isset($_SESSION['role']) && !isset($_SESSION['id'])){
    header("Location: ".$BASE_URL.'login.html');
}else if($_SESSION['role']!='college'){
    header("Location: ".$BASE_URL.'login.html');
}

?>