<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	session_start();
	error_reporting(0);
	if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
        header("location: ../../login.php");
		exit;
    }
	/* Connect To Database*/
	include("../../config/db.php");
	include("../../config/conexion.php");
	//Archivo de funciones PHP
	include("../../funciones.php");
	$id_factura= intval($_GET['id_factura']);
	$sql_count=mysqli_query($con,"SELECT * FROM cji_pedido WHERE PEDIP_Codigo='".$id_factura."'");
	$count=mysqli_num_rows($sql_count);
	if ($count==0){
		echo "<script>alert('Pedido no encontrado')</script>";
		echo "<script>window.close();</script>";
		exit;
	}
	$styleBorder = "border-left: #cccccc 1mm solid; border-right: #cccccc 1mm solid; border-bottom:#cccccc 1mm solid; border-top:#cccccc 1mm solid;";
	$sqlPedido="SELECT *,
            (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) as documento,
            (CASE c.CLIC_TipoPersona  WHEN '1'
            THEN e.EMPRC_RazonSocial
            ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre 
			FROM cji_pedido p
            INNER JOIN cji_cliente c ON c.CLIP_Codigo=p.CLIP_Codigo
            LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND c.CLIC_TipoPersona ='0'
            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND c.CLIC_TipoPersona='1'
			WHERE p.PEDIP_Codigo = '$id_factura'";
	$queryP=mysqli_query($con,$sqlPedido);
	$pedidoDatos=mysqli_fetch_array($queryP);

	$queryPedidosDetalle 	= mysqli_query($con, "SELECT * FROM cji_pedidodetalle WHERE PEDIP_Codigo = $id_factura");
	$row1 = mysqli_fetch_assoc($queryPedidosDetalle);
	$detalleP[] = $row1;
	while($row = mysqli_fetch_assoc($queryPedidosDetalle)){
		$detalleP[] = $row;
	}
	$compania 	= $pedidoDatos['COMPP_Codigo'];
	$documento	= $pedidoDatos['documento'];
	$serie 	   	= $pedidoDatos['PEDIC_Serie'];
	$numero 	= $pedidoDatos['PEDIC_Numero'];
	$nombre		= $pedidoDatos['nombre'];
	$documento	= $pedidoDatos['documento'];
	$direccion	= $pedidoDatos['PEDIC_Direccion'];
	$fechaR		= $pedidoDatos['PEDIC_FechaRegistro'];
	$fechaEMIN	= $pedidoDatos['PEDIC_FechaEntregaMin'];
	$fechaEMAX	= $pedidoDatos['PEDIC_FechaEntregaMax'];
	$estado 	= $pedidoDatos['PEDIC_FlagEstado'];
	if($fechaEMIN == "0000-00-00 00:00:00"){
		$fechaEMIN = "";
	}else{
		$fechaEMIN = $fechaEMIN." - ";
	}

	if($fechaEMAX == "0000-00-00 00:00:00"){
		$fechaEMAX = "";
	}


	//DATOS DE LA EMPRESA
	$queryCompania 	= mysqli_query($con,"SELECT * FROM cji_compania WHERE COMPP_Codigo = '$compania'");
	$datosCompania 	= mysqli_fetch_array($queryCompania);
	$empresaCodigo 	= $datosCompania['EMPRP_Codigo'];

	$queryEmpresa 	= mysqli_query($con, "SELECT * FROM cji_empresa WHERE EMPRP_Codigo = '$empresaCodigo'");
	$datosEmpresa 	= mysqli_fetch_array($queryEmpresa);
	$rucEmpresa		= $datosEmpresa['EMPRC_Ruc'];
	$nombreEmpresa  = $datosEmpresa['EMPRC_RazonSocial'];

	$sqlEstablecimiento 	= "SELECT es.*, u.UBIGC_DescripcionDpto as departamento, u.UBIGC_DescripcionProv as provincia, u.UBIGC_Descripcion as distrito
						  	FROM cji_emprestablecimiento es
						  	INNER JOIN cji_ubigeo u ON u.UBIGP_Codigo = es.UBIGP_Codigo
						  	INNER JOIN cji_compania c ON c.EESTABP_Codigo = es.EESTABP_Codigo
						  	WHERE es.EMPRP_Codigo = $empresaCodigo AND c.COMPP_Codigo = $compania";
	$queryEstablecimiento 		= mysqli_query($con, $sqlEstablecimiento);
	$datosEstablecimiento 		= mysqli_fetch_array($queryEstablecimiento);
	$direccionEstablecimiento 	= $datosEstablecimiento['EESTAC_Direccion'];
	$distritoEstablecimiento 	= $datosEstablecimiento['distrito'];
	$provinciaEstablecimiento 	= $datosEstablecimiento['provincia'];
	$departEstablecimiento 		= $datosEstablecimiento['departamento'];

	


	$simbolo_moneda=get_row('perfil','moneda', 'id_perfil', 1);

	require_once('../../TCPDF/tcpdf.php');           
    header('Content-type: application/pdf');
    $pdf = new TCPDF('P', 'mm', 'a4', true, 'UTF-8', false); 
	$pdf->SetCreator(PDF_CREATOR); 
    $pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
    $pdf->SetDefaultMonospacedFont('helvetica');  
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
    $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
    $pdf->setPrintHeader(false);  
    $pdf->setPrintFooter(true);  
    $pdf->SetAutoPageBreak(TRUE, 10);  
    $pdf->SetFont('helvetica', '', 11);  
    $pdf->AddPage();  
	$pdf->SetMargins(7, 50, 10);

	
	$posY = 15;
    $posX = 139;
    $pdf->RoundedRect($posX, $posY, 60, 35, 1.50, '1111', '');
    $pdf->SetY($posY + 4);
    $pdf->SetX($posX);
	$sumatotal = 0;

	foreach($detalleP as $i => $val){
		$codigo				= $val['PROD_Codigo'];
        $nombre_producto 	= $val['PROD_Nombre'];
		$marca 				= "";
		$cantidad 			= $val['PEDIDETC_Cantidad'];
		$precio 			= $val['PEDIDETC_PCIGV'];
		$total	 			= $val['PEDIDETC_Importe'];
  
        $bgcolor = ($indice % 2 == 0) ? "#FFFFFF" : "#F1F1F1";
        $detaProductos = $detaProductos . '
              <tr bgcolor="' . $bgcolor . '">
              <td style="' . $styleBorder . ' text-align:center;">' . $codigo . '</td>
              <td style="' . $styleBorder . ' text-align:left;">' . $nombre_producto . '</td>
              <td style="' . $styleBorder . ' text-align:center;">' . $marca . '</td>
              <td style="' . $styleBorder . ' text-align:center;">' . $cantidad . '</td>
              <td style="' . $styleBorder . ' text-align:right;">' . number_format($precio, 2) . '</td>
              <td style="' . $styleBorder . ' text-align:right;">' . number_format($total, 2) . '</td>
              </tr>';
		$sumatotal += $total;
	}
	$rucHTML = '<tr>
                	<td style="">R.U.C. '.$rucEmpresa.'</td>
                </tr>';

    $comprobanteHTML = '<table style="text-align:center; line-height:20pt; width:6cm; font-weight:bold; font-size:14pt;" border="0">
                            '.$rucHTML.'
                        <tr>
                            <td style="">ORDEN DE <br> PEDIDO</td>
                        </tr>
                        <tr>
                            <td style="">'.$serie.' - '.$numero.'</td>
                        </tr>
                    </table>';

        $pdf->writeHTML($comprobanteHTML,true,false,true,'');
        $pdf->SetX(10);
        //SE IMPRIME EL LOGO DE LA EMPRESA RAZON ANCHO/ALTO=4.7

        $logo_empresa = '../../img/logo_1.jpg';
        $pdf->Image($logo_empresa, 10, 8, 60, 23, '', '', '', false, 300);

        $pdf->SetY(34);

        $comprobanteHTML = '<table style="width:12cm; font-size:8pt;" border="0">
        <tr>
        <td style="font-weight:bold;">'.$nombreEmpresa.'</td>
        </tr>
        <tr>
        <td>'.$direccionEstablecimiento.'<br>'.$distritoEstablecimiento.'-'.$provinciaEstablecimiento.'-'.$departEstablecimiento.'<br>---
        </td>
        </tr>
        </table>';
	$pdf->writeHTML($comprobanteHTML,true,false,true,'');
	$pdf->SetY(55);

	$clienteHTML = '<table style="font-size:8pt;" cellpadding="0.1cm" border="0">
		<tr>
			<td style="width:3.0cm; font-style:normal; font-weight:bold;">NOMBRE:</td>
			<td style="width:auto; text-indent:0.1cm; text-align:justification">' . $nombre . '</td>
		</tr>
		<tr>
			<td style="width:3.0cm; font-style:normal; font-weight:bold;">RUC/DNI:</td>
			<td style="width:auto; text-indent:0.1cm;">' . $documento . '</td>
		</tr>
		<tr> 
			<td style="width:3.0cm; font-style:normal; font-weight:bold;">DIRECCIÓN:</td>
			<td style="width:auto; text-indent:0.1cm; text-align:justification">' . $direccion . '</td>
		</tr>
		<tr>
			<td style="width:3.0cm; font-style:normal; font-weight:bold;">FECHA DE ELABORACIÓN:</td>
			<td style="width:auto; text-indent:0.1cm; text-align:justification">' . $fechaR . '</td>
		</tr>
		<tr>
			<td style="width:3.0cm; font-style:normal; font-weight:bold;">FECHA DE ENTREGA:</td>
			<td style="width:auto; text-indent:0.1cm; text-align:justification">' . $fechaEMIN . $fechaEMAX.'</td>
		</tr>
	</table>';

  	$pdf->writeHTML($clienteHTML, true, false, true, '');

	$productoHTML = '
  	<table cellpadding="0.05cm" style="font-size:8.5pt; width:100%; border-collapse:collapse;" 1px solid black; text-align>
		<tr bgcolor="#F1F1F1" style="font-size:8.5pt;">
			<th style="' . $styleBorder . ' font-style:italic; font-weight:bold; text-align:center; width:2.0cm;">Código</th>
			<th style="' . $styleBorder . ' font-style:italic; font-weight:bold; text-align:center; width:10.0cm;">Descripción</th>
			<th style="' . $styleBorder . ' font-style:italic; font-weight:bold; text-align:center; width:2.5cm;">Marca</th>
			<th style="' . $styleBorder . ' font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">Cantidad</th>
			<th style="' . $styleBorder . ' font-style:italic; font-weight:bold; text-align:center; width:1.5cm;">P/U</th>
			<th style="' . $styleBorder . ' font-style:italic; font-weight:bold; text-align:center; width:1.75cm;">Total</th>
		</tr>
		' . $detaProductos . '
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td style="text-align:center;">TOTAL</td>
			<td style="' . $styleBorder . '">S/.</td>
			<td style="' . $styleBorder . 'text-align:right;">'.$sumatotal.'</td>
		</tr>
		<tr>
			<td colspan="7" style="height:10cm;"></td> <!-- Ajusta la altura según sea necesario -->
		</tr>
	</table>';

	$pdf->writeHTML($productoHTML, true, false, true, '');
    if ($estado == 3) {
		$pdf->Image("../../img/anulado.png", 30, 30, 150, 150, '', '', '', false, 300);
	}

	$nombre='ORDEN DE PEDIDO '.$serie.'-'.$numero.'.pdf';
	$pdf->SetTitle($nombre);
	$pdf->Output($nombre, 'D');  
