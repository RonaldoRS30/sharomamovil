<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	session_start();
	$hoy = date('Y-m-d H:i:s');
	$_POST['hola'] = 2;
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
	$title="Editar Documento | Order Tracking";
	$hoy = date("Y-m-d H:i:s");
	
	/* Connect To Database*/
	require_once ("config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("config/conexion.php");//Contiene funcion que conecta a la base de datos
	
	if (isset($_GET['id']))
	{
		$id_factura=intval($_GET['id']);
		$sql = "SELECT pe.*, 
				(CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Ruc ELSE p.PERSC_NumeroDocIdentidad end) as documento,
				(CASE c.CLIC_TipoPersona  WHEN '1'
					THEN e.EMPRC_RazonSocial
					ELSE CONCAT(p.PERSC_Nombre , ' ', p.PERSC_ApellidoPaterno, ' ', p.PERSC_ApellidoMaterno) end) nombre,
				(CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Direccion ELSE p.PERSC_Direccion end) as direccion
				FROM cji_pedido pe
				INNER JOIN cji_cliente c ON c.CLIP_Codigo = pe.CLIP_Codigo
				LEFT JOIN cji_persona p ON c.PERSP_Codigo = p.PERSP_Codigo
				LEFT JOIN cji_empresa e ON c.EMPRP_Codigo = e.EMPRP_Codigo
				WHERE pe.PEDIP_Codigo = '$id_factura'";
		$sql_factura=mysqli_query($con,$sql);
		$count=mysqli_num_rows($sql_factura);
		if ($count==1)
		{
				$rw_factura=mysqli_fetch_array($sql_factura);
				$id_cliente=$rw_factura['CLIP_Codigo'];
				$documento=$rw_factura['documento'];
				$direccion=$rw_factura['direccion'];
				$nombre_cliente=$rw_factura['nombre'];
				$id_vendedor_db=$rw_factura['PEDIC_Vendedor'];
				$id_almacen_db=$rw_factura['ALMAP_Codigo'];
				$forpapCodigo=$rw_factura['FORPAP_Codigo'];
				if($forpapCodigo == 22){
					$montoEdit = "";
				}else{
					$montoEdit = $rw_factura['FORPAP_Monto'];
				}
				$fecha_factura=date("d/m/Y", strtotime($rw_factura['PEDIC_FechaRegistro']));
				$fechaEntMin=$rw_factura['PEDIC_FechaEntregaMin'];
				$fechaEntMax=$rw_factura['PEDIC_FechaEntregaMax'];
				$estado_factura=$rw_factura['PEDIC_FlagEstado'];
				$numero_factura=$rw_factura['PEDIC_Numero'];
				$obs=$rw_factura['PEDIC_Observacion'];
				$_SESSION['id_factura']=$id_factura;
				$_SESSION['numero_factura']=$numero_factura;

				// if ($estado_factura==0 or $estado_factura==2) 
				// {
				// 	header("location: facturas.php");
				// 	exit;	
				// }
		}	
		else
		{
		}
	} 
	else 
	{
	}
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
			<h4><i class='glyphicon glyphicon-edit'></i> Editar Pedido</h4>
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
					  <input type="text" class="form-control input-sm" id="nombre_cliente" placeholder="Selecciona un cliente" required value="<?php echo $nombre_cliente;?>">
					  <input id="id_cliente" name="id_cliente" type='hidden' value="<?php echo $id_cliente;?>">	
				  </div>
				  <label for="ruc_cliente" class="col-md-1 control-label">RUC*</label>
				  <div class="col-md-2">
					  <input type="text" class="form-control input-sm" id="ruc_cliente" placeholder="RUC" readonly value="<?=$documento?>">
				  </div>
					<!--<label for="mail" class="col-md-1 control-label">Email</label>
							<div class="col-md-3">
								<input type="text" class="form-control input-sm" id="mail" placeholder="Email" readonly>
							</div>-->
				  <label for="direc_cliente" class="col-md-1 control-label">Dirección*</label>
				  <div class="col-md-2">
					  <input type="text" class="form-control input-sm" id="direc_cliente" placeholder="Dirección" readonly value="<?=$direccion?>">
				  </div>
				  
							<!-- <label for="email" class="col-md-1 control-label">Pago</label>
							<div class="col-md-2">
								<select class='form-control input-sm ' id="condiciones" name="condiciones">
									<option value="1" <?php if ($condiciones==1){echo "selected";}?>>Efectivo</option>
									<option value="2" <?php if ($condiciones==2){echo "selected";}?>>Yape</option>
									<option value="3" <?php if ($condiciones==3){echo "selected";}?>>Transferencia bancaria</option>
									<option value="4" <?php if ($condiciones==4){echo "selected";}?>>Tarjeta de Crédito</option>
									<option value="5" <?php if ($condiciones==5){echo "selected";}?>>Pendiente</option>
								</select>
							</div> -->
				  <!-- <label for="tel1" class="col-md-1 control-label">Teléfono</label>
							<div class="col-md-2">
								<input type="text" class="form-control input-sm" id="tel1" placeholder="Teléfono" value="<?php echo $telefono_cliente;?>" readonly>
							</div>
					<label for="mail" class="col-md-1 control-label">Email</label>
							<div class="col-md-3">
								<input type="text" class="form-control input-sm" id="mail" placeholder="Email" readonly value="<?php echo $email_cliente;?>">
							</div> -->
				 </div>
						<div class="form-group row">
							<label for="empresa" class="col-md-1 control-label">Vendedor</label>
							<div class="col-md-3">
								<select class="form-control input-sm" id="id_vendedor" name="id_vendedor">
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
											if ($id_vendedor==$id_vendedor_db){
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
							if ($id_almacen==$id_almacen_db){
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
								<input type="datetime-local" class="form-control input-sm" id="fechaEntMin" value="<?=$fechaEntMin?>">
							</div>
							<label for="tel2" class="col-md-1 control-label"></label>
							<div class="col-md-2">
							<input type="datetime-local" class="form-control input-sm" id="fechaEntMax" value="<?=$fechaEntMax?>">
							</div>
							<!-- <label for="moneda" class="col-md-1 control-label">Moneda</label>
								<div class="col-md-2">
									<select class='form-control input-sm' id="moneda" name="moneda">
										<option value="1" <?php if ($condiciones==1){echo "selected";}?>>Soles</option>
									</select>
								</div> -->
							</div>
				<div class="col-md-12">
					<div class="pull-right">
						<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
						 <span class="glyphicon glyphicon-plus"></span> Agregar productos
						</button>
					</div>	
				</div>
				<br>
		
			</form>	
			<div class="clearfix"></div>
				<div class="editar_factura" class='col-md-12' style="margin-top:10px"></div><!-- Carga los datos ajax -->	
			
		<div id="resultados" class='col-md-12' style="margin-top:10px"></div><!-- Carga los datos ajax -->
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
							if ($id_forpap==$forpapCodigo){
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
					<input type="number" class="form-control input-sm" id="montoDefault" placeholder="Monto" value="<?=$montoEdit?>">
				</div>
				<div id="Multiple" hidden>
					<button class="btn btn-default" data-toggle="modal" data-target="#ModalFormaPap" style="float:right; margin-right:10px;" onclick="load(1)">
						<span class="glyphicon glyphicon-plus"></span> Agregar
					</button>
				</div>
			</div>
		</div>	
		<div>
			<label class="col-md-1 control-label" >OBSERVACIONES:</label>
			<textarea class="form-control input-sm" id="observacion" style="width:97%; height:100px; text-transform: uppercase;" rows="4" val="<?=$obs?>"><?=$obs?></textarea>
		</div>
		<br><br>
			<a href="facturas.php" class="btn btn-danger" style="float:right;"><span class="glyphicon"></span>Cerrar</a>		
			<button class="btn btn-default" onclick="editar('<?=$session_id?>')" style="float:right; margin-right:10px;">
				<span class="glyphicon glyphicon-print"></span> Actualizar Pedido
			</button>
		</div>
	</div>
			
		 
	</div>
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
							<?php if($forpapCodigo != 22){?>
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
								<td class='text-center'><a href="#" onclick="eliminar('<?=$id_tmp ?>','<?=$session?>')"><button type="button" class="btn btn-danger delete"><i class="glyphicon glyphicon-trash"></i></button></a></td>
							</tr>
							<?php }else{ 
								$sqlEditPago = "SELECT p.* FROM cji_pedido_formaspago p WHERE p.PEDIP_Codigo = $id_factura AND p.pedi_forPa_flag = 1";
								$forpapEdit=mysqli_query($con,$sqlEditPago);
								while ($rwEdit=mysqli_fetch_array($forpapEdit)){
									$id_forpapEdit=$rwEdit["FORPAP_Codigo"];?>
							<tr>
								<td>
									<select class="form-control input-sm FormaPagoModal" id="FormaPago[]" >
										<?php
											$sqlFormaPago = "SELECT * FROM cji_formapago WHERE FORPAC_FlagEstado = 1 AND NOT FORPAP_Codigo = 22 ORDER BY FORPAC_Descripcion";
											$forpap=mysqli_query($con,$sqlFormaPago);
											while ($rw=mysqli_fetch_array($forpap)){
												$id_forpap=$rw["FORPAP_Codigo"];
												$desc_forpap=$rw["FORPAC_Descripcion"];
												if ($id_forpap==$id_forpapEdit){
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
										<input type="number" class="form-control input-sm montoModal" id="monto[]" placeholder="Monto" value="<?=$rwEdit["monto"]?>">
									</div>
								</td>
								<td class='text-center'><a href="#"><button type="button" class="btn btn-danger borrar"><i class="glyphicon glyphicon-trash"></i></button></a></td>
							</tr>
							<?php } 
							}?>
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
	<hr>
	<?php
	include("footer.php");
	?>
	<script type="text/javascript" src="js/VentanaCentrada.js"></script>
	<script type="text/javascript" src="js/editar_factura.js"></script>
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
								$('#tel1').val(ui.item.telefono_cliente);
								$('#mail').val(ui.item.email_cliente);
								$('#ruc_cliente').val(ui.item.ruc_cliente);
								$('#direc_cliente').val(ui.item.direc_cliente);	
								
							 }
						});
						 
						
					});
					
	$("#nombre_cliente" ).on( "keydown", function( event ) {
						if (event.keyCode== $.ui.keyCode.LEFT || event.keyCode== $.ui.keyCode.RIGHT || event.keyCode== $.ui.keyCode.UP || event.keyCode== $.ui.keyCode.DOWN || event.keyCode== $.ui.keyCode.DELETE || event.keyCode== $.ui.keyCode.BACKSPACE )
						{
							$("#id_cliente" ).val("");
							$("#tel1" ).val("");
							$("#mail" ).val("");
											
						}
						if (event.keyCode==$.ui.keyCode.DELETE){
							$("#nombre_cliente" ).val("");
							$("#id_cliente" ).val("");
							$("#tel1" ).val("");
							$("#mail" ).val("");
						}
			});	
	</script>

  </body>
</html>