<?php
include 'conexion.php';

/* LIMPIAR TEXTO */
$textoOriginal = $_POST['texto'];
$texto = strtolower($textoOriginal);
$texto = str_replace(
['á','é','í','ó','ú','ñ'],
['a','e','i','o','u','n'],
$texto
);

/* EXTRAER MONTO */
$textoSinComas = str_replace(',', '', $texto);
preg_match('/\d+(\.\d+)?/', $textoSinComas, $matches);
$monto = isset($matches[0]) ? floatval($matches[0]) : 0;

$iva = $monto * 0.13;
$fecha = date('Y-m-d');
$concepto = $textoOriginal;

/* INSERTAR PARTIDA */
$conexion->query("INSERT INTO partidas(fecha,concepto) VALUES('$fecha','$concepto')");
$partida_id = $conexion->insert_id;

/* FUNCION MOVIMIENTO */
function mov($conexion,$partida,$cuenta,$debe,$haber){
 $conexion->query("INSERT INTO movimientos(partida_id,cuenta_id,debe,haber)
 VALUES($partida,$cuenta,$debe,$haber)");
}

/* PALABRAS CLAVE */
$esContado = strpos($texto,'contado') !== false || strpos($texto,'efectivo') !== false;
$esCredito = strpos($texto,'credito') !== false;
$esVenta = strpos($texto,'venta') !== false || strpos($texto,'vend') !== false;
$esCompra = strpos($texto,'compra') !== false;
$esMercaderia = strpos($texto,'merc') !== false || strpos($texto,'producto') !== false;
$esDevolucion = strpos($texto,'devol') !== false;
$esPagoProveedor = strpos($texto,'proveedor') !== false || strpos($texto,'deuda') !== false;
$esCobroCliente = strpos($texto,'recib') !== false || strpos($texto,'cobro') !== false;
$esPublicidad = strpos($texto,'public') !== false || strpos($texto,'radio') !== false;
$esApertura = strpos($texto,'aporte') !== false || strpos($texto,'inicia') !== false;

/* CUENTAS
1 Efectivo
2 Cuentas x Cobrar
3 IVA Credito
4 Inventario
5 Propiedad Planta
6 Cuentas x Pagar
7 IVA Debito
8 Capital
9 Ventas
11 Compras
12 Gastos Venta
*/

/* ========================= */
/* PRIORIDAD DE REGLAS */
/* ========================= */

$esVentaAnterior = 
 strpos($texto,'realizada') !== false ||
 strpos($texto,'anterior') !== false ||
 strpos($texto,'ejercicio') !== false;

/* 1 APERTURA */
if($esApertura){
 mov($conexion,$partida_id,1,$monto,0);
 mov($conexion,$partida_id,8,0,$monto);
}

/* 2 DEVOLUCION MERCADERIA / COMPRA */
elseif($esDevolucion){
 mov($conexion,$partida_id,6,$monto+$iva,0); // baja cuentas por pagar
 mov($conexion,$partida_id,11,0,$monto);     // baja inventario
 mov($conexion,$partida_id,3,0,$iva);       // baja iva credito
}

/* 3 COBRO A CLIENTES */
elseif(
 strpos($texto,'recib') !== false ||
 strpos($texto,'cobro') !== false
){
 mov($conexion,$partida_id,1,$monto,0); // Efectivo
 mov($conexion,$partida_id,2,0,$monto); // Cuentas x Cobrar
}

/* 4 PAGO A PROVEEDORES */
elseif(strpos($texto,'proveedor')!==false || strpos($texto,'deuda')!==false){
 mov($conexion,$partida_id,6,$monto,0);
 mov($conexion,$partida_id,1,0,$monto);
}

/* 5 COMPRA MOBILIARIO CONTADO */
elseif($esCompra && $esContado && (strpos($texto,'equipo')!==false || strpos($texto,'mobili')!==false)){
 mov($conexion,$partida_id,5,$monto,0);
 mov($conexion,$partida_id,3,$iva,0);
 mov($conexion,$partida_id,1,0,$monto+$iva);
}

/* 6 COMPRA MERCADERIA CREDITO */
elseif(($esCompra || $esMercaderia) && $esCredito){
 mov($conexion,$partida_id,11,$monto,0);
 mov($conexion,$partida_id,3,$iva,0);
 mov($conexion,$partida_id,6,0,$monto+$iva);
}

/* 7 VENTA CONTADO */
elseif($esVenta && $esContado && !$esCobro && !$esVentaAnterior){
 mov($conexion,$partida_id,1,$monto+$iva,0);
 mov($conexion,$partida_id,9,0,$monto);
 mov($conexion,$partida_id,7,0,$iva);
}

/* 8 VENTA CREDITO */
elseif($esVenta && $esCredito && !$esCobro && !$esVentaAnterior){
 mov($conexion,$partida_id,2,$monto+$iva,0);
 mov($conexion,$partida_id,9,0,$monto);
 mov($conexion,$partida_id,7,0,$iva);
}

/* 9 ALQUILER */
elseif($esAlquiler){
 mov($conexion,$partida_id,12,$monto,0);
 mov($conexion,$partida_id,3,$iva,0);
 mov($conexion,$partida_id,1,0,$monto+$iva);
}

/* 10 PUBLICIDAD */
elseif($esPublicidad){
 mov($conexion,$partida_id,12,$monto,0);
 mov($conexion,$partida_id,3,$iva,0);
 mov($conexion,$partida_id,1,0,$monto+$iva);
}

/* FALLBACK */
else{
 mov($conexion,$partida_id,1,$monto,0);
}

echo "ok";
?>