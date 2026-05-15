<?php
// ============================================
// PÁGINA DE INICIO
// ============================================

// Incluir conexión a BD
require_once 'modynConnection.php';

// Incluir header HTML
require_once 'header.php';

// Mostrar contenido de bienvenida
echo "<h2>Bienvenido</h2>";
echo "<p>Selecciona <a href='tables.php'>Tablas</a> para explorar la base de datos Modyn_DB.</p>";

// Incluir footer HTML
require_once 'footer.php';
?>


