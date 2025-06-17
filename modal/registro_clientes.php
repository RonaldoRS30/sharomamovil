<?php
if (isset($con)) {
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Modal -->
<div class="modal fade" id="nuevoCliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
						aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Agregar nuevo cliente
				</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" method="post" id="guardar_cliente" name="guardar_cliente">
					<div id="resultados_ajax"></div>

					<!-- Tipo de cliente -->
					<div class="form-group">
						<label for="tipo_cliente" class="col-sm-3 control-label">Tipo de cliente</label>
						<div class="col-sm-8">
							<select id="tipo_cliente" name="tipo_cliente" class="form-control" onchange="toggleClientFields()">
								<option value="0">NATURAL</option>
								<option value="1">JURIDICO</option>
							</select>
						</div>
					</div>

					<!-- Tipo de documento -->
					<div class="form-group">
						<label for="tipo_documento" class="col-sm-3 control-label">Tipo de documento</label>
						<div class="col-sm-8">
							<select id="tipo_documento" name="tipo_documento" class="form-control" required>
								<option value="1" selected>DNI</option>
								<option value="0">RUC</option>
							</select>
						</div>
					</div>

					<!-- Número de documento -->
					<div class="form-group row align-items-center">
						<label for="numero_documento" class="col-sm-3 col-form-label text-right">Número de
							documento</label>
						<div class="col-sm-9">
							<div style="display: flex; align-items: center; gap: 5px;">
								<input type="number" class="form-control" id="numero_documento" name="numero_documento"
									required placeholder="Ingrese el número de documento" />
								<button type="button" class="btn btn-default btn-search-sunat"
									style="padding: 4px 6px; height: 38px; display: flex; align-items: center; justify-content: center;">
									<img src="img/sunat.png" style="width: 20px; height: 20px;" alt="Buscar SUNAT" />
								</button>
								<span class="icon-loading-lg" style="margin-left: 10px;"></span>
							</div>
						</div>
					</div>

					<!-- Nombres -->
					<div id="divNom">
						<div class="form-group">
							<label for="nombres" class="col-sm-3 control-label">Nombres</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="nombres" name="nombres" required
									placeholder="Ingrese los nombres" />
							</div>
						</div>

						<!-- Apellido paterno -->
						<div class="form-group">
							<label for="apepa" class="col-sm-3 control-label">Apellido paterno</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="apepa" name="apepa" required
									placeholder="Ingrese apellido paterno" />
							</div>
						</div>

						<!-- Apellido materno -->
						<div class="form-group">
							<label for="apema" class="col-sm-3 control-label">Apellido materno</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="apema" name="apema" required
									placeholder="Ingrese apellido materno" />
							</div>
						</div>
					</div>

					<!-- Razón Social -->
					<div id="divRuc" style="display:none;">
						<div class="form-group">
							<label for="razon" class="col-sm-3 control-label">Razón Social</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" id="razon" name="razon" placeholder="Ingrese la Razón Social" />
							</div>
						</div>
					</div>

					<!-- Teléfono -->
					<div class="form-group">
						<label for="telefono" class="col-sm-3 control-label">Teléfono</label>
						<div class="col-sm-8">
							<input type="number" class="form-control" id="telefono" name="telefono" />
						</div>
					</div>

		         		<!-- Email -->
						<!-- <div class="form-group">
							<label for="email" class="col-sm-3 control-label">Email</label>
							<div class="col-sm-8">
								<input type="email" class="form-control" id="email" name="email">
							</div>
						</div> -->

					<!-- Dirección -->
					<div class="form-group">
						<label for="direccion" class="col-sm-3 control-label">Dirección</label>
						<div class="col-sm-8">
							<textarea class="form-control" id="direccion" name="direccion" maxlength="255"></textarea>
						</div>
					</div>

					<!-- Estado -->
					<div class="form-group">
						<label for="estado" class="col-sm-3 control-label">Estado</label>
						<div class="col-sm-8">
							<select class="form-control" id="estado" name="estado" required>
								<option value="">-- Selecciona estado --</option>
								<option value="1" selected>Activo</option>
								<option value="0">Inactivo</option>
							</select>
						</div>
					</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-primary" id="guardar_datos">Guardar datos</button>
			</div>
			</form>
		</div>
	</div>
</div>

<!-- jQuery (asegúrate de tenerlo cargado en tu proyecto) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
	function toggleClientFields() {
		var tipoCliente = document.getElementById('tipo_cliente').value;
		var tipoDocumento = document.getElementById('tipo_documento');
		var nom = document.getElementById("divNom");
		var ruc = document.getElementById("divRuc");

		// Limpiar las opciones del tipo de documento
		tipoDocumento.innerHTML = '';

		if (tipoCliente === '0') {
			// NATURAL: solo DNI
			var optionDNI = document.createElement('option');
			optionDNI.value = '1';
			optionDNI.text = 'DNI';
			tipoDocumento.appendChild(optionDNI);

			nom.style.display = "block";
			ruc.style.display = "none";

			$('#razon').removeAttr("required").val('');
			$('#nombres').prop("required", true);
			$('#apepa').prop("required", true);
			$('#apema').prop("required", true);
		} else if (tipoCliente === '1') {
			// JURIDICO: solo RUC
			var optionRUC = document.createElement('option');
			optionRUC.value = '2';
			optionRUC.text = 'RUC';
			tipoDocumento.appendChild(optionRUC);

			nom.style.display = "none";
			ruc.style.display = "block";

			$('#nombres').removeAttr("required").val('');
			$('#apepa').removeAttr("required").val('');
			$('#apema').removeAttr("required").val('');

			$('#razon').prop("required", true);
		}
	}

	document.addEventListener('DOMContentLoaded', toggleClientFields);

	$(document).ready(function () {
		$('.btn-search-sunat').click(function () {
			var numero = $('#numero_documento').val().trim();

			if (numero.length !== 8 && numero.length !== 11) {
				alert('Ingrese un número de documento válido (8 o 11 dígitos)');
				return;
			}

			// Mostrar icono de carga
			$('.icon-loading-lg').html('<img src="img/loading.gif" alt="Cargando..." style="width:20px; height:20px;" />');

			$.ajax({
				url: './ajax/buscar_documento.php',
				type: 'POST',
				dataType: 'json',
				data: { numero: numero },
				success: function (response) {
					$('.icon-loading-lg').html(''); // Quitar icono de carga

					if (response.success) {
						if (response.tipo_cliente == 1) {
							// Juridico - RUC
							$('#tipo_cliente').val('1').change();
							$('#tipo_documento').val('2').change();
							$('#razon').val(response.razon_social);
							$('#direccion').val(response.direccion);
						} else {
							// Natural - DNI
							$('#tipo_cliente').val('0').change();
							$('#tipo_documento').val('1').change();
							$('#nombres').val(response.nombres);
							$('#apepa').val(response.apellido_paterno);
							$('#apema').val(response.apellido_materno);
						}
					} else {
						alert(response.message);
					}
				},
				error: function () {
					$('.icon-loading-lg').html('');
					alert('Error al consultar la API');
				}
			});
		});
	});
</script>

<?php
}
?>
