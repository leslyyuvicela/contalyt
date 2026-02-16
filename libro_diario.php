<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contalyt - Libro Diario</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
body{
    font-family:'Segoe UI', Tahoma, sans-serif;
    background:#e5e7eb;
}

.container{
    max-width:1100px;
}

.header{
    background:linear-gradient(135deg,#7c3aed,#834AEF);
    color:white;
    padding:40px 0 60px 0;
}

.logo{
    width:160px;
}

.main-card{
    background:#f7f9fb;
    border-radius:20px;
    margin-top:30px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.card-inicio{
    background:white;
    border-radius:16px;
    padding:20px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

.btn-principal{
    background:#834AEF;
    color:white;
    font-weight:bold;
    border:none;
}

.btn-principal:hover{
    background:#6d35d8;
}

.saldo{
    font-size:22px;
    font-weight:bold;
    color:#10b981;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
  <div class="container text-center text-md-start">
    <img src="img/logo_blanco.png" class="logo mb-3">
    <h3>Libro Diario</h3>
    <p class="mb-0">Registra tus operaciones contables automáticamente.</p>
  </div>
</div>

<div class="container mt-4">

<!-- CAPITAL INICIAL -->
<div class="card-inicio mb-4">
  <h5 class="mb-3"><i class="bi bi-cash-coin"></i> Capital Inicial y Caja</h5>

  <div class="row g-3 align-items-center">
    <div class="col-md-5">
      <input id="inicio" class="form-control" placeholder="Ej: 50000">
    </div>

    <div class="col-md-3">
      <button onclick="iniciarEmpresa()" class="btn btn-success w-100">
        Iniciar Empresa
      </button>
    </div>

  </div>

  <small class="text-muted">
    Se generará automáticamente la partida de apertura (Efectivo vs Capital Social).
  </small>
</div>

<!-- LIBRO -->
<div class="main-card">
<h4>Registrar Operación</h4>

<input id="operacion" class="form-control" placeholder="Ej: Compra de mercadería $5000 al crédito">
<button onclick="enviarOperacion()" class="btn btn-principal mt-3">
Registrar Operación
</button>

<div id="libro" class="mt-4"></div>
</div>

</div>

<script>
function iniciarEmpresa(){
 let monto=document.getElementById("inicio").value;

 if(monto.trim()==="" || monto<=0){
   alert("Monto inválido");
   return;
 }

 fetch("generar_partida.php",{
 method:"POST",
 headers:{"Content-Type":"application/x-www-form-urlencoded"},
 body:"inicio="+monto
 }).then(r=>r.text()).then(()=>{
  document.getElementById("inicio").value="";
  cargarLibro();
 });
}

function enviarOperacion(){
 let texto=document.getElementById("operacion").value;

 if(texto.trim()===""){
   alert("Escribe una operación");
   return;
 }

 fetch("generar_partida.php",{
 method:"POST",
 headers:{"Content-Type":"application/x-www-form-urlencoded"},
 body:"texto="+encodeURIComponent(texto)
 }).then(r=>r.text()).then(()=>{
 document.getElementById("operacion").value="";
 cargarLibro();
 });
}

function cargarLibro(){
 fetch("libro.php")
 .then(r=>r.text())
 .then(html=>{
 document.getElementById("libro").innerHTML=html;

 /* EXTRAER SALDO DESDE libro.php */
 let saldoMatch=html.match(/data-saldo="([\d\.]+)"/);
 if(saldoMatch){
  document.getElementById("saldoCaja").innerText="$"+parseFloat(saldoMatch[1]).toFixed(2);
 }
 });
}

cargarLibro();
</script>

</body>
</html>
<?php
// Este espacio está intencionalmente vacío para evitar problemas con el servidor
?>