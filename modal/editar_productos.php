	<?php
		if (isset($con))
		{
	?>
	<!-- Modal -->
	<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Editar producto</h4>
		  </div>
		  <div class="modal-body">
			<form class="form-horizontal" method="post" id="editar_producto" name="editar_producto">
			<div id="resultados_ajax2"></div>
			  <div class="form-group">
				<label for="mod_codigo" class="col-sm-3 control-label">Código</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="mod_codigo" name="mod_codigo" placeholder="Código del producto" required>
					<input type="hidden" name="mod_id" id="mod_id">
				</div>
			  </div>
			   <div class="form-group">
				<label for="mod_nombre" class="col-sm-3 control-label">Nombre</label>
				<div class="col-sm-8">
				  <textarea class="form-control" id="mod_nombre" name="mod_nombre" placeholder="Nombre del producto" required></textarea>
				</div>
			  </div>
			  <div class="form-group">
				<label for="mod_detalle" class="col-sm-3 control-label">Detalle</label>
				<div class="col-sm-8">
				  <textarea class="form-control" id="mod_detalle" name="mod_detalle" placeholder="Detalle del producto" required></textarea>
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
			  <div class="form-group">
				<label for="mod_precio" class="col-sm-3 control-label">Precio</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="mod_precio" name="mod_precio" placeholder="Precio de venta del producto" required pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$" title="Ingresa sólo números con 0 ó 2 decimales"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="8">
				</div>
			  </div>
		    <div class="form-group">
				<label for="mod_precio_costo" class="col-sm-3 control-label">Precio Costo</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="mod_precio_costo" name="mod_precio_costo" placeholder="Precio costo del producto" required pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$"  oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
				</div>
			  </div>

			  <!--<div class="form-group">
				<label for="mod_medida" class="col-sm-3 control-label">Unidad de Medida</label>
				<div class="col-sm-8">
				 <select class="form-control" id="mod_medida" name="mod_medida" required>
					<option value="">-- Selecciona --</option>
					<?php  
					$sql="SELECT * FROM cji_unidadmedida";
					$data=mysqli_query($con,$sql);
					
					while ($row_data=mysqli_fetch_array($data)) 
					{
						?> 
						<option value="<?php echo $row_data["UNDMED_Codigo"]; ?>"><?php echo $row_data["UNDMED_Descripcion"]." (".$row_data["UNDMED_Simbolo"].")"; ?></option>  
						<?php
					}


					?>
				  </select>
				</div>
			  </div>-->


			  <!--<div class="form-group">
				<label for="mod_igv" class="col-sm-3 control-label">IGV</label>
				<div class="col-sm-8">
				 <select class="form-control" id="mod_igv" name="mod_igv" required>
					<option value="">-- Selecciona --</option>
					<?php  
					$sql="SELECT * FROM cji_tipo_afectacion";
					$data=mysqli_query($con,$sql);
					
					while ($row_data=mysqli_fetch_array($data)) 
					{
						?>
						<option value="<?php echo $row_data["AFECT_Codigo"]; ?>"><?php echo $row_data["AFECT_Descripcion"]; ?></option>
						<?php
					}


					?>
				  </select>
				</div>
			  </div>-->	 
			 
			
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