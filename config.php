<?php
/* --------------- À personnaliser --------------- */
$host = 'localhost';          // Correct for local XAMPP
$db   = 'french';            // The database you created
$user = 'root';               // The default XAMPP user
$pass = '';                  // The default XAMPP password is blank
/* ----------------------------------------------- */

$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ];

$pdo = new PDO($dsn, $user, $pass, $options);
?>