<?php
require_once 'modynConnection.php';
require_once 'header.php';

if (!isset($_GET['tabla'])) {
    $res = mysqli_query($link, "SHOW TABLES");
    $count = mysqli_num_rows($res);

    echo "<h2>Tablas de Modyn_DB</h2>";
    echo "<p>Tablas encontradas: <strong>$count</strong></p>";
    echo "<table>";
    echo "<tr><th>#</th><th>Tabla</th><th>Ver datos</th></tr>";

    $i = 1;
    while ($row = mysqli_fetch_array($res)) {
        $nombre = $row[0];
        echo "<tr><td>$i</td><td>$nombre</td><td><a href='/modyn/tables.php?tabla=$nombre'>Ver</a></td></tr>";
        $i++;
    }
    echo "</table>";

    mysqli_close($link);
    require_once 'footer.php';
    exit;
}
