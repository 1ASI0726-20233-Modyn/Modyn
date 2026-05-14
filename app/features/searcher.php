<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir conexión a BD (ruta relativa desde features/)
require_once '../modynConnection.php';


$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($search !== '') {
    $safe = mysqli_real_escape_string($link, $search);
    $sql = "SELECT PRO_id, PRO_name, PRO_description, PRO_price
            FROM Products
            WHERE PRO_id LIKE '%$safe%'
               OR PRO_name LIKE '%$safe%'
               OR PRO_description LIKE '%$safe%'
            ORDER BY PRO_id ASC";
} else {
    $sql = "SELECT PRO_id, PRO_name, PRO_description, PRO_price
            FROM Products
            ORDER BY PRO_id ASC";
}

$result  = mysqli_query($link, $sql);
$total   = $result ? mysqli_num_rows($result) : 0;


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modyn Database</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/searcher.css">
</head>
<body>

<!-- Título principal -->
<h1>MODYN</h1>

<!-- Navegación principal (igual que header.php + link al catálogo) -->
<nav>
    <a href="../index.php">Inicio</a>
    <a href="../tables.php">Tablas</a>
    <a href="searcher.php">Catálogo</a>
</nav>

<hr>

<!-- ============================================ -->
<!-- CONTENIDO DEL CATÁLOGO                      -->
<!-- ============================================ -->
<div class="searcher-wrapper">

    <h2>Catálogo de Productos</h2>

    <!-- Barra de búsqueda -->
    <form method="GET" action="searcher.php">
        <div class="searcher-bar">
            <input
                type="text"
                name="q"
                placeholder="Buscar por ID, nombre o descripción..."
                value="<?php echo htmlspecialchars($search); ?>"
                autocomplete="off"
            >
            <button type="submit">Buscar</button>
        </div>
    </form>

    <!-- Contador de resultados -->
    <p class="searcher-count">
        <?php if ($search !== ''): ?>
            <?php echo $total; ?> resultado<?php echo $total !== 1 ? 's' : ''; ?> para
            "<strong><?php echo htmlspecialchars($search); ?></strong>"
            — <a href="searcher.php">Limpiar búsqueda</a>
        <?php else: ?>
            <?php echo $total; ?> producto<?php echo $total !== 1 ? 's' : ''; ?> en catálogo
        <?php endif; ?>
    </p>

    <!-- Grilla de cards -->
    <div class="products-grid">

        <?php if ($total > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="product-card">
                    <span class="card-id"># <?php echo htmlspecialchars($row['PRO_id']); ?></span>
                    <span class="card-name"><?php echo htmlspecialchars($row['PRO_name']); ?></span>
                    <span class="card-desc"><?php echo htmlspecialchars($row['PRO_description']); ?></span>
                    <span class="card-price">S/ <?php echo number_format($row['PRO_price'], 2); ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-results">No se encontraron productos para tu búsqueda.</p>
        <?php endif; ?>

    </div><!-- /products-grid -->

</div><!-- /searcher-wrapper -->

<?php
mysqli_close($link);
require_once '../footer.php';
?>
