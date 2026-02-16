<?php
$archivo="datos/libro.json";
if(!file_exists($archivo)){file_put_contents($archivo,"[]");}

$datos=json_decode(file_get_contents($archivo),true);

/* CUENTAS (NO SE TOCA) */
$cuentas=[
1=>["codigo"=>"1101","nombre"=>"Efectivo y Equivalentes"],
2=>["codigo"=>"1103","nombre"=>"Cuentas por Cobrar Clientes"],
3=>["codigo"=>"1104","nombre"=>"IVA Crédito Fiscal"],
4=>["codigo"=>"1105","nombre"=>"Inventarios"],
5=>["codigo"=>"1201","nombre"=>"Propiedad, Planta y Equipo"],
6=>["codigo"=>"2101","nombre"=>"Cuentas por Pagar Proveedores"],
7=>["codigo"=>"2102","nombre"=>"IVA Débito Fiscal"],
8=>["codigo"=>"3101","nombre"=>"Capital Social"],
9=>["codigo"=>"4101","nombre"=>"Ventas"],
10=>["codigo"=>"4102","nombre"=>"Rebajas y Devoluciones/Ventas"],
11=>["codigo"=>"5101","nombre"=>"Compras"],
12=>["codigo"=>"5201","nombre"=>"Gastos de ventas"]
];

$totalDebe=0;
$totalHaber=0;
$saldoCaja=0;

/* TABLA */
echo "<table class='table table-striped table-bordered'>";
echo "<tr>
<th>#</th>
<th>Fecha</th>
<th>Concepto</th>
<th>Código</th>
<th>Cuenta</th>
<th>Debe</th>
<th>Haber</th>
</tr>";

$numPartida=1;

foreach($datos as $p){

 $cantidadMov = count($p['movimientos']);
 $primeraFila = true;

 foreach($p['movimientos'] as $m){

  $cuentaID=$m['cuenta'];

  $codigo=$cuentas[$cuentaID]['codigo'] ?? '---';
  $nombre=$cuentas[$cuentaID]['nombre'] ?? 'Desconocido';

  $debe=$m['debe'];
  $haber=$m['haber'];

  $totalDebe+=$debe;
  $totalHaber+=$haber;

  /* SALDO CAJA */
  if($cuentaID==1){
   $saldoCaja += $debe;
   $saldoCaja -= $haber;
  }

  $debeMostrar=$debe>0 ? "$".number_format($debe,2) : "";
  $haberMostrar=$haber>0 ? "$".number_format($haber,2) : "";

  echo "<tr>";

  /* SOLO LA PRIMERA FILA MUESTRA PARTIDA / FECHA / CONCEPTO */
  if($primeraFila){
   echo "<td rowspan='$cantidadMov'>$numPartida</td>";
   echo "<td rowspan='$cantidadMov'>{$p['fecha']}</td>";
   echo "<td rowspan='$cantidadMov'>{$p['concepto']}</td>";
   $primeraFila=false;
  }

  echo "<td>$codigo</td>
  <td>$nombre</td>
  <td>$debeMostrar</td>
  <td>$haberMostrar</td>
  </tr>";
 }

 $numPartida++;
}

/* FILA TOTAL */
echo "<tr style='font-weight:bold;background:#f3f4f6'>
<td colspan='5'>TOTALES</td>
<td style='color:#10b981'>$".number_format($totalDebe,2)."</td>
<td style='color:#7c3aed'>$".number_format($totalHaber,2)."</td>
</tr>";

echo "</table>";

/* SALDO OCULTO PARA JS (SIN COMAS) */
echo "<div data-saldo='".number_format($saldoCaja,2,'.','')."'></div>";
?>