<?php
// ============================================
// FORMULARIO DE INSERT - INSERTAR REGISTROS
// ============================================

require_once '../modynConnection.php';
require_once '../header.php';

// ============================================
// SECCIÓN 1: PROCESAR FORMULARIO POST
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Procesar el insert
    $tabla = mysqli_real_escape_string($link, $_POST['tabla']);

    // Obtener las columnas de la tabla con sus tipos
    $res = mysqli_query($link, "DESCRIBE $tabla");
    $columnsInfo = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $columnsInfo[$row['Field']] = $row;
    }

    // Construir el INSERT
    $valores = [];
    $columnas = [];

    foreach ($columnsInfo as $col => $info) {
        if (isset($_POST[$col])) {
            $valor = $_POST[$col];

            // Si el campo es opcional y está vacío, saltarlo
            if ($valor === '' && $info['Null'] === 'YES') {
                continue;
            }

            // Si el campo es requerido y está vacío, mostrar error
            if ($valor === '' && $info['Null'] === 'NO') {
                echo "<h2>✗ Error: El campo <strong>$col</strong> es obligatorio</h2>";
                echo "<p><a href='insert.php?tabla=$tabla'>← Volver</a></p>";
                require_once '../footer.php';
                exit;
            }

            // Si hay valor, agregarlo
            if ($valor !== '') {
                $columnas[] = $col;
                // Usar NULL para valores vacíos en campos opcionales, o combinar con prepared statements
                $valores[] = "'" . mysqli_real_escape_string($link, $valor) . "'";
            }
        }
    }

    if (!empty($columnas)) {
        $sql = "INSERT INTO $tabla (" . implode(", ", $columnas) . ") VALUES (" . implode(", ", $valores) . ")";

        if (mysqli_query($link, $sql)) {
            echo "<h2>✓ Registro insertado exitosamente</h2>";
            echo "<p><a href='../tables.php?tabla=$tabla'>← Ver tabla</a></p>";
        } else {
            echo "<h2>✗ Error al insertar</h2>";
            echo "<p><strong>Error:</strong> " . htmlspecialchars(mysqli_error($link)) . "</p>";
            echo "<p style='color: #999; font-size: 12px; margin-top: 10px;'>";
            echo "Por favor verifica que los datos cumplan con el formato requerido:<br>";
            echo "- Fecha debe ser: YYYY-MM-DD<br>";
            echo "- Fecha y hora debe ser: YYYY-MM-DD HH:MM:SS<br>";
            echo "</p>";
            echo "<p><a href='insert.php?tabla=$tabla'>← Volver e intentar de nuevo</a></p>";
        }
    } else {
        echo "<h2>✗ Por favor completa al menos un campo</h2>";
        echo "<p><a href='insert.php?tabla=$tabla'>← Volver</a></p>";
    }

    mysqli_close($link);
    require_once '../footer.php';
    exit;
}

// ============================================
// SECCIÓN 2: MOSTRAR LISTA DE TABLAS
// ============================================
if (!isset($_GET['tabla'])) {
    $res = mysqli_query($link, "SHOW TABLES");

    echo "<h2>Insertar Registro</h2>";
    echo "<p>Selecciona una tabla para insertar un nuevo registro:</p>";
    echo "<form method='GET'>";
    echo "<select name='tabla' required>";
    echo "<option value=''>-- Selecciona una tabla --</option>";

    while ($row = mysqli_fetch_array($res)) {
        $nombre = $row[0];
        echo "<option value='$nombre'>$nombre</option>";
    }

    echo "</select>";
    echo "<button type='submit'>Continuar</button>";
    echo "</form>";
    echo "<p><a href='../tables.php'>← Volver a Tablas</a></p>";

    mysqli_close($link);
    require_once '../footer.php';
    exit;
}

// ============================================
// SECCIÓN 3: MOSTRAR FORMULARIO DE INSERCIÓN
// ============================================
$tabla = mysqli_real_escape_string($link, $_GET['tabla']);
$res = mysqli_query($link, "DESCRIBE $tabla");

// Obtener información de claves foráneas
$fkInfo = [];
$resFK = mysqli_query($link, "SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '$tabla' AND REFERENCED_TABLE_NAME IS NOT NULL");
while ($fkRow = mysqli_fetch_assoc($resFK)) {
    $fkInfo[$fkRow['COLUMN_NAME']] = [
        'tabla_ref' => $fkRow['REFERENCED_TABLE_NAME'],
        'columna_ref' => $fkRow['REFERENCED_COLUMN_NAME']
    ];
}

// Obtener el ID principal (PRIMARY KEY)
$resPK = mysqli_query($link, "DESCRIBE $tabla");
$primaryKey = null;
while ($pkRow = mysqli_fetch_assoc($resPK)) {
    if (strpos($pkRow['Key'], 'PRI') !== false) {
        $primaryKey = $pkRow['Field'];
        break;
    }
}

// Obtener el siguiente ID
$nextId = 1;
if ($primaryKey) {
    $resMaxId = mysqli_query($link, "SELECT MAX($primaryKey) as maxId FROM $tabla");
    $maxIdRow = mysqli_fetch_assoc($resMaxId);
    $nextId = ($maxIdRow['maxId'] ?? 0) + 1;
}

if (!$res) {
    echo "<h2>Error</h2>";
    echo "<p>" . mysqli_error($link) . "</p>";
} else {
    echo "<h2>Insertar en: <strong>$tabla</strong></h2>";
    echo "<p><a href='insert.php'>← Cambiar tabla</a> | <a href='../tables.php?tabla=$tabla'>← Ver datos</a></p>";

    // Mostrar el próximo ID que se generará
    if ($primaryKey) {
        echo "<div style='margin: 10px 0; padding: 10px; background: #e3f2fd; border-left: 4px solid #2196F3; border-radius: 4px;'>";
        echo "<strong>Próximo ID:</strong> <span style='color: #2196F3; font-size: 18px;'>$nextId</span>";
        echo "</div>";
    }

    echo "<form method='POST'>";
    echo "<input type='hidden' name='tabla' value='$tabla'>";

    while ($row = mysqli_fetch_assoc($res)) {
        $campo = htmlspecialchars($row['Field']);
        $tipo = htmlspecialchars($row['Type']);

        // Saltar campos PRIMARY KEY (se autogeneran)
        if (strpos($row['Key'], 'PRI') !== false) {
            continue;
        }

        echo "<div style='margin: 10px 0; padding: 10px; background: #f9f9f9; border-left: 3px solid #2196F3; border-radius: 4px;'>";
        echo "<label for='$campo'><strong>$campo</strong></label><br>";

        // Verificar si es una clave foránea
        if (isset($fkInfo[$campo])) {
            $tablaRef = $fkInfo[$campo]['tabla_ref'];
            $columnaRef = $fkInfo[$campo]['columna_ref'];
            $resFK_valores = mysqli_query($link, "SELECT $columnaRef FROM $tablaRef ORDER BY $columnaRef ASC");

            echo "<select name='$campo' id='$campo' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;' required>";
            echo "<option value=''>-- Selecciona --</option>";

            while ($fkVal = mysqli_fetch_assoc($resFK_valores)) {
                echo "<option value='" . htmlspecialchars($fkVal[$columnaRef]) . "'>" . htmlspecialchars($fkVal[$columnaRef]) . "</option>";
            }

            echo "</select>";
        }
        // Determinar el tipo de input según el tipo de dato
        elseif (strpos($tipo, 'text') !== false) {
            echo "<textarea name='$campo' id='$campo' style='width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 3px; font-family: Arial;' rows='4' placeholder='Ej: Descripción...'></textarea>";
        } elseif (strpos($tipo, 'varchar') !== false) {
            echo "<input type='text' name='$campo' id='$campo' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;' placeholder='Ej: Juan Pérez'>";
        } elseif (strpos($tipo, 'int') !== false) {
            echo "<input type='number' name='$campo' id='$campo' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;' placeholder='Ej: 5'>";
        } elseif (strpos($tipo, 'double') !== false || strpos($tipo, 'float') !== false) {
            echo "<input type='number' step='0.01' name='$campo' id='$campo' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;' placeholder='Ej: 99.99'>";
        } elseif (strpos($tipo, 'date') !== false && strpos($tipo, 'datetime') === false) {
            echo "<input type='date' name='$campo' id='$campo' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;'>";
        } elseif (strpos($tipo, 'datetime') !== false) {
            echo "<input type='datetime-local' name='$campo' id='$campo' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;'>";
        } else {
            echo "<input type='text' name='$campo' id='$campo' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;' placeholder='Ingresa un valor'>";
        }

        echo "</div>";
    }

    echo "<div style='margin-top: 20px;'>";
    echo "<button type='submit' style='padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer;'>Insertar Registro</button>";
    echo " <a href='../tables.php?tabla=$tabla' style='padding: 10px 20px; background: #f0f0f0; text-decoration: none; border: 1px solid #ccc;'>Cancelar</a>";
    echo "</div>";
    echo "</form>";
}

mysqli_close($link);
require_once '../footer.php';
?>

