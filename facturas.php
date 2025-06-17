

<?php

$active_facturas = "active";
$active_productos = "";
$active_clientes = "";
$active_usuarios = "";
$title = "Pedido | Order Tracking";

require_once("config/db.php");
require_once("config/conexion.php");
include("funciones.php");

session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include("head.php"); ?>

</head>

<body>
	<?php
	include("navbar.php");
	?>
	<div class="container">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="btn-group pull-right">
					<a href="nueva_factura.php" class="btn btn-info"><span class="glyphicon glyphicon-plus"></span>
						Nuevo Pedido</a>
				</div>
				<h4>Buscar Pedidos</h4>
			</div>
			<div class="panel-body">
			<form class="form-horizontal" role="form" id="datos_cotizacion">
    <div class="form-group row">
        <div class="col-md-2">
            <label class="control-label" style="font-size: 18px;">Filtros de Búsqueda</label>
        </div>
        <br>
        <div class="col-md-2">
            <label class="control-label">Documento</label>
            <input type="text" class="form-control" id="documentoCliente" placeholder="Documento del cliente" onkeyup='load(1);'>
        </div>
        <div class="col-md-2">
            <label class="control-label">Nombre</label>
            <input type="text" class="form-control" id="nombreCliente" placeholder="Nombre del cliente" onkeyup='load(1);'>
        </div>
        <div class="col-md-2">
            <label>Inicio</label>
            <input type="date" class="form-control" id="fecha_inicio" onchange="load(1);">
        </div>
        <div class="col-md-2">
            <label>Fin</label>
            <input type="date" class="form-control" id="fecha_fin" onchange="load(1);">
        </div>  
        <div class="col-md-2">				
		<button type="button" class="btn btn-info btn-responsive" id="limpiar" name="limpiar">LIMPIAR</button>				
		</div>
        <!--<div class="col-md-2">
            <label>Estado</label>
            <select class="form-control" id="tipo_search" onchange="load(1);">
                <option value="A">Todas</option>
                <option value="2">Aprobado</option>
                <option value="1">Por Aprobar</option>
                <option value="0">Cancelado</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Encargado</label>
            <select class="form-control" id="encargado_search" onchange="load(1);">
                <option value="A">Todos</option>
                <?php
                $sql = "SELECT * FROM  users";
                $query = mysqli_query($con, $sql);
                while ($row = mysqli_fetch_array($query)) {
                    ?>
                    <option value="<?php echo $row["user_id"]; ?>">
                        <?php echo $row["firstname"] . " " . $row["lastname"]; ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="col-md-2">
            <label>Tipo Doc</label>
            <select class="form-control" id="doc_search" onchange="load(1);">
                <option value="A">Todos</option>
                <option value="3">Comprobante</option>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-2">
            <label>Paises</label>
            <select class="form-control" id="pais_search" onchange="load(1);">
                <option value="A">Todos</option>
                <?php
                $sql = "SELECT * FROM  pais";
                $query = mysqli_query($con, $sql);
                while ($row = mysqli_fetch_array($query)) {
                    ?>
                    <option value="<?php echo $row["id"]; ?>">
                        <?php echo $row["nombre"]; ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>-->

        <!-- Contenedor para alinear los botones a la derecha -->
        <div class="form-group row">
    <div class="col-md-12 text-center text-md-right">
        <!-- <button id="btn-excel" type="button" class="btn btn-primary me-2 mb-2">
            <img src="./img/excel.png" alt="Exportar Excel" style="width: 20px; height: 20px; margin-right: 5px;">
            Exportar Excel
        </button>
        <button id="btn-pdf" type="button" class="btn btn-danger mb-2">
            <img src="./img/pdf_salida.png" alt="Exportar PDF" style="width: 20px; height: 20px; margin-right: 5px;">
            Exportar PDF
        </button> -->
    </div>
</div>


    </div>
</form>

				<div id="resultados"></div><!-- Carga los datos ajax -->
				<div class='outer_div'></div><!-- Carga los datos ajax -->
			</div>
		</div>

	</div>
	<hr>
	<?php
	include("footer.php");
	?>
	<script type="text/javascript" src="js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="js/facturas.js"></script>
	<script>
	
	document.addEventListener('DOMContentLoaded', function () {
    // Botón para generar Excel
    document.getElementById('btn-excel').addEventListener('click', function (e) {
        e.preventDefault();
        generarExcel();
    });
});

function generarExcel() {
    const params = obtenerParametros();
    window.location.href = `phpexcel/documentos/ver_facturaf.php?${params}`;
}
function obtenerParametros() {
    const q = document.getElementById('q').value;
    const fecha_inicio = document.getElementById('fecha_inicio').value;
    const fecha_fin = document.getElementById('fecha_fin').value;
    const tipo_search = document.getElementById('tipo_search').value;
    const encargado_search = document.getElementById('encargado_search').value;
    const pais_search = document.getElementById('pais_search').value;

    return `fecha_inicio=${encodeURIComponent(fecha_inicio)}&fecha_fin=${encodeURIComponent(fecha_fin)}&estado_doc=${encodeURIComponent(tipo_search)}&encargado=${encodeURIComponent(encargado_search)}&pais=${encodeURIComponent(pais_search)}&nombre_cliente=${encodeURIComponent(q)}`;
}




document.addEventListener('DOMContentLoaded', function () {
    // Botón para generar Excel
    document.getElementById('btn-pdf').addEventListener('click', function (e) {
        e.preventDefault();
        generarPDF();
    });
});

function generarPDF() {
    const params = obtenerParametros();
    window.open(`TCPDF/documentos/reportefacturaf.php?${params}`, '_blank');
}

function obtenerParametros() {
    const q = document.getElementById('q').value;
    const fecha_inicio = document.getElementById('fecha_inicio').value;
    const fecha_fin = document.getElementById('fecha_fin').value;
    const tipo_search = document.getElementById('tipo_search').value;
    const encargado_search = document.getElementById('encargado_search').value;
    const pais_search = document.getElementById('pais_search').value;

    return `fecha_inicio=${encodeURIComponent(fecha_inicio)}&fecha_fin=${encodeURIComponent(fecha_fin)}&estado_doc=${encodeURIComponent(tipo_search)}&encargado=${encodeURIComponent(encargado_search)}&pais=${encodeURIComponent(pais_search)}&nombre_cliente=${encodeURIComponent(q)}`;
}

/* function limpiarFormulario() {
    document.getElementById("q").value = ""; // Limpiar texto
    document.getElementById("fecha_inicio").value = ""; // Limpiar fecha
    document.getElementById("fecha_fin").value = ""; // Limpiar fecha
    document.getElementById("tipo_search").selectedIndex = 0; // Seleccionar la primera opción
    document.getElementById("encargado_search").selectedIndex = 0; // Seleccionar la primera opción
    document.getElementById("doc_search").selectedIndex = 0; // Seleccionar la primera opción
    document.getElementById("pais_search").selectedIndex = 0; // Seleccionar la primera opción
} */



	   $("#limpiar").click(function() {
    // Limpiar los valores de los campos del formulario
    $("#documentoCliente").val("");            // Limpiar el campo de búsqueda
        $("#nombreCliente").val("");            // Limpiar el campo de búsqueda
        $("#fecha_inicio").val("");            // Limpiar el campo de búsqueda
        $("#fecha_fin").val("");            // Limpiar el campo de búsqueda

    // Variables con valores vacíos o predeterminados
    var documentoCliente = $("#documentoCliente").val();      // Capturar búsqueda (vacía)
    var nombreCliente = $("#nombreCliente").val();      // Capturar búsqueda (vacía)
    var fecha_inicio = $("#fecha_inicio").val();      // Capturar búsqueda (vacía)
    var fecha_fin = $("#fecha_fin").val();      // Capturar búsqueda (vacía)

    // Construir la URL con los parámetros
    // Construir la URL con parámetros vacíos (pero correctamente formateada)
    var url = './ajax/buscar_facturas.php?action=ajax&page=1' +
              '&documentoCliente=' + encodeURIComponent($("#documentoCliente").val()) +
              '&nombreCliente=' + encodeURIComponent($("#nombreCliente").val()) +
              '&fecha_inicio=' + encodeURIComponent($("#fecha_inicio").val()) +
              '&fecha_fin=' + encodeURIComponent($("#fecha_fin").val());
    // Mostrar cargador mientras se actualizan los resultados
    $("#loader").fadeIn('slow');

    // Realizar la solicitud AJAX
    $.ajax({
        url: url,  // Usar la URL construida
        beforeSend: function(objeto) {
            $('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');  // Mostrar mensaje de carga
        },
        success: function(data) {
            $(".outer_div").html(data).fadeIn('slow');  // Mostrar los resultados en la tabla
           $('#datapedidos').html(response);
          //  $('#resultados').html('');
        }
    });
});
</script>
</body>

</html>