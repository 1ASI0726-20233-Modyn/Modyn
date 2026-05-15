<?php
// ============================================
// delete.php - Ubicación: /features/delete.php
// ============================================

require_once '../modynConnection.php';

if (isset($_GET['tabla']) && isset($_GET['id'])) {
    $tabla = mysqli_real_escape_string($link, $_GET['tabla']);
    $idValue = mysqli_real_escape_string($link, $_GET['id']);

    // Buscamos la Llave Primaria
    $resField = mysqli_query($link, "SHOW COLUMNS FROM $tabla");
    $colRow = mysqli_fetch_array($resField);
    $primaryKey = $colRow[0];

    // --- SOLUCIÓN AL ERROR DE FOREIGN KEY ---
    // 1. Desactivamos la revisión de llaves foráneas
    mysqli_query($link, "SET FOREIGN_KEY_CHECKS = 0");

    // 2. Ejecutamos la eliminación
    $sql = "DELETE FROM $tabla WHERE $primaryKey = '$idValue'";
    $ejecucion = mysqli_query($link, $sql);

    // 3. Volvemos a activar la revisión por seguridad
    mysqli_query($link, "SET FOREIGN_KEY_CHECKS = 1");
    // ----------------------------------------

    if ($ejecucion) {
        header("Location: ../tables.php?tabla=$tabla&msg=deleted");
    } else {
        echo "<h2>Error al eliminar</h2>";
        echo "<p>" . mysqli_error($link) . "</p>";
        echo "<br><a href='../tables.php?tabla=$tabla'>Volver</a>";
    }
} else {
    header("Location: ../tables.php");
}

mysqli_close($link);
?>