<?php
// ============================================
// VISTA DE TABLAS Y DATOS DE LA BD (ACTUALIZADO)
// ============================================

require_once 'modynConnection.php';
require_once 'header.php';

// ============================================
// SECCIÓN 1: LISTAR TODAS LAS TABLAS
// ============================================
if (!isset($_GET['tabla'])) {
    // Mostrar lista de todas las tablas
    $res = mysqli_query($link, "SHOW TABLES");
    $count = mysqli_num_rows($res);

    echo "<h2>Tablas de Modyn_DB</h2>";
    echo "<p>Tablas encontradas: <strong>$count</strong></p>";
    echo "<table>";
    echo "<tr><th>#</th><th>Tabla</th><th>Ver datos</th></tr>";

    $i = 1;
    while ($row = mysqli_fetch_array($res)) {
        $nombre = $row[0];
        echo "<tr><td>$i</td><td>$nombre</td><td><a href='tables.php?tabla=$nombre'>Ver</a></td></tr>";
        $i++;
    }
    echo "</table>";

    mysqli_close($link);
    require_once 'footer.php';
    exit;
}

// ============================================
// SECCIÓN 2: MOSTRAR DATOS DE UNA TABLA SELECCIONADA
// ============================================
$tabla = mysqli_real_escape_string($link, $_GET['tabla']);
$res = mysqli_query($link, "SELECT * FROM $tabla");

if (!$res) {
    echo "<h2>Error</h2>";
    echo "<p>Error al obtener datos: " . mysqli_error($link) . "</p>";
} else {
    echo "<h2>Datos de: <strong>$tabla</strong></h2>";

    // Mensaje opcional si viene de delete.php
    if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
        echo "<p style='color: green; font-weight: bold;'>✔ Registro eliminado correctamente.</p>";
    }

    echo "<div style='margin-bottom: 15px;'>";
    echo "<a href='tables.php' style='padding: 8px 15px; background: #f0f0f0; text-decoration: none; border: 1px solid #ccc; border-radius: 4px; display: inline-block; margin-right: 10px;'>← Volver a Tablas</a>";
    echo "<a href='features/insert.php?tabla=$tabla' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold;'>+ Insertar Nuevo Registro</a>";
    echo "</div>";

    $count = mysqli_num_rows($res);
    echo "<p>Registros encontrados: <strong>$count</strong></p>";

    if ($count > 0) {
        echo "<table>";

        // Obtener metadatos de las columnas
        $fields = mysqli_fetch_fields($res);
        // Guardamos el nombre de la primera columna para usarla como ID en el borrado
        $primaryKeyName = $fields[0]->name;

        echo "<tr>";
        foreach ($fields as $field) {
            $fieldName = ($field->name === null) ? "" : htmlspecialchars($field->name);
            echo "<th>" . $fieldName . "</th>";
        }
        echo "<th>Acciones</th>"; // Encabezado para borrar
        echo "</tr>";

        // Mostrar los datos
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>";
            foreach ($row as $value) {
                $displayValue = ($value === null) ? "&nbsp;" : htmlspecialchars($value);
                echo "<td>" . $displayValue . "</td>";
            }

            // Valor de la llave primaria para este registro específico
            $idValue = $row[$primaryKeyName];

// Cambia la ruta de 'delete.php' a 'features/delete.php'
echo "<td>";
echo "<a href='features/delete.php?tabla=$tabla&id=$idValue'
         onclick='return confirm(\"¿Estás seguro de que deseas eliminar este registro?\")'
         style='color: #d9534f; font-weight: bold; text-decoration: none;'>Borrar</a>";
echo "</td>";

            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No hay registros en esta tabla.</p>";
    }
}

mysqli_close($link);
require_once 'footer.php';
?>

