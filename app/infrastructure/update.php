<?php
// Ruta: app/infrastructure/update.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1. Incluir conexión (ruta relativa porque estamos en la carpeta infrastructure)
require_once '../modynConnection.php';

// 2. Si se envió el formulario por POST (Ejecutar el UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $tabla = $_POST['tabla'];

    // Validación estricta
    if ($tabla !== 'Products') {
        require_once '../header.php';
        echo "<h3 style='color: red; text-align: center;'>Acceso denegado. Solo se editan Products.</h3>";
        require_once '../footer.php';
        exit;
    }

    // Escapamos los datos para evitar errores de sintaxis en SQL
    $PRO_name = mysqli_real_escape_string($link, trim($_POST['PRO_name']));
    $PRO_description = mysqli_real_escape_string($link, trim($_POST['PRO_description']));
    $PRO_price = (float)$_POST['PRO_price'];

    // Sentencia SQL con tus nombres de columnas reales
    $query = "UPDATE Products
              SET PRO_name = '$PRO_name',
                  PRO_description = '$PRO_description',
                  PRO_price = '$PRO_price'
              WHERE PRO_id = $id";

    require_once '../header.php'; // Cargamos el diseño superior

    if (mysqli_query($link, $query)) {
        echo "<h3 style='color: green; text-align: center;'>✅ Producto actualizado correctamente.</h3>";
        echo "<div style='text-align: center;'><a href='../tables.php?tabla=Products'>Volver a la tabla</a></div>";
    } else {
        echo "<h3 style='color: red; text-align: center;'>❌ Error al actualizar: " . mysqli_error($link) . "</h3>";
        echo "<div style='text-align: center;'><a href='../tables.php?tabla=Products'>Volver</a></div>";
    }

    require_once '../footer.php'; // Cargamos el diseño inferior
    exit;
}

// 3. Si se entra por GET (Mostrar el formulario)
require_once '../header.php';

if(isset($_GET['id']) && isset($_GET['tabla'])) {
    $id = (int)$_GET['id'];
    $tabla = $_GET['tabla'];

    if ($tabla !== 'Products') {
        echo "<div style='text-align:center; margin-top: 50px;'>";
        echo "<h2>Solo tienes permisos para editar la tabla <i>Products</i>.</h2>";
        echo "<a href='../tables.php'>Volver al inicio</a>";
        echo "</div>";
        require_once '../footer.php';
        exit;
    }

    // Consultamos el registro con tu llave primaria PRO_id
    $query_actual = "SELECT PRO_id, PRO_name, PRO_description, PRO_price FROM Products WHERE PRO_id = $id";
    $resultado = mysqli_query($link, $query_actual);

    if($producto = mysqli_fetch_assoc($resultado)) {
        ?>

        <h2 style="text-align: center;">Editar Producto #<?php echo htmlspecialchars($producto['PRO_id']); ?></h2>

        <form method="POST" action="update.php" style="max-width: 400px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; background: #f9f5f1;">
            <input type="hidden" name="id" value="<?php echo $producto['PRO_id']; ?>">
            <input type="hidden" name="tabla" value="Products">

            <label><strong>Nombre del Producto:</strong></label><br>
            <input type="text" name="PRO_name" value="<?php echo htmlspecialchars($producto['PRO_name']); ?>" required style="width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box;"><br>

            <label><strong>Descripción:</strong></label><br>
            <textarea name="PRO_description" required style="width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box; min-height: 80px;"><?php echo htmlspecialchars($producto['PRO_description']); ?></textarea><br>

            <label><strong>Precio (S/):</strong></label><br>
            <input type="number" step="0.01" name="PRO_price" value="<?php echo htmlspecialchars($producto['PRO_price']); ?>" required style="width: 100%; padding: 8px; margin: 10px 0; box-sizing: border-box;"><br>

            <div style="text-align: center; margin-top: 15px;">
                <button type="submit" style="padding: 10px 20px; background: #333; color: white; border: none; cursor: pointer;">Guardar Cambios</button>
                <a href="../tables.php?tabla=Products" style="margin-left: 15px; color: #333; text-decoration: none;">Cancelar</a>
            </div>
        </form>

        <?php
    } else {
        echo "<p style='text-align: center;'>No se encontró el producto en la base de datos.</p>";
    }
} else {
    echo "<p style='text-align: center;'>Faltan parámetros en la URL.</p>";
}

require_once '../footer.php';
?>