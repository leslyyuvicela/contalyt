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

    <h3>Calculadora de sueldo líquido en El Salvador</h3>
    <p class="mb-0">
      Ingresa tu sueldo bruto y calcula tu sueldo líquido basado en el tipo de salario seleccionado.
    </p>

  </div>
</div>

<!-- CONTENIDO -->
<div class="container py-4">
  <div class="main-card">
    <div class="row g-4 align-items-stretch">

      <!-- IZQUIERDA -->
      <div class="col-12 col-md-6">

        <label class="fw-bold text-dark">Sueldo base (USD)</label>
        <input type="text" id="sueldo" class="form-control input-custom mt-2" placeholder="$ 0.00">

        <label class="fw-bold text-dark mt-4">Tipo de sueldo</label>

        <div class="position-relative mt-2">
          <select id="tipo" class="form-control input-custom pe-5">
            <option value="mensual">Mensual</option>
            <option value="quincenal">Quincenal</option>
            <option value="semanal">Semanal</option>
          </select>

          <i class="bi bi-caret-down-fill position-absolute"
             style="right:15px; top:50%; transform:translateY(-50%); pointer-events:none; color:#6b7280;">
          </i>
        </div>

        <div class="text-center mt-4">
          <button id="btnCalcular" class="btn-calcular" disabled>Calcular</button>
        </div>

      </div>

      <!-- DERECHA -->
      <div class="col-12 col-md-6">
        <div class="card result-card p-4 h-100">

          <div class="d-flex align-items-center justify-content-center mb-3">
            <img src="img/calculator.png" width="35" class="me-2">
            <h5 class="fw-bold text-dark m-0">Resultado del cálculo</h5>
          </div>

          <div class="d-flex justify-content-between">
            <span>ISSS (3%):</span>
            <span id="isss" class="monto">$0.00</span>
          </div>

          <div class="d-flex justify-content-between">
            <span>AFP (7.25%):</span>
            <span id="afp" class="monto">$0.00</span>
          </div>

          <div class="d-flex justify-content-between">
            <span>Renta imponible:</span>
            <span id="renta" class="monto">$0.00</span>
          </div>

          <div class="d-flex justify-content-between">
            <span>ISR:</span>
            <span id="isr" class="monto">$0.00</span>
          </div>

          <hr>

          <div class="d-flex justify-content-between liquido">
            <span>Sueldo líquido:</span>
            <span id="liquido">$0.00</span>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
const sueldoInput = document.getElementById("sueldo");
const btn = document.getElementById("btnCalcular");

sueldoInput.addEventListener("input", () => {
    let valor = sueldoInput.value;

    // Solo números y punto
    valor = valor.replace(/[^0-9.]/g, '');
    sueldoInput.value = valor;

    let numero = parseFloat(valor);

    if(!isNaN(numero) && numero > 0){
        btn.disabled = false;
    }else{
        btn.disabled = true;
    }
});

btn.addEventListener("click", calcular);
document.getElementById("tipo").addEventListener("change", () => {
    if(!btn.disabled){
        calcular();
    }
});

function calcular(){
    let bruto = parseFloat(sueldoInput.value);

    if(isNaN(bruto) || bruto <= 0) return;

    let tipo = document.getElementById("tipo").value;

    let isss = bruto * 0.03;
    if(bruto > 1000) isss = 30;

    let afp = bruto * 0.0725;
    let renta = bruto - isss - afp;
    let isr = calcularISR(renta, tipo);
    let liquido = renta - isr;

    set("isss", isss);
    set("afp", afp);
    set("renta", renta);
    set("isr", isr);
    set("liquido", liquido);
}

function set(id, val){
    document.getElementById(id).innerText = "$" + val.toFixed(2);
}

function calcularISR(renta, tipo){
    if(tipo === "mensual"){
        if(renta <= 550) return 0;
        if(renta <= 895.24) return (renta-550)*0.10 + 17.67;
        if(renta <= 2038.10) return (renta-895.24)*0.20 + 60;
        return (renta-2038.10)*0.30 + 288.57;
    }

    if(tipo === "quincenal"){
        if(renta <= 275) return 0;
        if(renta <= 447.62) return (renta-275)*0.10 + 8.83;
        if(renta <= 1019.05) return (renta-447.62)*0.20 + 30;
        return (renta-1019.05)*0.30 + 144.28;
    }

    if(tipo === "semanal"){
        if(renta <= 137.5) return 0;
        if(renta <= 223.81) return (renta-137.5)*0.10 + 4.42;
        if(renta <= 509.52) return (renta-223.81)*0.20 + 15;
        return (renta-509.52)*0.30 + 72.14;
    }

    return 0; // ← IMPORTANTE
}
</script>

</body>
</html>