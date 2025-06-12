<?php
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/*Inicia validacion del lado del servidor*/
	$id = $_POST['mod_id'];
	$fail = "false";
	$idSec = $_POST['mod_idSec'];
	$tipoCliente = $_POST['mod_tipo'];
	$documento = $_POST["mod_ruc"];
	if (empty($id)) {
		$errors[] = "ID vacío";
	}
	if($tipoCliente == 1){
		if(empty($_POST['mod_razon'])){
			$errors[] = "Nombre vacío";
			$fail = "true";
		}
		if(empty($documento) || strlen($documento) != 11){
			$errors[] = "Documento inválido";
			$fail = "true";
		}
	}else{
		if(empty($_POST['mod_nombres']) || empty($_POST['mod_apepa']) || empty($_POST['mod_apema'])){
			$errors[] = "Nombre y/o Apellidos vacíos";
			$fail = "true";
		
		}
		if(empty($documento) || strlen($documento) != 8){
			$errors[] = "Documento inválido";
			$fail = "true";
		}
	}

	if($_POST['mod_estado'] == ""){
		$errors[] = "Seleccione un estado válido";
	}
	
	if($fail == "false"){
		if (!empty($id) && $_POST['mod_estado']!=""){
			/* Connect To Database*/
			require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
			// escaping, additionally removing everything that could be (html/javascript-) code
			$nombres=mysqli_real_escape_string($con,(strip_tags($_POST["mod_nombres"],ENT_QUOTES)));
			$apepa = mysqli_real_escape_string($con, (strip_tags($_POST["mod_apepa"], ENT_QUOTES)));
			$apema = mysqli_real_escape_string($con, (strip_tags($_POST["mod_apema"], ENT_QUOTES)));
			$razon = mysqli_real_escape_string($con, (strip_tags($_POST["mod_razon"], ENT_QUOTES)));
			$telefono=mysqli_real_escape_string($con,(strip_tags($_POST["mod_telefono"],ENT_QUOTES)));
			$num=mysqli_real_escape_string($con,(strip_tags($documento,ENT_QUOTES)));
			$email=mysqli_real_escape_string($con,(strip_tags($_POST["mod_email"],ENT_QUOTES)));
			$direccion=mysqli_real_escape_string($con,(strip_tags($_POST["mod_direccion"],ENT_QUOTES)));
			$estado=intval($_POST['mod_estado']);
			$date_edited = date("Y-m-d H:i:s");


			//Realizar la inserción
			//Empresa
			if($tipoCliente == 1){
				$sqlEmpresa="UPDATE cji_empresa SET EMPRC_Ruc='$num', EMPRC_RazonSocial='$razon',EMPRC_Telefono='$telefono' ,EMPRC_Email='$email', EMPRC_FechaModificacion ='$date_edited', EMPRC_Direccion='$direccion', EMPRC_FlagEstado='$estado'  WHERE EMPRP_Codigo='$idSec'";
				$queryEmpresa = mysqli_query($con,$sqlEmpresa);
				if($queryEmpresa){
					$sqlUpdate="UPDATE cji_cliente SET CLIC_FechaModificacion='$date_edited', CLIC_FlagEstado='$estado' WHERE CLIP_Codigo = $id";
					$queryUpdate = mysqli_query($con, $sqlUpdate);
				}else{
					$errors []= "Lo siento algo ha salido mal intenta nuevamente.".mysqli_error($con);
				}
			}else{
				$sqlPersona="UPDATE cji_persona SET PERSC_Nombre='$nombres', PERSC_ApellidoPaterno='$apepa',PERSC_ApellidoMaterno='$apema' ,PERSC_NumeroDocIdentidad='$num', PERSC_Direccion ='$direccion', PERSC_Telefono='$telefono', PERSC_Email='$email', PERSC_Domicilio = '$direccion', PERSC_FechaModificacion = '$date_edited', PERSC_FlagEstado = '$estado' WHERE PERSP_Codigo='$idSec'";
				$queryPersona = mysqli_query($con,$sqlPersona);
				if($queryPersona){
					$sqlUpdate="UPDATE cji_cliente SET CLIC_FechaModificacion='$date_edited', CLIC_FlagEstado='$estado' WHERE CLIP_Codigo = $id";
					$queryUpdate = mysqli_query($con, $sqlUpdate);
				}else{
					$errors []= "Lo siento algo ha salido mal intenta nuevamente.".mysqli_error($con);
				}
			}
				if ($queryUpdate){
					$messages[] = "Cliente ha sido actualizado satisfactoriamente.";
				} else{
					$errors []= "Lo siento algo ha salido mal intenta nuevamente.".mysqli_error($con);
				}
			} else {
				$errors []= "Error desconocido.";
			}
		}
		
		if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}

?>