<?php

$archivo = "datos/libro.json";

/* CREAR JSON SI NO EXISTE */
if(!file_exists($archivo)){
  file_put_contents($archivo, "[]");
}

/* ========================= */
/* INICIALIZAR EMPRESA */
/* ========================= */
if(isset($_POST['inicio'])){
 $monto = floatval($_POST['inicio']);
 if($monto <= 0){
  echo "monto_error";
  exit;
 }

 $fecha = date('Y-m-d');

 $partida = [
  "fecha"=>$fecha,
  "concepto"=>"Apertura de empresa",
  "movimientos"=>[
   ["cuenta"=>1,"debe"=>$monto,"haber"=>0],
   ["cuenta"=>8,"debe"=>0,"haber"=>$monto]
  ]
 ];

 $datos = json_decode(file_get_contents($archivo), true);
 $datos[]=$partida;
 file_put_contents($archivo,json_encode($datos,JSON_PRETTY_PRINT));

 echo "ok";
 exit;
}

/* ========================= */
/* LIMPIAR TEXTO */
/* ========================= */
$textoOriginal = $_POST['texto'] ?? "";
$texto = strtolower($textoOriginal);
$texto = str_replace(
['á','é','í','ó','ú','ñ'],
['a','e','i','o','u','n'],
$texto
);

/* ========================= */
/* EXTRAER MONTO */
/* ========================= */
$textoSinComas = str_replace(',', '', $texto);
preg_match('/\d+(\.\d+)?/', $textoSinComas, $matches);
$monto = isset($matches[0]) ? floatval($matches[0]) : 0;

/* VALIDAR MONTO */
if($monto <= 0){
 echo "monto_error";
 exit;
}

$iva = $monto * 0.13;
$fecha = date('Y-m-d');

/* CARGAR JSON */
$datos = json_decode(file_get_contents($archivo), true);

/* CREAR PARTIDA */
$partida = [
  "fecha" => $fecha,
  "concepto" => $textoOriginal,
  "movimientos" => []
];

/* FUNCION MOVIMIENTO */
function mov(&$partida,$cuenta,$debe,$haber){
  $partida["movimientos"][] = [
    "cuenta"=>$cuenta,
    "debe"=>$debe,
    "haber"=>$haber
  ];
}

/* ========================= */
/* PALABRAS CLAVE */
/* ========================= */
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

$esAlquiler = strpos($texto,'alquiler') !== false || strpos($texto,'renta') !== false;
$esPagare = strpos($texto,'pagare') !== false || strpos($texto,'pagaré') !== false;

$esVentaAnterior =
 strpos($texto,'realizada') !== false ||
 strpos($texto,'anterior') !== false ||
 strpos($texto,'ejercicio') !== false;

/* ========================= */
/* PRIORIDAD DE REGLAS */
/* ========================= */

/* 1 APERTURA */
if($esApertura){
 mov($partida,1,$monto,0);
 mov($partida,8,0,$monto);
}

/* 2 DEVOLUCION */
elseif($esDevolucion){
 mov($partida,6,$monto+$iva,0);
 mov($partida,11,0,$monto);
 mov($partida,3,0,$iva);
}

/* 3 COBRO CLIENTES */
elseif($esCobroCliente){
 mov($partida,1,$monto,0);
 mov($partida,2,0,$monto);
}

/* 4 PAGO PROVEEDORES */
elseif($esPagoProveedor){
 mov($partida,6,$monto,0);
 mov($partida,1,0,$monto);
}

/* 5 COMPRA MOBILIARIO CONTADO */
elseif($esCompra && $esContado && (strpos($texto,'equipo')!==false || strpos($texto,'mobili')!==false)){
 mov($partida,5,$monto,0);
 mov($partida,3,$iva,0);
 mov($partida,1,0,$monto+$iva);
}

/* 6 COMPRA MERCADERIA CREDITO */
elseif(($esCompra || $esMercaderia) && $esCredito){
 mov($partida,11,$monto,0);
 mov($partida,3,$iva,0);
 mov($partida,6,0,$monto+$iva);
}

/* 7 VENTA CON PAGARE */
elseif($esVenta && $esPagare){
 mov($partida,2,$monto+$iva,0);
 mov($partida,9,0,$monto);
 mov($partida,7,0,$iva);
}

/* 8 ALQUILER */
elseif($esAlquiler){
 mov($partida,12,$monto,0);
 mov($partida,3,$iva,0);
 mov($partida,1,0,$monto+$iva);
}

/* 9 VENTA CONTADO */
elseif($esVenta && $esContado && !$esVentaAnterior){
 mov($partida,1,$monto+$iva,0);
 mov($partida,9,0,$monto);
 mov($partida,7,0,$iva);
}

/* 10 VENTA CREDITO */
elseif($esVenta && $esCredito && !$esVentaAnterior){
 mov($partida,2,$monto+$iva,0);
 mov($partida,9,0,$monto);
 mov($partida,7,0,$iva);
}

/* 11 PUBLICIDAD */
elseif($esPublicidad){
 mov($partida,12,$monto,0);
 mov($partida,3,$iva,0);
 mov($partida,1,0,$monto+$iva);
}

/* FALLBACK */
else{
 mov($partida,1,$monto,0);
}

/* ========================= */
/* GUARDAR */
/* ========================= */
$datos[] = $partida;
file_put_contents($archivo, json_encode($datos, JSON_PRETTY_PRINT));

echo "ok";
?>