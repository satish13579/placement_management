<?php

$servername = "localhost";
$username = "root";
$password = "";
$name = 'pm';

// Create connection
$conn = mysqli_connect($servername, $username, $password, $name);

$servername     = 'localhost';
$username       = 'root';
$password       = '';
$db_name        = 'pm';

$dsn = "mysql:host=$servername;dbname=$db_name";
$option = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode = CONCAT(@@sql_mode, ",", "NO_BACKSLASH_ESCAPES")',
];

$conn = new PDO($dsn, $username, $password, $option);

$BASE_URL = 'http://localhost/placement_management/';
