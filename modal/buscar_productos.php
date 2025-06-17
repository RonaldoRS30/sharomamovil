	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<?php
		if (isset($con))
		{
	
	?>	
			<!-- Modal -->
			<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Buscar productos</h4>
				  </div>
				  <div class="modal-body">
					<form class="form-horizontal">
					  <div class="form-group">
						<div class="col-sm-6">
						  <input type="hidden" id="session" value="<?=$session_id?>">
						  <input type="text" class="form-control" id="q" placeholder="Buscar productos" onkeyup="load(1)">
						</div>
						<label for="precioVenta" class="col-md-3 control-label">Precios:</label>
							<div class="col-md-3">
								<select class="form-control" id="precioVenta" onchange='load(1);'>
								<?php
										$sql = "SELECT * FROM cji_tipocliente WHERE TIPCLIC_FlagEstado = 1";
										$query = mysqli_query($con, $sql);
										$j=1;
										while($row=mysqli_fetch_array($query)){
											if(isset($_SESSION['Zona']) && $_SESSION['Zona']==$j){
												$select= "selected";
											}else{
												$select = "";
											}
												?>
												<option value="<?=$j?>" id="" <?=$select?>><?=$row['TIPCLIC_Descripcion']?></option>
											<?php
											$j++;
										}
									?>
								</select>
							</div>
							<label for="marcaSelect" class="col-md-3 control-label">Marca:</label>
							<div class="col-md-3">
						<p></p>
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
						<!--<button type="button" class="btn btn-default" onclick="load(1)"><span class='glyphicon glyphicon-search'></span> Buscar</button>-->
									  
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
					<div id="loader" style="position: absolute;	text-align: center;	top: 55px;	width: 100%;display:none;"></div><!-- Carga gif animado -->
					<div class="outer_div">
					</div><!-- Datos ajax Final -->
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					
				  </div>
				</div>
			  </div>
			</div>
	<?php
		}
		
	?>

	<script>
	$(document).ready(function() {
    console.log("Registrando el evento 'hidden.bs.modal'.");

    // Función de limpieza para reutilizar en diferentes lugares
    function limpiarCampos() {
        // Limpiar los valores de los campos del formulario
        $("#marcaSelect").val("");  // Limpiar el campo de marca
        $("#q").val("");            // Limpiar el campo de búsqueda
        $("#id_almacen").val("0");  // Establecer el valor predeterminado del almacén
        $("#precioVenta").val("1");  // Establecer el valor predeterminado del almacén

        // Variables con valores vacíos o predeterminados
        var q = $("#q").val();      // Capturar búsqueda (vacía)
        var precioVenta = $("#precioVenta").val();  // Asegúrate de que este campo exista
        var marca = $("#marcaSelect").val();        // Capturar marca (vacía)
        var almacen = $("#id_almacen").val();       // Capturar almacén (valor predeterminado)

        // Construir la URL con los parámetros
        var url = './ajax/productos_factura.php?action=ajax&page=1&q=' + q + '&precioVenta=' + precioVenta + '&marca=' + marca + '&almacen=' + almacen;

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
                $('#loader').html(''); // Limpiar cargador
            }
        });
    }

    // Ejecutar la limpieza cuando el botón de limpiar sea clickeado
    $("#limpiar").click(function() {
        console.log("Limpiar campos...");
        limpiarCampos(); // Llamar la función de limpieza
    });

    // Detectar cuando el modal se cierra
    $('#myModal').on('hidden.bs.modal', function () {
        console.log("El modal se ha cerrado.");
        limpiarCampos(); // Llamar la función de limpieza al cerrar el modal
    });
});


	</script>