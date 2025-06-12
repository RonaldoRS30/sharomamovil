<?php
if (isset($con)) {
	?>
	<!-- Modal -->
	<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
							aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Editar cliente</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" method="post" id="editar_cliente" name="editar_cliente">
						<div id="resultados_ajax2"></div>
						<input type="hidden" name="mod_tipo" id="mod_tipo">
						<input type="hidden" name="mod_idSec" id="mod_idSec">
						<div id="divNomMod">
							<div class="form-group">
								<label for="mod_nombre" class="col-sm-3 control-label">Nombres</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="mod_nombres" name="mod_nombres" required>
								</div>
							</div>
							<div class="form-group">
								<label for="mod_nombre" class="col-sm-3 control-label">Apellido paterno</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="mod_apepa" name="mod_apepa" required>
								</div>
							</div>
							<div class="form-group">
								<label for="mod_nombre" class="col-sm-3 control-label">Apellido paterno</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="mod_apema" name="mod_apema" required>
								</div>
							</div>
						</div>

						<div id="divRucMod">
							<div class="form-group">
								<label for="mod_nombre" class="col-sm-3 control-label">Razón Social</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" id="mod_razon" name="mod_razon" required>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="mod_ruc" class="col-sm-3 control-label">Documento</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" id="mod_ruc" name="mod_ruc">
							</div>
							<input type="hidden" name="mod_id" id="mod_id">
						</div>
						<div class="form-group">
							<label for="mod_telefono" class="col-sm-3 control-label">Teléfono</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" id="mod_telefono" name="mod_telefono">
							</div>
						</div>

						<div class="form-group">
							<label for="mod_email" class="col-sm-3 control-label">Email</label>
							<div class="col-sm-8">
								<input type="email" class="form-control" id="mod_email" name="mod_email">
							</div>
						</div>
<!-- 						<div class="form-group">
							<label for="email" class="col-sm-3 control-label">País</label>
							<div class="col-sm-8">
								<select class="form-control" id="mod_pais" name="mod_pais" required>
									<option value="">-- Selecciona --</option>
									<?php
									// Realizar una consulta MySQL
									$query = 'SELECT * FROM pais';
									$data = mysqli_query($con, $query);
									// Iterar sobre los resultados de la consulta
									while ($row_data = mysqli_fetch_array($data)) {
										?>
										<option value="<?php echo $row_data["id"]; ?>">
											<?php echo $row_data["nombre"]; ?>
										</option>
										<?php
									}
									?>
								</select>
							</div>
						</div> -->
						<div class="form-group">
							<label for="mod_direccion" class="col-sm-3 control-label">Dirección</label>
							<div class="col-sm-8">
								<textarea class="form-control" id="mod_direccion" name="mod_direccion"></textarea>
							</div>
						</div>

						<div class="form-group">
							<label for="mod_estado" class="col-sm-3 control-label">Estado</label>
							<div class="col-sm-8">
								<select class="form-control" id="mod_estado" name="mod_estado" required>
									<option value="">-- Selecciona estado --</option>
									<option value="1" selected>Activo</option>
									<option value="0">Inactivo</option>
								</select>
							</div>
						</div>




				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-primary" id="actualizar_datos">Actualizar datos</button>
				</div>
				</form>
			</div>
		</div>
	</div>
	<?php
}
?>