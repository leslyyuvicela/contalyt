<?php
include 'conexion.php';

$partidas = $conexion->query("SELECT * FROM partidas ORDER BY id ASC");

while($p = $partidas->fetch_assoc()){
echo "<h5>Partida #".$p['id']."</h5>";
echo "<p>".$p['fecha']." - ".$p['concepto']."</p>";

echo "<table class='table'>";
echo "<tr><th>Codigo</th><th>Cuenta</th><th>Debe</th><th>Haber</th></tr>";

$movs = $conexion->query("
SELECT m.*, c.codigo, c.nombre 
FROM movimientos m
JOIN cuentas c ON m.cuenta_id = c.id
WHERE partida_id=".$p['id']);

while($m = $movs->fetch_assoc()){
echo "<tr>
<td>{$m['codigo']}</td>
<td>{$m['nombre']}</td>
<td>{$m['debe']}</td>
<td>{$m['haber']}</td>
</tr>";
}

echo "</table><hr>";
}
?>