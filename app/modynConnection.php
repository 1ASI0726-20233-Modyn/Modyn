<?php
// ============================================
// CONEXIÓN A LA BASE DE DATOS MYSQL
// ============================================

// Configuración de conexión
$host = "158.23.178.49";      // Servidor MySQL
$user = "admin";               // Usuario de BD
$pass = "lo subes tu pe mano";            // Contraseña de BD
$db   = "Modyn_DB";            // Nombre de la BD

// Conectar a la BD
$link = mysqli_connect($host, $user, $pass, $db);

// Verificar si la conexión fue exitosa
if (!$link) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Configurar charset UTF-8
mysqli_set_charset($link, 'utf8mb4');
?>
