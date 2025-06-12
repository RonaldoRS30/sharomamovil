<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
$id_factura= $_SESSION['id_factura'];
$numero_factura= $_SESSION['numero_factura'];
if (isset($_POST['id'])){$id=intval($_POST['id']);}
if (isset($_POST['cantidad'])){$cantidad=intval($_POST['cantidad']);}
if (isset($_POST['precio_venta'])){$precio_venta=floatval($_POST['precio_venta']);}
if (isset($_POST['detalle'])){$detalle_adicional=$_POST['detalle'];}
if (isset($_POST['nombre'])){$nombre=$_POST['nombre'];}
$sessionG = null;
$session = null;
if (isset($_POST['session'])) {
	$session = $_POST['session'];
}else if (isset($_GET['session'])) {
	$sessionG = $_GET['session'];
}

	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	//Archivo de funciones PHP
	include("../funciones.php");
if (!empty($id) && !empty($cantidad) and !empty($precio_venta)) {
	$total = $cantidad * $precio_venta;
	$subtotal= $total/1.18;
	$iva_t = $total - $subtotal;
	$insert_tmp = mysqli_query($con, "INSERT INTO temporal_detalle (PROD_Codigo, UNDMED_Codigo, LOTP_Codigo, TEMPDE_Descripcion, TEMPDE_Observacion, TEMPDE_Cantidad, TEMPDE_Costo, TEMPDE_Precio,TEMPDE_Subtotal, TEMPDE_Igv, TEMPDE_Total, TEMPDE_SESSION, TEMPDE_FlagEstado, TEMPDE_TipoIgv, TEMPDE_FlagBs) 
	VALUES ('$id',1,0, '$nombre','$detalle_adicional', '$cantidad','$subtotal', '$precio_venta', '$subtotal','$iva_t','$total', '$session', 0, 1, 'B')");
}

if (isset($_GET['id']))//codigo elimina un elemento del array
{
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

if(isset($_GET['editarS'])){
	$session = $_GET['editarS'];
	$id_cliente = $_GET['id_cliente'];
	$id_vendedor = $_GET['id_vendedor'];
	$id_almacen =  $_GET['id_almacen'];
	$obs = $_GET['obs'];
	$fecha = date("Y-m-d h:i:s");
	$fechaEntMin = $_GET['fechaEntMin'];
	$fechaEntMax = $_GET['fechaEntMax'];
	$montoDefault = $_GET['montoDefault'];
	$forpapDefault = $_GET['forpapDefault'];
	$total = 0;
	$MontoPedido = 0;
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

	//CALCULAR EL TOTAL
	foreach($allpt as $i => $val){
		$total+=$val;
	}
	
	$subtotal= $total/1.18;
	$iva_t = $total - $subtotal;
	

	//ACTUALIZAR EL FLAG Y ELIMINAR PRODUCTOS FANTASMA
    $id_tmp = intval($_GET['editarS']);
    $stmt = $con->prepare("UPDATE temporal_detalle SET TEMPDE_FlagEstado = 1 WHERE TEMPDE_SESSION = ?");
    $stmt->bind_param("i", $id_tmp);
    $stmt->execute();
	$delete = mysqli_query($con,"DELETE FROM temporal_detalle WHERE TEMPDE_FlagEstado = 0");

	//ACTUALIZAR EL PEDIDO
	$sqlUpdate = "UPDATE cji_pedido SET CLIP_Codigo = $id_cliente, PEDIC_Vendedor = $id_vendedor, ALMAP_Codigo = $id_almacen,  PEDIC_ImporteBruto = $subtotal, PEDIC_IGVTotal = $iva_t,PEDIC_PrecioTotal = $total, PEDIC_FechaModificacion = '$fecha', PEDIC_FechaEntregaMin = '$fechaEntMin', PEDIC_FechaEntregaMax = '$fechaEntMax', PEDIC_Observacion='$obs', FORPAP_Codigo = '$forpapDefault', FORPAP_Monto ='$MontoPedido' WHERE PEDIP_Codigo = $id_factura";
	$query = mysqli_query($con, $sqlUpdate);

	//GET PEDIDOS
	$queryCantidadGet = mysqli_query($con,"SELECT * FROM cji_pedidodetalle WHERE PEDIP_Codigo = $id_factura");
	while($getCant=mysqli_fetch_array($queryCantidadGet)){
		$codi=$getCant['PROD_Codigo'];
		$cantis=$getCant['PEDIDETC_Cantidad'];
		$sqlAlmacen = "UPDATE cji_almacenproducto SET ALMPROD_StockComprometido = ALMPROD_StockComprometido - $cantis WHERE PROD_Codigo = $codi AND ALMAC_Codigo = $id_almacen AND COMPP_Codigo = 1";
		$queryAlmacen = mysqli_query($con, $sqlAlmacen);
	}

	$destroy = mysqli_query($con,"DELETE FROM cji_pedidodetalle WHERE PEDIP_Codigo = $id_factura");
	//ACTUALIZAR EL DETALLE
    if ($query == true) {
		$j=0;
		foreach($allcode as $i){
			$iva_pu = $allpu[$j]/1.18;
			$subtotal=($allpu[$j]*$allcant[$j])/1.18;
			$iva_t = number_format(($allpu[$j]*$allcant[$j])-$subtotal,2,'.','');
			$sqlDeta="INSERT INTO cji_pedidodetalle(PEDIP_Codigo, PROD_Codigo, PROD_Nombre, UNDMED_Codigo, PEDIDETC_Almacen, PEDIDETC_Cantidad, PEDIDETC_PCIGV, PEDIDETC_PSIGV, PEDIDETC_Precio, PEDIDETC_IGV, PEDIDETC_Importe, PEDIDETC_FechaRegistro, PEDIDETC_FlagEstado)
				      VALUES('$id_factura','$allcode[$j]','$allnom[$j]',1, 1,'$allcant[$j]','$allpu[$j]','$iva_pu','$subtotal','$iva_t','$allpt[$j]','$fecha',1)";
			$queryDeta= mysqli_query($con,$sqlDeta);
			$sqlAlmacen = "UPDATE cji_almacenproducto SET ALMPROD_StockComprometido = ALMPROD_StockComprometido + $allcant[$j] WHERE PROD_Codigo = $allcode[$j] AND ALMAC_Codigo = 1 AND COMPP_Codigo = 1";
			$queryAlmacen = mysqli_query($con, $sqlAlmacen);
			$j++;
		}

		if($forpapDefault == 22){
			$j=0;
			$queryFlag0 = mysqli_query($con,"UPDATE cji_pedido_formaspago SET pedi_forPa_flag = 0 WHERE PEDIP_Codigo = $id_factura");
			foreach($allForpap as $i => $val){
				$sqlMultiple = "INSERT INTO cji_pedido_formaspago(PEDIP_Codigo, FORPAP_Codigo, MONED_Codigo, monto, pedi_flag_FechaRegistro)
								VALUES('$id_factura','$val','1','$allMonto[$j]','$fecha')";
				$queryMultiple = mysqli_query($con, $sqlMultiple);
				$j++;
			}
		}

		$stmt->close();
		$json = array(
            "result" => "success",
            "message" => "Pedido actualizado con éxito"
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
<table id="tableEditar" class="table">
<tr>
	<th class='text-center'>CANT.</th>
	<th>DESCRIPCION</th>
	<th></th>
	<th class='text-right'>PRECIO UNIT.</th>
	<th class='text-right'>PRECIO TOTAL</th>
	<th></th>
</tr>
<?php
	$sumador_total=0;

  	$iva_t=0;
	$igv_total=1;
	$impuesto=18;

	if($sessionG){
		$sql = "SELECT * FROM cji_pedidodetalle WHERE PEDIP_Codigo = '$id_factura' AND PEDIDETC_FlagEstado = 1";
		$query=mysqli_query($con, $sql);
		while ($row=mysqli_fetch_array($query))
		{
		//start	
				$id_tmp=$row["PEDIDETP_Codigo"];
				$codigo_producto=$row['PROD_Codigo'];
				$cantidad=$row['PEDIDETC_Cantidad'];
				$nombre_producto=$row['PROD_Nombre'];
				$detalle_adicional=$row['PEDIDETC_Detalle'];
				$precio_m_r=$row['PEDIDETC_PCIGV'];
				$total = $row['PEDIDETC_Importe'];
				$iva_t = $total/1.18;
				$subtotal= $total - $iva_t;
				mysqli_query($con, "INSERT INTO temporal_detalle (PROD_Codigo, TEMPDE_Descripcion, TEMPDE_Cantidad, TEMPDE_Precio,TEMPDE_Subtotal, TEMPDE_Igv, TEMPDE_Total, TEMPDE_CodDetalle, TEMPDE_Observacion, TEMPDE_SESSION, TEMPDE_FlagEstado) 
				VALUES ('$codigo_producto', '$nombre_producto', '$cantidad', '$precio_m_r','$subtotal','$iva_t','$total','$id_tmp','$detalle_adicional','$sessionG', 0)");
		}
		$session = $sessionG;
	}
	
	$sqlTemp = "SELECT * FROM temporal_detalle WHERE TEMPDE_SESSION = '$session'";
	$queryTemp=mysqli_query($con,$sqlTemp);
	$n = 0;

	while ($row=mysqli_fetch_array($queryTemp))
	{	
		$n++;
			$id_tmp=$row["TEMPDE_Codigo"];
			$codigo_producto=$row['PROD_Codigo'];
			$cantidad=$row['TEMPDE_Cantidad'];
			$nombre_producto=$row['TEMPDE_Descripcion'];
			$detalle_adicional=$row['TEMPDE_Observacion'];
			$precio_m_r=$row['TEMPDE_Precio'];
			$total = $row['TEMPDE_Total'];
			?>
				<tr>
					<input type="hidden" value='<?=$codigo_producto?>' id='prod_cod_<?=$n?>'>
					<input type="hidden" value="<?=$codigo_producto?>" id="get_<?=$id_tmp?>">

					<input type="hidden" value='<?=$cantidad?>' id='prod_cant_<?=$n?>'>
					<td class='text-center'><?php echo $cantidad;?></td>

					<input type="hidden" value='<?=$nombre_producto?>' id='prod_nom_<?=$n?>'>
					<td class='text-left'><?php echo $nombre_producto;if($detalle_adicional != null)echo ' - ';echo $detalle_adicional?></td>

					<td></td>
					<input type="hidden" value='<?=$precio_m_r?>' id='prod_pu_<?=$n?>'>
					<td class='text-right'><?php echo $precio_m_r;?></td>

					<input type="hidden" value='<?=$total?>' id='prod_pt_<?=$n?>'>
					<input type="hidden" value='<?=$total?>' id='total_<?=$id_tmp?>'>
					<td class='text-right'><?php echo $total;?></td>
					<td class='text-center'><a href="#" onclick="eliminar('<?=$id_tmp ?>','<?=$session?>')"><button class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></button></a></td>
				</tr>		
				<?php
			$sumador_total += $total;
	}
	
	$subtotal=number_format($sumador_total/1.18,2,'.','');
	$iva_t = number_format($sumador_total - ($sumador_total/1.18),2,'.','');
	$total_factura=$subtotal+$iva_t;

	$update=mysqli_query($con,"update facturas set total_venta='$total_factura' where id_factura='$id_factura'");
?>
<tr>
	<td class='text-right' colspan=4>SUBTOTAL <?php echo $simbolo_moneda;?></td>
	<td class='text-right'><?php echo number_format($subtotal,2);?></td>
	<td></td>
</tr>

<tr>
	<td class='text-right' colspan=4>IGV (<?php echo $impuesto;?>)% <?php echo $simbolo_moneda;?></td>
	<td class='text-right'><?php echo number_format($iva_t,2);?></td>
	<td></td>
</tr>

<tr>
	<td class='text-right' colspan=4>TOTAL <?php echo $simbolo_moneda;?></td>
	<td class='text-right'><?php echo number_format($total_factura,2);?></td>
	<input type="hidden" id="SumaTotal" value = '<?php echo number_format($total_factura, 2); ?>'>
	<td></td>
</tr>

</table>

