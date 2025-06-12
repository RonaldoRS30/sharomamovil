<?php
session_start();
if (!isset($_SESSION['user_login_status']) || $_SESSION['user_login_status'] != 1) {
    header("location: login.php");
    exit;
}

/* Conexión a la Base de Datos */
require_once("config/db.php");
require_once("config/conexion.php");

$active_facturas = "";
$active_productos = "";
$active_clientes = "";
$active_usuarios = "";
$active_reportes = "active";

$title = "Reportes | Order Tracking";

// Re-execute client query to ensure fresh result set
$sql_clientes = mysqli_query($con, "SELECT id_cliente, nombre_cliente FROM clientes");

// Obtener los estados posibles
$estados = [
    "1" => "Pagado",
    "0" => "Cancelada",
    "2" => "Aprobado",
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include("head.php"); ?>
    <script>
        // Función para limpiar el formulario
        function limpiarFormulario(formId) {
            var form = document.getElementById(formId);
            form.querySelector('input[name="fecha_inicio"]').value = "";
            form.querySelector('input[name="fecha_fin"]').value = "";
            form.querySelector('select[name="estado_doc"]').selectedIndex = 0;
            form.querySelector('select[name="id_cliente"]').selectedIndex = 0;
        }
    </script>
</head>
<body>
    <?php include("navbar.php"); ?>

    <div class="container">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h4><i class='glyphicon glyphicon-search'></i> Crear Reportes</h4>
            </div>
            
            <div class="panel-body">
                <!-- Card para Reporte de Excel -->
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h5><i class="glyphicon glyphicon-file"></i> Reporte de Excel</h5>
                    </div>
                    <div class="card-body">
                        <form id="excelForm" action="phpexcel/documentos/ver_factura.php" method="get" target="_blank">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="fecha_inicial_excel">Fecha Inicial:</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicial_excel" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_final_excel">Fecha Final:</label>
                                    <input type="date" name="fecha_fin" id="fecha_final_excel" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label for="estado_doc">Estado:</label>
                                    <select name="estado_doc" id="estado_doc_excel" class="form-control">
                                        <option value="">Todos</option>
                                        <?php 
                                        mysqli_data_seek($sql_clientes, 0);  // Reset client query
                                        foreach ($estados as $key => $value): ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
    <label for="cliente_excel">Cliente:</label>
    <select name="id_cliente" id="cliente_excel" class="form-control">
        <option value="">Todos</option>
        <?php 
        $sql_clientes = mysqli_query($con, "SELECT id_cliente, nombre_cliente FROM clientes WHERE status_cliente = 1");
        while ($row = mysqli_fetch_assoc($sql_clientes)): ?>
            <option value="<?php echo $row['id_cliente']; ?>">
                <?php echo $row['nombre_cliente']; ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-success">
                                        <i class="glyphicon glyphicon-file"></i> Generar Excel
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="limpiarFormulario('excelForm')">
                                        <i class="glyphicon glyphicon-refresh"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card para Reporte de PDF -->
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5><i class="glyphicon glyphicon-stats"></i> Reporte de PDF</h5>
                    </div>
                    <div class="card-body">
                        <form id="pdfForm" action="TCPDF/documentos/reportefactura.php" method="get" target="_blank">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="fecha_inicial_pdf">Fecha Inicial:</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicial_pdf" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label for="fecha_final_pdf">Fecha Final:</label>
                                    <input type="date" name="fecha_fin" id="fecha_final_pdf" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label for="estado_doc_pdf">Estado:</label>
                                    <select name="estado_doc" id="estado_doc_pdf" class="form-control">
                                        <option value="">Todos</option>
                                        <?php 
                                        $sql_clientes = mysqli_query($con, "SELECT id_cliente, nombre_cliente FROM clientes");
                                        foreach ($estados as $key => $value): ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
    <label for="cliente_excel">Cliente:</label>
    <select name="id_cliente" id="cliente_excel" class="form-control">
        <option value="">Todos</option>
        <?php 
        $sql_clientes = mysqli_query($con, "SELECT id_cliente, nombre_cliente FROM clientes WHERE status_cliente = 1");
        while ($row = mysqli_fetch_assoc($sql_clientes)): ?>
            <option value="<?php echo $row['id_cliente']; ?>">
                <?php echo $row['nombre_cliente']; ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="glyphicon glyphicon-file"></i> Generar PDF
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="limpiarFormulario('pdfForm')">
                                        <i class="glyphicon glyphicon-refresh"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <?php include("footer.php"); ?>
</body>
</html>