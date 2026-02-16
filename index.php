<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Contalyt</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
  background:#e5e7eb;
  font-family:'Segoe UI';
}

/* TARJETAS */
.card-custom{
  border:none;
  border-radius:20px;
  background:white;
  transition:0.3s;
  box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

.card-custom:hover{
  transform:translateY(-6px);
  box-shadow:0 12px 25px rgba(0,0,0,0.15);
}

/* BOTONES */
.btn-contalyt{
  background:#42C2A1;
  color:white;
  border-radius:30px;
  padding:10px 25px;
  border:none;
  font-weight:600;
  transition:0.3s;
}

.btn-contalyt:hover{
  background:#552BBE;
  color:white;
}
</style>

</head>

<body>

<div class="header text-center" style="background:linear-gradient(135deg,#7c3aed,#834AEF); color:white; padding:40px;">
  <img src="img/logo_blanco.png" width="160">
  <h3 class="mt-3">Sistema Contable Básico</h3>
</div>

<div class="container mt-5">
  <div class="row text-center g-4">

    <div class="col-md-6">
      <div class="card-custom p-4">
        <h4>💼 Calculadora de Sueldo</h4>
        <a href="calculadora.php" class="btn btn-contalyt mt-3">Entrar</a>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card-custom p-4">
        <h4>📘 Libro Diario</h4>
        <a href="libro_diario.php" class="btn btn-contalyt mt-3">Entrar</a>
      </div>
    </div>

  </div>
</div>

</body>
</html>