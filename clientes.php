<?php
/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
session_start();
if (!isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] != 1) {
	header("location: login.php");
	exit;
}

/* Connect To Database*/
require_once("config/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
require_once("config/conexion.php"); //Contiene funcion que conecta a la base de datos

$active_facturas = "";
$active_productos = "";
$active_clientes = "active";
$active_usuarios = "";
$title = "Clientes | Order Tracking";

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
					<button type='button' class="btn btn-info" data-toggle="modal" data-target="#nuevoCliente"><span
							class="glyphicon glyphicon-plus"></span> Nuevo Cliente</button>
				</div>
				<h4><i class='glyphicon glyphicon-search'></i> Buscar Clientes</h4>
			</div>
			<div class="panel-body">

				<?php
				include("modal/registro_clientes.php");
				include("modal/editar_clientes.php");
				?>
				<form class="form-horizontal" role="form" id="datos_cotizacion">

					<div class="form-group row">
						<label for="q" class="col-md-2 control-label">Cliente</label>
						<div class="col-md-5">
							<input type="text" class="form-control" id="q" placeholder="Nombre o ruc del cliente"
								onkeyup='load(1);'>
						</div>
						<!-- <div class="col-md-3">
							<button type="button" class="btn btn-default" onclick='load(1);'>
								<span class="glyphicon glyphicon-search"></span> Buscar</button>
							<span id="loader"></span>
						</div> -->
	<style>
				@media (max-width: 767px) {
					.btn-responsive {
					display: block;
					width: 30%;
					margin: 10px auto;
					font-size: 16px;
					max-width: 150px; /* para que no sea demasiado ancho */
					}
				}
				</style>
			<button type="button" class="btn btn-info btn-responsive" id="limpiar" name="limpiar">LIMPIAR</button>
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
	<script type="text/javascript" src="js/clientes.js"></script>
</body>

</html> 

<script>
	   $("#limpiar").click(function() {
    // Limpiar los valores de los campos del formulario
    $("#q").val("");            // Limpiar el campo de búsqueda
    
    // Variables con valores vacíos o predeterminados
    var q = $("#q").val();      // Capturar búsqueda (vacía)

    // Construir la URL con los parámetros
    var url = './ajax/buscar_clientes.php?action=ajax&page=1&q=' + q;

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
           $('#datacliente').html(response);
          //  $('#resultados').html('');
        }
    });
});
</script>