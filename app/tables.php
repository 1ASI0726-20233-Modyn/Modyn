<?php
// ============================================
// VISTA DE TABLAS Y DATOS - RUTA: /tables.php
// ============================================

require_once 'modynConnection.php';
require_once 'header.php'; // Asegúrate de que header.php ya tenga el nuevo diseño

// ============================================
// SECCIÓN 1: LISTAR TODAS LAS TABLAS
// ============================================
if (!isset($_GET['tabla'])) {
    $res = mysqli_query($link, "SHOW TABLES");
    $count = mysqli_num_rows($res);

    echo "<div class='container' style='padding: 20px; max-width: 1200px; margin: 0 auto;'>";
    echo "<h2 style='color: #f06292;'>Tablas de Modyn_DB</h2>";
    echo "<p>Tablas encontradas: <strong>$count</strong></p>";

    echo "<table>";
    echo "<tr><th>#</th><th>Nombre de la Tabla</th><th>Acción</th></tr>";

    $i = 1;
    while ($row = mysqli_fetch_array($res)) {
        $nombre = $row[0];
        echo "<tr>
                <td>$i</td>
                <td><strong>" . htmlspecialchars($nombre) . "</strong></td>
                <td><a href='tables.php?tabla=$nombre' style='color: #f06292; font-weight: bold; text-decoration: none;'>Ver Datos</a></td>
              </tr>";
        $i++;
    }
    echo "</table>";
    echo "</div>";

    mysqli_close($link);
    require_once 'footer.php';
    exit;
}

// ============================================
// SECCIÓN 2: MOSTRAR DATOS DE LA TABLA
// ============================================
$tabla = mysqli_real_escape_string($link, $_GET['tabla']);
$res = mysqli_query($link, "SELECT * FROM $tabla");

echo "<div class='container' style='padding: 20px; max-width: 1200px; margin: 0 auto;'>";

if (!$res) {
    echo "<h2 style='color: #d9534f;'>Error</h2>";
    echo "<p>No se pudo acceder a la tabla: " . mysqli_error($link) . "</p>";
} else {
    echo "<h2 style='color: #f06292;'>Datos de la tabla: <span style='color: #333;'>$tabla</span></h2>";

    // Mensajes de confirmación
    if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
        echo "<p style='color: #4CAF50; font-weight: bold; background: #e8f5e9; padding: 10px; border-radius: 5px;'>✔ Registro eliminado correctamente.</p>";
    }

    // Botones de navegación (Rutas actualizadas a /infrastructure)
    echo "<div style='margin-bottom: 20px; display: flex; gap: 10px;'>";
    echo "<a href='tables.php' style='padding: 10px 20px; background: #fce4ec; color: #f06292; text-decoration: none; border-radius: 20px; font-weight: bold; border: 1px solid #f8bbd0;'>← Volver a lista</a>";
    echo "<a href='infrastructure/insert.php?tabla=$tabla' style='padding: 10px 20px; background: #f06292; color: white; text-decoration: none; border-radius: 20px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>+ Insertar Nuevo Registro</a>";
    echo "</div>";

    $count = mysqli_num_rows($res);
    echo "<p>Registros encontrados: <strong>$count</strong></p>";

    if ($count > 0) {
        echo "<table>";

        // Obtener nombres de columnas
        $fields = mysqli_fetch_fields($res);
        $primaryKeyName = $fields[0]->name; // Asumimos la primera columna como ID

        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "<th>Acciones</th>";
        echo "</tr>";

        // Mostrar filas de datos
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>"; // Error tipográfico corregido aquí
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }

            $idValue = $row[$primaryKeyName];

            echo "<td style='text-align: center;'>";

            // =========================================================
            // TU CONEXIÓN: Botón "Editar" exclusivo para Products
            // =========================================================
            if ($tabla === 'Products') {
                echo "<a href='infrastructure/update.php?tabla=$tabla&id=$idValue'
                         style='color: #0ea5e9; font-weight: bold; text-decoration: none; background: #e0f2fe; padding: 5px 10px; border-radius: 10px; margin-right: 8px;'>Editar</a>";
            }

            // Enlace de borrado original de tus compañeros
            echo "<a href='infrastructure/delete.php?tabla=$tabla&id=$idValue'
                     onclick='return confirm(\"¿Estás seguro de que deseas eliminar este registro?\")'
                     style='color: #e11d48; font-weight: bold; text-decoration: none; background: #fff1f2; padding: 5px 10px; border-radius: 10px;'>Borrar</a>";

            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='font-style: italic; color: #999;'>Esta tabla no contiene registros actualmente.</p>";
    }
}

echo "</div>";

mysqli_close($link);
require_once 'footer.php';
?>