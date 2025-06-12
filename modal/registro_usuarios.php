	<?php
	include ("config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	include ("config/conexion.php");//Contiene funcion que conecta a la base de datos
		if (isset($con))
		{
	?>
	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><i class='glyphicon glyphicon-edit'></i> Agregar nuevo usuario</h4>
		  </div>
		  <div class="modal-body">
			<form class="form-horizontal" method="post" id="guardar_usuario" name="guardar_usuario">
			<div id="resultados_ajax"></div>
			  <div class="form-group">
				<label for="firstname" class="col-sm-3 control-label">Nombres</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Nombres" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="lastname" class="col-sm-3 control-label">Apellidos</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Apellidos" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="user_name" class="col-sm-3 control-label">Usuario</label>
				<div class="col-sm-8">
				  <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Usuario" pattern="[a-zA-Z0-9]{2,64}" title="Nombre de usuario ( sólo letras y números, 2-64 caracteres)"required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="user_email" class="col-sm-3 control-label">Email</label>
				<div class="col-sm-8">
				  <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Correo electrónico" required>
				</div>
			  </div>
			  <div class="form-group">
			  <label for="vendedor" class="col-sm-3 control-label">Para: </label>
				<div class="col-sm-8">
				<select class="form-control input-sm" id="id_vendedor" name="id_vendedor">
				<?php
					$sqlVendedor= "SELECT d.DIREP_Codigo, p.PERSP_Codigo, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno
									FROM cji_directivo d
									INNER JOIN cji_persona p ON d.PERSP_Codigo = p.PERSP_Codigo
									INNER JOIN cji_cargo c ON d.CARGP_Codigo = c.CARGP_Codigo
									WHERE d.DIREC_FlagEstado = 1 AND c.COMPP_Codigo = 1 AND d.EMPRP_Codigo = 1";
					$user=mysqli_query($con,$sqlVendedor);
					while ($rw=mysqli_fetch_array($user)){
						$id_vendedor=$rw["DIREP_Codigo"];
						$nombre_vendedor=$rw["PERSC_ApellidoPaterno"]." ".$rw["PERSC_ApellidoMaterno"]." ".$rw["PERSC_Nombre"];
					?>
					<option value="<?php echo $id_vendedor?>"><?php echo $nombre_vendedor?></option>
					<?php
						}
					?>
				</select>
				</div>
			  </div>
			  <div class="form-group">
				<label for="user_password_new" class="col-sm-3 control-label">Contraseña</label>
				<div class="col-sm-8">
				  <input type="password" class="form-control" id="user_password_new" name="user_password_new" placeholder="Contraseña" pattern=".{6,}" title="Contraseña ( min . 6 caracteres)" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="user_password_repeat" class="col-sm-3 control-label">Repite contraseña</label>
				<div class="col-sm-8">
				  <input type="password" class="form-control" id="user_password_repeat" name="user_password_repeat" placeholder="Repite contraseña" pattern=".{6,}" required>
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