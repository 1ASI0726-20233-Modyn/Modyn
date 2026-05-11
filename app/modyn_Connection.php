<?php
$host = "158.23.145.99";
$user = "admin";
$pass = "Admin123";
$db   = "Modyn_DB";

$link = mysqli_connect($host, $user, $pass, $db);
if (!$link) {
    die("Error de conexión: " . mysqli_connect_error());
}
mysqli_set_charset($link, 'utf8mb4');
?>
