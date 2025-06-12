<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	session_start();
	$hoy = date('Y-m-d H:i:s');
	$cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
	$session_id = str_replace('.','',$cadena);
	if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
        header("location: login.php");
		exit;
    }
		
	$active_facturas="active";
	$active_productos="";
	$active_clientes="";
	$active_usuarios="";	
	$title="Nueva Venta | Order Tracking";
	$hoy = date("Y-m-d H:i:s");
	
	/* Connect To Database*/
	require_once ("config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("config/conexion.php");//Contiene funcion que conecta a la base de datos
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
			<h4><i class='glyphicon glyphicon-edit'></i> Nuevo Pedido</h4>
		</div>
		<div class="panel-body">
		<?php 
			include("modal/buscar_productos.php");
			include("modal/registro_clientes.php");
			include("modal/registro_productos.php");
		?>
			<form class="form-horizontal" role="form" id="datos_factura">
				<div class="form-group row">
				  <input type="hidden" id="hoy" name="hoy" value="<?=$hoy?>">
				  <label for="nombre_cliente" class="col-md-1 control-label">Cliente*</label>
				  <div class="col-md-3">
					  <input type="text" class="form-control input-sm" id="nombre_cliente" placeholder="Selecciona un cliente" required>
					  <input id="id_cliente" type='hidden'>	
				  </div>
				  <label for="ruc_cliente" class="col-md-1 control-label">RUC*</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" id="ruc_cliente" placeholder="RUC" readonly>
							</div>
					<!--<label for="mail" class="col-md-1 control-label">Email</label>
							<div class="col-md-3">
								<input type="text" class="form-control input-sm" id="mail" placeholder="Email" readonly>
							</div>-->
					<label for="direc_cliente" class="col-md-1 control-label">Direccion*</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" id="direc_cliente" placeholder="Direccion" readonly>
							</div> 
								<button type='button' class="btn btn-info" data-toggle="modal" data-target="#nuevoCliente"><span
							class="glyphicon glyphicon-plus"></span> Nuevo Cliente</button>	
							
				 </div>
						<div class="form-group row">
							<label for="empresa" class="col-md-1 control-label">Vendedor</label>
							<div class="col-md-3">
								<select class="form-control input-sm" id="id_vendedor">
									<?php
										$sqlVendedor = "SELECT d.DIREP_Codigo, p.PERSP_Codigo, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno
														FROM cji_directivo d
														INNER JOIN cji_persona p ON d.PERSP_Codigo = p.PERSP_Codigo
														INNER JOIN cji_cargo c ON d.CARGP_Codigo = c.CARGP_Codigo
														WHERE d.DIREC_FlagEstado = 1 AND c.COMPP_Codigo = 1 AND d.EMPRP_Codigo = 1 AND d.CARGP_Codigo = 2";
										$user=mysqli_query($con,$sqlVendedor);
										while ($rw=mysqli_fetch_array($user)){
											$id_vendedor=$rw["DIREP_Codigo"];
											$nombre_vendedor=$rw["PERSC_ApellidoPaterno"]." ".$rw["PERSC_ApellidoMaterno"]." ".$rw["PERSC_Nombre"];
											if ($id_vendedor==$_SESSION['DIREP_Codigo']){
												$selected="selected";
											} else {
												$selected="";
											}
											?>
											<option value="<?php echo $id_vendedor?>" <?php echo $selected;?>><?php echo $nombre_vendedor?></option>
											<?php
										}
									?>
								</select>
							</div>
								<div class="form-group row">
			<label class="col-md-1 control-label">Almacen</label>
			<div class="col-md-2">
				<select class="form-control input-sm" id="id_almacen">
					<?php
						$sqlAlmacen = "SELECT * FROM cji_almacen WHERE ALMAC_FlagEstado = 1 ORDER BY ALMAC_Descripcion";
						$almac=mysqli_query($con,$sqlAlmacen);
						while ($rw=mysqli_fetch_array($almac)){
							$id_almacen=$rw["ALMAP_Codigo"];
							$desc_almacen=$rw["ALMAC_Descripcion"];
							if ($id_almacen==1){
								$selected="selected";
							} else {
								$selected="";
							}
					?>
					<option value="<?php echo $id_almacen?>" <?php echo $selected;?>><?php echo $desc_almacen?></option>
					<?php
						}
					?>
				</select>
			</div>
			
		</div>  
							<label for="tel2" class="col-md-1 control-label">Rango de entrega</label>
							<div class="col-md-2">
								<input type="datetime-local" class="form-control input-sm" id="fechaEntMin" value="">
							</div>
							<label for="tel2" class="col-md-1 control-label"></label>
							<div class="col-md-2">
							<input type="datetime-local" class="form-control input-sm" id="fechaEntMax" value="">
							</div>
							
							<!--<label for="email" class="col-md-1 control-label">Pago</label>
							<div class="col-md-3">
								<select class='form-control input-sm' id="condiciones">
									<option value="1">Efectivo</option>
									<option value="2">Yape</option>
									<option value="3">Transferencia bancaria</option>
									<option value="4">Tarjeta de Cr��dito</option>
									<option value="5">Pendiente</option>
								</select>
							</div>-->
							
							<!--<label for="tipo_doc" class="col-md-1 control-label">Tipo Doc</label>
							<div class="col-md-3">
								<select class='form-control input-sm' id="tipo_doc">
									<option value="">Seleccione</option>
									<option value="1">Factura</option>
									<option value="2">Boleta</option>
									<option value="3">Comprobante</option>


								</select>
							</div>-->

							<!--<label for="MONED_Codigo" class="col-md-1 control-label">Moneda</label>
							<div class="col-md-3">
								<select class='form-control input-sm' id="MONED_Codigo">
									<option value="">Seleccione</option>
									<option value="1">Soles</option>
								</select>
							</div>
						</div>-->
				
				
				  <div class="col-md-12">
					<div class="pull-right">
						<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
						 <span class="glyphicon glyphicon-plus"></span> Agregar productos
						</button>
					</div>	
				</div>
			</form>	
			<div class="clearfix"></div>
		<div id="resultados" class='col-md-12' style="margin-top:10px"></div><!-- Carga los datos ajax -->			
		</div>
		<br>
	

		<div class="form-group row">
			<label class="col-md-1 control-label">Forma de Pago:</label>
			<div class="col-md-2">
				<select class="form-control input-sm" id="FormaPagoDefault" onchange="tipoForpap()">
					<?php
						$sqlFormaPago = "SELECT * FROM cji_formapago WHERE FORPAC_FlagEstado = 1 ORDER BY FORPAC_Descripcion";
						$forpap=mysqli_query($con,$sqlFormaPago);
						while ($rw=mysqli_fetch_array($forpap)){
							$id_forpap=$rw["FORPAP_Codigo"];
							$desc_forpap=$rw["FORPAC_Descripcion"];
							if ($id_forpap==1){
								$selected="selected";
							} else {
								$selected="";
							}
					?>
					<option value="<?php echo $id_forpap?>" <?php echo $selected;?>><?php echo $desc_forpap?></option>
					<?php
						}
					?>
				</select>
			</div>
			<br>
			<div class="col-md-3">
				<div id="Monto">
					<input type="number" class="form-control input-sm" id="montoDefault" placeholder="Monto">
				</div>
				<div id="Multiple" hidden>
					<button class="btn btn-default" data-toggle="modal" data-target="#ModalFormaPap" style="float:right; margin-right:10px;" onclick="load(1)">
						<span class="glyphicon glyphicon-plus"></span> Agregar
					</button>
				</div>
			</div>
		</div>
		<div class="form-group row">
			<label class="col-md-10	control-label" >OBSERVACIONES:</label>
		</div>
		<div>
			<textarea class="form-control input-sm" id="observacion" style="width:97%; height:100px; text-transform: uppercase;" rows="4"></textarea>
		</div>
		<br><br>
		<div>
			<a href="facturas.php" class="btn btn-danger" style="float:right;"><span class="glyphicon"></span>Cerrar</a>	
			<button class="btn btn-default" onclick="guardar('<?=$session_id?>')" style="float:right; margin-right:10px;">
				<span class="glyphicon glyphicon-print"></span> Crear Pedido
			</button>
		</div>		
		  <div class="row-fluid">
			<div class="col-md-12">			
			</div>	
		 </div>
	</div>
	<hr>
	
	<!-- MODAL FORMA PAGO MULTIPLE -->
	<div class="modal fade bs-example-modal-lg" id="ModalFormaPap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Agregar formas de pago</h4>
				  </div>
				  <div class="modal-body">
					<form class="form-horizontal">
					  <div class="form-group">
						<div class="col-sm-6">
						  <input type="hidden" id="session" value="<?=$session_id?>">
						</div>
						<!--<button type="button" class="btn btn-default" onclick="load(1)"><span class='glyphicon glyphicon-search'></span> Buscar</button>-->
					  </div>
					</form>
					<div id="loader" style="position: absolute;	text-align: center;	top: 55px;	width: 100%;display:none;"></div><!-- Carga gif animado -->
					<div>
					<div class="table-responsive">
						<table class="table" id="tableFormaPago">
							<tr class="warning">
								<th>Forma de pago</th>
								<th> <span class="pull-right">Monto</span></th>
								<th class='text-center' style="width: 36px;">Eliminar</th>
							</tr>
							<tr>
								<td>
									<select class="form-control input-sm FormaPagoModal" id="FormaPago[]" >
										<?php
											$sqlFormaPago = "SELECT * FROM cji_formapago WHERE FORPAC_FlagEstado = 1 AND NOT FORPAP_Codigo = 22 ORDER BY FORPAC_Descripcion";
											$forpap=mysqli_query($con,$sqlFormaPago);
											while ($rw=mysqli_fetch_array($forpap)){
												$id_forpap=$rw["FORPAP_Codigo"];
												$desc_forpap=$rw["FORPAC_Descripcion"];
												if ($id_forpap==1){
													$selected="selected";
												} else {
													$selected="";
												}
										?>
										<option value="<?php echo $id_forpap?>" <?php echo $selected;?> name="<?php echo $desc_forpap?>"><?php echo $desc_forpap?></option>
										<?php
											}
										?>
									</select>
								</td>
								<td>
									<div class="col-md-6 pull-right">
										<input type="number" class="form-control input-sm montoModal" id="monto[]" placeholder="Monto">
									</div>
								</td>
								<td class='text-center'><a href="#"><button type="button" class="btn btn-danger borrar"><i class="glyphicon glyphicon-trash"></i></button></a></td>
							</tr>
						</table>
						<button class="btn btn-default" style="float:right; margin-right:10px;" onclick="agregarFila()">
							<span class="glyphicon glyphicon-plus"></span> Agregar
						</button>
					</div>
					</div><!-- Datos ajax Final -->
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					
				  </div>
				</div>
			  </div>
			</div>
	<script type="text/javascript" src="js/clientes.js"></script>
	<?php
	include("footer.php");
	?>
	<script type="text/javascript" src="js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="js/nueva_factura.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script>
		$(function() {
						$("#nombre_cliente").autocomplete({
							source: "./ajax/autocomplete/clientes.php",
							minLength: 2,
							select: function(event, ui) {
								event.preventDefault();
								$('#id_cliente').val(ui.item.PERSP_Codigo);
								$('#nombre_cliente').val(ui.item.nombre_cliente);
								$('#ruc_cliente').val(ui.item.ruc_cliente);
								$('#direc_cliente').val(ui.item.direc_cliente);
																								
							 }
						});						 
					});
					
	$("#nombre_cliente" ).on( "keydown", function( event ) {
						if (event.keyCode== $.ui.keyCode.LEFT || event.keyCode== $.ui.keyCode.RIGHT || event.keyCode== $.ui.keyCode.UP || event.keyCode== $.ui.keyCode.DOWN || event.keyCode== $.ui.keyCode.DELETE || event.keyCode== $.ui.keyCode.BACKSPACE )
						{
							$("#id_cliente" ).val("");
							$("#ruc_cliente" ).val("");
							$('#direc_cliente').val(ui.item.direc_cliente);

											
						}
						if (event.keyCode==$.ui.keyCode.DELETE){
							$("#nombre_cliente" ).val("");
							$("#ruc_cliente" ).val("");
							$('#direc_cliente').val(ui.item.direc_cliente);

						}
			});
	</script>
	
  </body>
</html>