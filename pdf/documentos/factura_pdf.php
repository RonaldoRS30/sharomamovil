<?php
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
	$session_id= session_id();
	$sql_count=mysqli_query($con,"select * from temporal_detalle where TEMPDE_SESSION='".$session_id."'");
	$count=mysqli_num_rows($sql_count);
	if ($count==0)
	{
	echo "<script>alert('No hay productos agregados a la factura')</script>";
	echo "<script>window.close();</script>";
	exit;
	}

	require_once(dirname(__FILE__).'/../html2pdf.class.php');
		
	//Variables por GET
	$tipo_doc=$_GET["tipo"];
	$id_cliente=intval($_GET['id_cliente']);
	$id_vendedor=intval($_GET['id_vendedor']);
	$condiciones=mysqli_real_escape_string($con,(strip_tags($_REQUEST['condiciones'], ENT_QUOTES)));

	//Fin de variables por GET
	$sql=mysqli_query($con, "select LAST_INSERT_ID(PEDIC_Numero) as last from cji_pedido order by PEDIP_Codigo desc limit 0,1 ");
	$rw=mysqli_fetch_array($sql);
	$numero_factura=$rw['last']+1;	

	$count_q   = mysqli_query($con, "SELECT count(*) AS numrows FROM cji_pedido WHERE PEDIC_TipoDocume='".$tipo_doc."'");
	$row5= mysqli_fetch_array($count_q);
	$numrows5 = $row5['numrows'];

	if ($numrows5>0) 
	{
			$select_tipo="SELECT PEDIC_Numero FROM cji_pedido WHERE PEDIC_TipoDocume='".$tipo_doc."' order by PEDIP_Codigo DESC LIMIT 1";
			$count_s   = mysqli_query($con, $select_tipo);
			$row6= mysqli_fetch_array($count_s);
			$numero_f=$row6["PEDIC_Numero"]+1;
	}
	else
	{
		$numero_f=1;
	}


	$simbolo_moneda=get_row('perfil','moneda', 'id_perfil', 1);
    // get the HTML
     ob_start();
     include(dirname('__FILE__').'/res/factura_html.php');
    $content = ob_get_clean();

    try
    {
        // init HTML2PDF
        $html2pdf = new HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(0, 0, 0, 0));
        // display the full page
        $html2pdf->pdf->SetDisplayMode('fullpage');
        // convert
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        // send the PDF
        $html2pdf->Output('Factura.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
