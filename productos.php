<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	session_start();
	if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
        header("location: login.php");
		exit;
        }

	/* Connect To Database*/
	require_once ("config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("config/conexion.php");//Contiene funcion que conecta a la base de datos
	
	$active_facturas="";
	$active_productos="active";
	$active_clientes="";
	$active_usuarios="";	
	$title="Productos | Order Tracking";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include("head.php");?>
  </head>
  <body>
	<?php
	include("navbar.php");
	?>
	
    <div class="container">
	<div class="panel panel-info">
		<div class="panel-heading">
		    <div class="btn-group pull-right">
				<!-- <button type='button' class="btn btn-info" data-toggle="modal" data-target="#nuevoProducto"><span class="glyphicon glyphicon-plus" ></span> Nuevo Producto</button> -->
			</div>
			<h4><i class='glyphicon glyphicon-search'></i> Buscar Productos</h4>
		</div>
		<div class="panel-body">
		
			
			
			<?php
			include("modal/registro_productos.php");
			include("modal/editar_productos.php");
			?>
			<form class="form-horizontal" role="form" id="datos_cotizacion">
			<div class="form-group row">
				<label for="q" class="col-md-2 control-label">Código o nombre</label>
				<div class="col-md-5">
					<input type="text" class="form-control" id="q" placeholder="Código o nombre del producto" onkeyup='load(1);'>
				</div>
			</div>

			<div class="form-group row">
				<label for="marcaSelect" class="col-md-2 control-label">Marca</label>
				<div class="col-md-5">
					<select class="form-control" id="marcaSelect" onchange="load(1);">
						<option value="">-- Todas las marcas --</option>
						<?php
						$sql_marca = "SELECT * FROM cji_marca";
						$query_marca = mysqli_query($con, $sql_marca);
						while ($row_marca = mysqli_fetch_array($query_marca)) {
							echo "<option value='" . $row_marca['MARCP_Codigo'] . "'>" . $row_marca['MARCC_Descripcion'] . "</option>";
						}
						?>
					</select>
				</div>
			</div> 
				<div class="form-group row">
							<!-- Selección del almacén en nueva_factura.php -->
						<label class="col-md-1 control-label">Almacen</label>
						<div class="col-md-2">
						<select class="form-control input-sm" id="id_almacen" onchange="load(1);">
							<option value="1">Seleccionar Almacén</option> <!-- Opción vacía si no se selecciona un almacén -->
							<?php
							$sqlAlmacen = "SELECT * FROM cji_almacen WHERE ALMAC_FlagEstado = 1 ORDER BY ALMAC_Descripcion";
							$almac = mysqli_query($con, $sqlAlmacen);
							$almacenSeleccionado = isset($_GET['almacen']) ? $_GET['almacen'] : ''; // Obtener el valor del almacén desde la URL
							while ($rw = mysqli_fetch_array($almac)) {
								$id_almacen = $rw["ALMAP_Codigo"];
								$desc_almacen = $rw["ALMAC_Descripcion"];
								$selected = ($id_almacen == $almacenSeleccionado) ? "selected" : ""; // Marcar como seleccionado el almacén de la URL
							?>
								<option value="<?php echo $id_almacen ?>" <?php echo $selected; ?>><?php echo $desc_almacen ?></option>
							<?php
							}
							?>
						</select>

						</div> 
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
	<script type="text/javascript" src="js/productos.js"></script>
  </body>
</html>
<script>
$( "#guardar_producto" ).submit(function( event ) {
  $('#guardar_datos').attr("disabled", true);
  
 var parametros = $(this).serialize();
	 $.ajax({
			type: "POST",
			url: "ajax/nuevo_producto.php",
			data: parametros,
			 beforeSend: function(objeto){
				$("#resultados_ajax_productos").html("Mensaje: Cargando...");
			  },
			success: function(datos){
			$("#resultados_ajax_productos").html(datos);
			$('#guardar_datos').attr("disabled", false);
			load(1);
		  }
	});
  event.preventDefault();
})

$( "#editar_producto" ).submit(function( event ) {
  $('#actualizar_datos').attr("disabled", true);
  
 var parametros = $(this).serialize();
	 $.ajax({
			type: "POST",
			url: "ajax/editar_producto.php",
			data: parametros,
			 beforeSend: function(objeto){
				$("#resultados_ajax2").html("Mensaje: Cargando...");
			  },
			success: function(datos){
			$("#resultados_ajax2").html(datos);
			$('#actualizar_datos').attr("disabled", false);
			load(1);
		  }
	});
  event.preventDefault();
})

	function obtener_datos(id){
			var codigo_producto = $("#codigo_producto"+id).val();
			var nombre_producto = $("#nombre_producto"+id).val();
			var detalle = $("#detalle"+id).val();
			var precio_costo = $("#precio_costo"+id).val();
			//var igv = $("#igv"+id).val();
			var medida = $("#medida"+id).val();



			var estado = $("#estado"+id).val();
			var precio_producto = $("#precio_producto"+id).val();
			$("#mod_id").val(id);
			$("#mod_codigo").val(codigo_producto);
			$("#mod_nombre").val(nombre_producto);
			$("#mod_detalle").val(detalle);
			$("#mod_precio").val(precio_producto);
			$("#mod_estado").val(estado);
			$("#mod_precio_costo").val(precio_costo);
			//$("#mod_igv").val(igv);
			$("#mod_medida").val(medida);



		}

   $("#limpiar").click(function() {
    // Limpiar los valores de los campos del formulario
    $("#marcaSelect").val("");  // Limpiar el campo de marca
    $("#q").val("");            // Limpiar el campo de búsqueda
    $("#id_almacen").val("1");  // Establecer el valor predeterminado del almacén
    
    // Variables con valores vacíos o predeterminados
    var q = $("#q").val();      // Capturar búsqueda (vacía)
    var precioVenta = $("#precioVenta").val();  // Asegúrate de que este campo exista
    var marca = $("#marcaSelect").val();        // Capturar marca (vacía)
    var almacen = $("#id_almacen").val();       // Capturar almacén (valor predeterminado)

    // Construir la URL con los parámetros
    var url = './ajax/buscar_productos.php?action=ajax&page=1&q=' + q + '&precioVenta=' + precioVenta + '&marca=' + marca + '&almacen=' + almacen;

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
           $('#dataproducto').html(response);
          //  $('#resultados').html('');
        }
    });
});


</script>