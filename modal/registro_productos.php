	<?php
		if (isset($con))
		{
	?>
	<!-- Modal -->
	<div class="modal fade" id="nuevoProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Agregar nuevo producto</h4>
		  </div>
		  <div class="modal-body">
			<form class="form-horizontal" method="post" id="guardar_producto" name="guardar_producto">
			<div id="resultados_ajax_productos"></div>
			  <div class="form-group">
				<label for="codigo" class="col-sm-3 control-label">Código</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código del producto" required>
				</div>
			  </div>
			  
			  <div class="form-group">
				<label for="nombre" class="col-sm-3 control-label">Nombre</label>
				<div class="col-sm-8">
					<textarea class="form-control" id="nombre" name="nombre" placeholder="Nombre del producto" required maxlength="255" ></textarea>
				  
				</div>
			  </div>
			  <div class="form-group">
				<label for="detalle" class="col-sm-3 control-label">Detalle del producto</label>
				<div class="col-sm-8">
					<textarea class="form-control" id="detalle" name="detalle" placeholder="Detalle adicional del producto" required maxlength="255" ></textarea>
				  
				</div>
			  </div>
			  
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
			  <div class="form-group">
    <label for="precio" class="col-sm-3 control-label">Precio Venta</label>
    <div class="col-sm-8">
        <input 
            type="text" 
            class="form-control" 
            id="precio" 
            name="precio" 
            placeholder="Precio de venta del producto" 
            required 
            inputmode="decimal" 
            pattern="^[0-9]+(\.[0-9]{1,2})?$" 
            title="Ingresa sólo números con hasta 2 decimales" 
            maxlength="8"
            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" 
        >
    </div>
</div>

			  <div class="form-group">
				<label for="precio" class="col-sm-3 control-label">Precio Costo</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="precio_costo" name="precio_costo" placeholder="Precio costo del producto" required pattern="^[0-9]{1,5}(\.[0-9]{0,2})?$"             oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" 
				  title="Ingresa sólo números con 0 ó 2 decimales" maxlength="8">
				</div>
			  </div>

			  <!--<div class="form-group">
				<label for="medida" class="col-sm-3 control-label">Unidad de Medida</label>
				<div class="col-sm-8">
				 <select class="form-control" id="medida" name="medida" required>
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

			  <div class="form-group">
				<label for="igv" class="col-sm-3 control-label">IGV</label>
				<div class="col-sm-8">
				 <select class="form-control" id="igv" name="igv" required>
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
	<?php
		}
	?>