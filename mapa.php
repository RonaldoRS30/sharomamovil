<?php

$active_facturas = "";
$active_productos = "";
$active_clientes = "";
$active_usuarios = "";
$active_mapa = "active";
$title = "Mapa | Order Tracking";

require_once("config/db.php");
require_once("config/conexion.php");
include("funciones.php");

session_start();
?>
<?php
	if(isset($_GET['idVend'])){
	$idVend = $_GET['idVend'];
	$sql="SELECT * FROM cji_rastreo WHERE DIREP_Codigo = $idVend";
	$get=mysqli_query($con,$sql);
		while ($res=mysqli_fetch_array($get)){
			$ubi1 = $res['RS_ubi1'];
			$ubi2 = $res['RS_ubi2'];
			$ubi3 = $res['RS_ubi3'];
			$ubi4 = $res['RS_ubi4'];

			$fecha1 = $res['RS_FechaRegistro1'];
			$fecha2 = $res['RS_FechaRegistro2'];
			$fecha3 = $res['RS_FechaRegistro3'];
			$fecha4 = $res['RS_FechaRegistro4'];

			if($ubi1!= ""){
				$json[] = [$ubi1, $fecha1];
			}
			if($ubi2!= ""){
				$json[] = [$ubi2, $fecha2];
			}
			if($ubi3!= ""){
				$json[] = [$ubi3, $fecha3];
			}
			if($ubi4!= ""){
				$json[] = [$ubi4, $fecha4];
			}
		}
	echo json_encode($json);
	die();
	}
?>
<!DOCTYPE html>
	<html lang="en">
	<head>
		<?php include("head.php"); ?>
	</head>
	<body>
		<?php
		include("navbar.php");
		?>
		<div class="container" style="width:100%;">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="btn-group pull-right">
					</div>
					<h4>Buscar Pedidos</h4>
				</div>
				<div class="panel-body">
				<div class="form-group row">
				<label for="empresa" class="col-md-1 control-label">Vendedor</label>
				<div class="col-md-3">
					<select class="form-control input-sm" id="id_vendedor" onchange="ActualizarMapa()">
						<option value="">SELECCIONE UN VENDEDOR</option>
					<?php
						$sqlVendedor = "SELECT d.DIREP_Codigo, p.PERSP_Codigo, p.PERSC_Nombre, p.PERSC_ApellidoPaterno, p.PERSC_ApellidoMaterno, p.PERSC_Telefono
										FROM cji_directivo d
										INNER JOIN cji_persona p ON d.PERSP_Codigo = p.PERSP_Codigo
										INNER JOIN cji_cargo c ON d.CARGP_Codigo = c.CARGP_Codigo
										WHERE d.DIREC_FlagEstado = 1 AND c.COMPP_Codigo = 1 AND d.EMPRP_Codigo = 1 AND d.CARGP_Codigo = 2";
						$user=mysqli_query($con,$sqlVendedor);
						$j = 1;
						while ($rw=mysqli_fetch_array($user)){
							$id_vendedor=$rw["DIREP_Codigo"];
							$num_vendedor=$rw["PERSC_Telefono"];
							$nombre_vendedor=$rw["PERSC_ApellidoPaterno"]." ".$rw["PERSC_ApellidoMaterno"]." ".$rw["PERSC_Nombre"];
							
						?>
						<option value="<?php echo $id_vendedor?>"><?php echo $nombre_vendedor?></option>
						<?php } ?>
					</select>
				</div>
				<br>
				<div class="btn-group" style="float:right; margin-right:20px;">
					<a class="btn btn-info"><span class="glyphicon glyphicon-search"></span>
						Buscar</a>
				</div>
			</div>
				</div>
			</div>
		</div>
		<?php
			include("footer.php");
		?>
		<div id="map" style="height:800px; width:100%; border-width:10px;"></div>
		<script src="js/mapa.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCuuOcOXA0ii-Wgxs_DCYXLa7KchZAfS7U&callback=iniciarMap&libraries=marker"></script>
	
	</body>
</html>