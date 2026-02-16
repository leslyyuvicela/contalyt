<?php
$conexion = new mysqli("localhost","root","","contalyt");
if($conexion->connect_error){
    die("Error de conexión");
}
?>