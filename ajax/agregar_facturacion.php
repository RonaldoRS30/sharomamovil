<?php
/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
include('is_logged.php'); //Archivo verifica que el usario que intenta acceder a la URL esta logueado (annel detalle) (Sergio detalle) XD

if (isset($_POST['id'])) {
	$id = $_POST['id'];
}
if (isset($_POST['cantidad'])) {
	$cantidad = $_POST['cantidad'];
}
if (isset($_POST['precio'])) {
	$precio_venta = $_POST['precio'];
}
if (isset($_POST['detalle'])) {
	$detalle_adicional = $_POST['detalle'];
}

if (isset($_POST['session'])) {
	$session = $_POST['session'];
}

if (isset($_POST['nombre'])){$nombre=$_POST['nombre'];}

/* Connect To Database*/
require_once("../config/db.php"); //Contiene las variables de configuracion para conectar a la base de datos
require_once("../config/conexion.php"); //Contiene funcion que conecta a la base de datos
//Archivo de funciones PHP (annel detalle)
include("../funciones.php");

if (!empty($id) && !empty($cantidad) and !empty($precio_venta)) {
	$total = $cantidad * $precio_venta;
	$subtotal= $total/1.18;
	$iva_t = $total - $subtotal;
    $insert_tmp = mysqli_query($con, "INSERT INTO temporal_detalle (PROD_Codigo, UNDMED_Codigo, LOTP_Codigo, TEMPDE_Descripcion, TEMPDE_Observacion, TEMPDE_Cantidad, TEMPDE_Costo, TEMPDE_Precio,TEMPDE_Subtotal, TEMPDE_Igv, TEMPDE_Total, TEMPDE_SESSION, TEMPDE_FlagEstado, TEMPDE_TipoIgv, TEMPDE_FlagBs) 
	VALUES ('$id',1,0, '$nombre','$detalle_adicional', '$cantidad','$subtotal', '$precio_venta', '$subtotal','$iva_t','$total', '$session', 0, 1, 'B')");
}

if (isset($_GET['id'])) {
	$session = $_GET['sesionE'];
    $id_tmp = intval($_GET['id']);
    $stmt = $con->prepare("DELETE FROM temporal_detalle WHERE TEMPDE_Codigo = ?");
    $stmt->bind_param("i", $id_tmp);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Elemento eliminado con éxito.";
    } else {
        echo "Error al eliminar el elemento.";
    }
    $stmt->close();
}

if (isset($_GET['session'])) {
	$id_cliente = $_GET['id_cliente'];
	$id_vendedor = $_GET['id_vendedor'];
	$obs = $_GET['obs'];
	$dir = $_GET['dir'];
	$fecha = date("Y-m-d h:i:s");
	$fechaEntMin = $_GET['fechaEntMin'];
	$fechaEntMax = $_GET['fechaEntMax'];
	$montoDefault = $_GET['montoDefault'];
	$forpapDefault = $_GET['forpapDefault'];
	$total=0;

	
	$allcode= explode(",",$_GET['allcode']);	
	$allcant = explode(",",$_GET['allcant']);
	$allnom = explode(",",$_GET['allnom']);
	$allpu = explode(",",$_GET['allpu']);
	$allpt = explode(",",$_GET['allpt']);
	$allForpap = explode(",",$_GET['forpapMult']);
	$allMonto = explode(",",$_GET['montoMult']);

	if($forpapDefault == 22){
		foreach($allMonto as $i => $val){
			$MontoPedido +=$val;
		}
	}else{
		$MontoPedido = $montoDefault;
	}
	
	//ACTUALIZAR EL FLAG Y ELIMINAR PRODUCTOS FANTASMA
    $id_tmp = intval($_GET['session']);
    $stmt = $con->prepare("UPDATE temporal_detalle SET TEMPDE_FlagEstado = 1 WHERE TEMPDE_SESSION = ?");
    $stmt->bind_param("i", $id_tmp);
    $stmt->execute();
	$delete = mysqli_query($con,"DELETE FROM temporal_detalle WHERE TEMPDE_FlagEstado = 0");

	//CALCULAR EL TOTAL
	foreach($allpt as $i => $val){
		$total+=$val;
	}
	$subtotal= $total/1.18;
	$iva_t = $total - $subtotal;

	//INSERTAR EL PEDIDO
	$queryNum= mysqli_query($con,"SELECT CONFIC_Numero from cji_configuracion WHERE CONFIP_Codigo = 21 LIMIT 1");
	$getNum= mysqli_fetch_array($queryNum);
	$newNum = $getNum[0]+1;
	$sql="INSERT INTO cji_pedido(ALMAP_Codigo, COMPP_Codigo, PEDIC_Direccion, PEDIC_TipoDocume, MONED_Codigo, PEDIC_IGV, PEDIC_Serie, PEDIC_Numero, FORPAP_Codigo, FORPAP_Monto, PEDIC_FechaSistema, PEDIC_Vendedor, CLIP_Codigo, PEDIC_IGVTotal, PEDIC_PrecioTotal, PEDIC_FlagEstado, PEDIC_FechaEntregaMin, PEDIC_FechaEntregaMax, PEDIC_Observacion) 
		  VALUES(1,1,'$dir','V',1,18,'OP01','$newNum','$forpapDefault','$MontoPedido','$fecha','$id_vendedor','$id_cliente','$iva_t','$total',0,'$fechaEntMin','$fechaEntMax', '$obs')";
	$query= mysqli_query($con,$sql);
	$id =mysqli_insert_id($con);

	//INSERTAR EL DETALLE
    if ($query == true) {
		$queryUpdate = mysqli_query($con, "UPDATE cji_configuracion SET CONFIC_Numero = $newNum WHERE CONFIP_Codigo = 21");
		$j=0;
		foreach($allcode as $i){
			//INSERTAR A SU TABLA DETALLE
			$iva_pu = $allpu[$j]/1.18;
			$subtotal=($allpu[$j]*$allcant[$j])/1.18;
			$iva_t = number_format(($allpu[$j]*$allcant[$j])-$subtotal,2,'.','');
			$sqlDeta="INSERT INTO cji_pedidodetalle(PEDIP_Codigo, PROD_Codigo, PROD_Nombre, UNDMED_Codigo, PEDIDETC_Almacen, PEDIDETC_Cantidad, PEDIDETC_PCIGV, PEDIDETC_PSIGV, PEDIDETC_Precio, PEDIDETC_IGV, PEDIDETC_Importe, PEDIDETC_FechaRegistro, PEDIDETC_FlagEstado)
				  VALUES('$id','$allcode[$j]','$allnom[$j]',1, 1,'$allcant[$j]','$allpu[$j]','$iva_pu','$subtotal','$iva_t','$allpt[$j]','$fecha',1)";
			$queryDeta= mysqli_query($con,$sqlDeta);

			//ACTUALIZAR SU STOCK COMPROMETIDO

			$sqlGetStockC = "SELECT * FROM cji_almacenproducto WHERE ALMAC_Codigo = 1 AND COMPP_Codigo = 1 AND PROD_Codigo = '$allcode[$j]'";
			$queryGetStockC = mysqli_query($con, $sqlGetStockC);
			while($rGet=mysqli_fetch_array($queryGetStockC)){
				$codPordAlm = $rGet['ALMPROD_Codigo'];
				$stockCActual = $rGet['ALMPROD_StockComprometido'];
			}
			$newStockComprometido = $stockCActual + $allcant[$j];

			$sqlUpdateStockC = "UPDATE cji_almacenproducto SET ALMPROD_StockComprometido = $newStockComprometido WHERE ALMPROD_Codigo = $codPordAlm";
			$queryUpdateStockC = mysqli_query($con, $sqlUpdateStockC);
			$j++;
		}
		if($forpapDefault == 22){
			$j=0;
			foreach($allForpap as $i => $val){
				$sqlMultiple = "INSERT INTO cji_pedido_formaspago(PEDIP_Codigo, FORPAP_Codigo, MONED_Codigo, monto, pedi_flag_FechaRegistro)
								VALUES('$id','$val','1','$allMonto[$j]','$fecha')";
				$queryMultiple = mysqli_query($con, $sqlMultiple);
				$j++;
			}
		}
		
		$stmt->close();
		$json = array(
            "result" => "success",
            "message" => "Pedido guardado con éxito"
        );
		echo json_encode($json);
		die();
    } else {
		$stmt->close();
        $json = array(
            "result" => "error",
            "message" => "ERROR en el pedido, comunicarse con Soporte Técnico"
        );
		echo json_encode($json);
		die();
    }
}

$simbolo_moneda = get_row('cji_moneda', 'MONED_Simbolo', 'MONED_Codigo', 1);
?> 
<div class="table-responsive-datatableProd">
<table id="datatableProd" class="table"> 
	<tr>
		<th class='text-center'>CANT.</th>
		<th class='text-left'>DESCRIPCION</th>
		<th></th>
		<th class='text-right'>PRECIO UNIT.</th>
		<th class='text-right'>PRECIO TOTAL</th>
		<th></th>
	</tr>
	<?php
	$sumador_total = 0;
	$impuesto=18;
	$iva_t = 0;
	$n = 0;
	//$igv_total = get_row('perfil', 'igv_total', 'id_perfil', 1);
	//$impuesto = get_row('perfil', 'impuesto', 'id_perfil', 1);
	$sql = "select * from cji_producto, temporal_detalle where cji_producto.PROD_Codigo=temporal_detalle.PROD_Codigo and temporal_detalle.TEMPDE_SESSION='" . $session . "'";
	$query = mysqli_query($con, $sql);
	while ($row = mysqli_fetch_array($query)) {
				$n++;
		

		//start	
		//$igv_get = get_row('cji_producto', 'igv', 'PROD_Codigo', $row['id_producto']);

//nel detalle
		//if ($igv_total == 1) {
				$id_tmp = $row["TEMPDE_Codigo"];
				$codigo_producto = $row['PROD_Codigo'];
				$cantidad = $row['TEMPDE_Cantidad'];
				$nombre_producto = $row['PROD_Nombre'];
				$detalle_adicional_tmp = $row['TEMPDE_Descripcion'];
				$precio_venta = $row['TEMPDE_Precio'];

				
				// $precio_m = (100 * $precio_venta) / 118;
				// $precio_m_f = number_format($precio_m, 2); //Formateo variables
				// $precio_m_r = str_replace(",", "", $precio_m_f); //Reemplazo las comas



				// $precio_total = $precio_m_r * $cantidad;



				// $precio_total_f = number_format($precio_total, 2); //Precio total formateado
				// $precio_total_r = str_replace(",", "", $precio_total_f); //Reemplazo las comas
				// $sumador_total += $precio_total_r; //Sumador

				$iva_t = $precio_venta * $cantidad;
				$sumador_total += $iva_t

	?>
	<tbody id="databody">
			<tr>
				<input type="hidden" value='<?=$codigo_producto?>' id='prod_cod_<?=$n?>' class="codigo_prod">
				<input type="hidden" value="<?=$codigo_producto?>" id="get_<?=$id_tmp?>">

				<input type="hidden" value='<?=$cantidad?>' id='prod_cant_<?=$n?>'>
				<td class='text-center'><?php echo $cantidad; ?></td>

				<input type="hidden" value='<?=$nombre_producto?>' id='prod_nom_<?=$n?>'>
				<td class='text-left'><?php echo $nombre_producto; ?></td>

				<td></td>
				<input type="hidden" value='<?=$precio_venta?>' id='prod_pu_<?=$n?>'>
				<td class='text-right'><?php echo $precio_venta; ?></td>

				<input type="hidden" value='<?=$iva_t?>' id='prod_pt_<?=$n?>'>
				<input type="hidden" value='<?=$iva_t?>' id='total_<?=$id_tmp?>'>
				<td class='text-right'><?php echo $iva_t; ?></td>
				<td class='text-center'><a href="#" onclick="eliminar('<?=$id_tmp ?>','<?=$session?>')"><button type="button" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></button></a></td>
			</tr>
	</tbody>
			<?php

			}
			$subtotal=number_format($sumador_total/1.18,2,'.','');
			$iva_t = number_format($sumador_total - ($sumador_total/1.18),2,'.','');
			$total_factura=$subtotal+$iva_t;
		//end
		



	/*$subtotal = number_format($sumador_total, 2, '.', '');

	$total_factura = $subtotal + $iva_t;*/


	if($total_factura != 0){
	?>
		<tr>
			<td class='text-right' colspan=4>SUBTOTAL <?php echo $simbolo_moneda; ?></td>
			<td class='text-right'><?php echo number_format($subtotal, 2); ?></td> 
			<td></td>
		</tr>

		<tr>
			<td class='text-right' colspan=4>IGV (<?php echo $impuesto; ?>)% <?php echo $simbolo_moneda; ?></td>
			<td class='text-right'><?php echo number_format($iva_t, 2); ?></td>
			<td></td>
		</tr>
				
		<tr>
			<td class='text-right' colspan=4>TOTAL <?php echo $simbolo_moneda; ?></td>
			<td class='text-right'><?php echo number_format($sumador_total, 2); ?></td>
			<input type="hidden" id="SumaTotal" value = '<?php echo number_format($sumador_total, 2); ?>'>
			<td></td>
		</tr>
	<?php } ?> 
</table>
</div>

<style>
	.table-responsive-datatableProd {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch; /* para scroll suave en iOS */
}

/* Opcional: que la tabla ocupe todo el ancho disponible */
.table-responsive-datatableProd table {
  width: 100%;
  border-collapse: collapse;
}

</style>