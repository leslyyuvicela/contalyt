<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contalyt - Calculadora</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
body{
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background:#e5e7eb;
}

/* CONTENEDOR CENTRADO */
.container{
    max-width:1100px;
}

/* HEADER */
.header{
    background:linear-gradient(135deg,#7c3aed,#834AEF);
    color:white;
    padding:40px 0 60px 0;
}

.logo{
    width:160px;
    height:auto;
}

/* BLOQUE PRINCIPAL */
.main-card{
    background:#f7f9fb;
    border-radius:20px;
    margin-top:30px;
    padding:30px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

/* INPUTS */
.input-custom{
    border-radius:12px;
    border:1px solid #d1d5db;
    transition:0.3s;
    height:45px;
}

.input-custom:focus,
.input-custom:hover{
    border-color:#2dd4bf;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

/* BOTÓN */
.btn-calcular{
    background:#834AEF;
    color:white;
    font-weight:bold;
    border-radius:12px;
    padding:10px 30px;
    border:none;
}

.btn-calcular:disabled{
    background:#bfa8f5;
}

/* RESULTADO */
.result-card{
    border-radius:20px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

.monto{
    color:#2DD4BF;
    font-weight:bold;
}

.liquido{
    color:#834AEF;
    font-weight:bold;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
  <div class="container text-center text-md-start">

    <img src="img/logo_blanco.png" class="logo mb-3">

    <h3>Libro Diario</h3>
    <p class="mb-0">
      Ingresa tus operaciones y registra tus partidas en el libro diario automaticamente.
    </p>

  </div>
</div>

<div class="main-card mt-5">
<h4>Libro Diario Automático</h4>

<input id="operacion" class="form-control" placeholder="Escribe la operación...">
<button onclick="enviarOperacion()" class="btn btn-primary mt-3">
Registrar Operación
</button>

<div id="libro" class="mt-4"></div>
</div>

<script>

function enviarOperacion(){
 let texto = document.getElementById("operacion").value;

 fetch("generar_partida.php",{
 method:"POST",
 headers:{"Content-Type":"application/x-www-form-urlencoded"},
 body:"texto="+texto
 }).then(r=>r.text()).then(()=>{
 cargarLibro();
 });
}

function cargarLibro(){
 fetch("libro.php")
 .then(r=>r.text())
 .then(html=>{
 document.getElementById("libro").innerHTML = html;
 });
}

</script>

</body>
</html>