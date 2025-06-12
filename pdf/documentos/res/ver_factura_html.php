<style type="text/css">

table {
    vertical-align: top;
}

tr {
    vertical-align: top;
}

td {
    vertical-align: top;
}

.midnight-blue {
    background: #2c3e50;
    padding: 4px 4px 4px;
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.silver {
    background: white;
    padding: 3px 4px 3px;
}

.clouds {
    background: #ecf0f1;
    padding: 3px 4px 3px;
}

.border-top {
    border-top: solid 1px #bdc3c7;

}

.border-left {
    border-left: solid 1px #bdc3c7;
}

.border-right {
    border-right: solid 1px #bdc3c7;
}

.border-bottom {
    border-bottom: solid 1px #bdc3c7;
}

table.page_footer {
    width: 100%;
    border: none;
    background-color: white;
    padding: 2mm;
    border-collapse: collapse;
    border: none;
}

</style>
<page backtop="15mm" backbottom="15mm" backleft="15mm" backright="15mm" style="font-size: 12pt; font-family: arial">
    <page_footer>
        <table class="page_footer">
            <tr>

                <td style="width: 50%; text-align: left">
                    P&aacute;gina [[page_cu]]/[[page_nb]]
                </td>
                <td style="width: 50%; text-align: right">
                    &copy; <?php echo  $anio=date('Y'); ?>
                </td>
            </tr>
        </table>
    </page_footer>
    <?php include("encabezado_factura.php");?>
    <br>

    <table cellspacing="2" style="width: 135%; text-align: left; font-size: 11pt;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div style="border: 1px solid black; border-radius: 5px; padding: 5px; width: 100%; text-align: left;">
                    <strong>CLIENTE</strong><br>
                    <?php 
                    $sql_cliente = mysqli_query($con, "select * from clientes where id_cliente='$id_cliente'");
                    $rw_cliente = mysqli_fetch_array($sql_cliente);
					echo "<br><strong>RUC:</strong> " . $rw_cliente['num_cliente']. "<br>";
					echo "<br><strong>DENOMINACION:</strong> " . $rw_cliente['nombre_cliente'] . "<br>";
					echo "<strong>NÚMERO:</strong> " . $rw_cliente['num_cliente'] . "<br>";
                    echo "<strong>DIRECCION:</strong> " . $rw_cliente['direccion_cliente'] . "<br>";
                ?><br>
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <div style="border: 1px solid black; border-radius: 5px; padding: 5px; width: 50%; text-align: left;">
                    <table cellspacing="0" style="width: 100%; font-size: 11pt; border-spacing: 0;">
                        <tr>
                            <td style="width: 100%; font-weight: bold;">VENDEDOR</td>
                        </tr>
                        <tr>
                            <td>
                                <?php 
                                $sql_user = mysqli_query($con, "select * from users where user_id='$id_vendedor'");
                                $rw_user = mysqli_fetch_array($sql_user);
                                echo $rw_user['firstname'] . " " . $rw_user['lastname'];
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%; font-weight: bold;">FECHA DE EMISION</td>
                        </tr>
                        <tr>
                            <td><?php echo date("d/m/Y"); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 100%; font-weight: bold;">FORMA DE PAGO</td>
                        </tr>
                        <tr>
                            <td>
                                <?php 
                                if ($condiciones == 1) { echo "Efectivo"; }
                                elseif ($condiciones == 2) { echo "Cheque"; }
                                elseif ($condiciones == 3) { echo "Transferencia bancaria"; }
                                elseif ($condiciones == 4) { echo "Crédito"; }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100%; font-weight: bold;">TIPO DE MONEDA</td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                if ($condiciones == 1) { echo "Soles"; }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
    <br>

    <table cellspacing="0" style="width: 100%; text-align: left; font-size: 10pt;">
        <tr>
            <th style="width: 10%;text-align:center" class='midnight-blue'>CANT.</th>
            <th style="width: 60%" class='midnight-blue'>DESCRIPCION</th>
            <th style="width: 15%;text-align: right" class='midnight-blue'>PRECIO UNIT.</th>
            <th style="width: 15%;text-align: right" class='midnight-blue'>PRECIO TOTAL</th>

        </tr>

        <?php
$nums=1;
$sumador_total=0;
$sql=mysqli_query($con, "select * from products, detalle_factura, facturas where products.id_producto=detalle_factura.id_producto and detalle_factura.numero_factura=facturas.numero_factura and facturas.id_factura='".$id_factura."'");

	$var1=0;
	$var2=0;
	$var3=0;
	$var4=0;

	$iva_t=0;
	$igv_total=get_row('facturas','igv', 'id_factura', $id_factura);
	$impuesto=get_row('perfil','impuesto', 'id_perfil', 1);

while ($row=mysqli_fetch_array($sql))
	{
		$igv_get=get_row('products','igv', 'id_producto', $row['id_producto']);

		if ($igv_total==1)
		{

			if ($igv_get==1) 
			{
				

				$id_producto=$row["id_producto"];
				$codigo_producto=$row['codigo_producto'];
				$cantidad=$row['cantidad'];
				$nombre_producto=$row['nombre_producto'];
                $detalle_adicional=$row['detalle_adicional'];
                $detalle = $row['detalle'];

				$precio_venta=$row['precio_producto'];
				$precio_m=(100*$precio_venta)/118;
				$precio_m_f=number_format($precio_m,2);//Formateo variables
				$precio_m_r=str_replace(",","",$precio_m_f);//Reemplazo las comas



				$precio_total=$precio_m_r*$cantidad;

							

				$precio_total_f=number_format($precio_total,2);//Precio total formateado
				$precio_total_r=str_replace(",","",$precio_total_f);//Reemplazo las comas
				$sumador_total+=$precio_total_r;//Sumador

				$iva_t+=($precio_venta-$precio_m_r)*$cantidad;

					if ($nums%2==0){
						$clase="clouds";
					} else {
						$clase="silver";
					}
					?>

        <tr>
            <td class='<?php echo $clase;?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
            <td class='<?php echo $clase;?>' style="width: 60%; text-align: left"><?php echo $nombre_producto;echo '  ';echo $detalle;echo ' - ';echo $detalle_adicional?></td>
            <td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_m_r;?></td>
            <td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_total_r;?></td>

        </tr>

        <?php 

			}
			else
			{
				
				$id_producto=$row["id_producto"];
				$codigo_producto=$row['codigo_producto'];
				$cantidad=$row['cantidad'];
				$nombre_producto=$row['nombre_producto'];
                $detalle_adicional=$row['detalle_adicional'];
                $detalle = $row['detalle'];
							
				$precio_venta=$row['precio_producto'];
				$precio_m_f=number_format($precio_venta,2);//Formateo variables
				$precio_m_r=str_replace(",","",$precio_m_f);//Reemplazo las comas



				$precio_total=$precio_m_r*$cantidad;

							

				$precio_total_f=number_format($precio_total,2);//Precio total formateado
				$precio_total_r=str_replace(",","",$precio_total_f);//Reemplazo las comas
				$sumador_total+=$precio_total_r;//Sumador

				$iva_t+=0;

					if ($nums%2==0){
						$clase="clouds";
					} else {
						$clase="silver";
					}
					?>

        <tr>
            <td class='<?php echo $clase;?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
            <td class='<?php echo $clase;?>' style="width: 60%; text-align: left"><?php echo $nombre_producto;echo '  ';echo $detalle;echo ' - ';echo $detalle_adicional?></td>
            <td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_m_r;?></td>
            <td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_total_r;?></td>

        </tr>

        <?php 


			}
				


			
		}
		else
		{

			if ($igv_get==1) 
			{
				
				$id_producto=$row["id_producto"];
				$codigo_producto=$row['codigo_producto'];
				$cantidad=$row['cantidad'];
				$nombre_producto=$row['nombre_producto'];
                $detalle_adicional=$row['detalle_adicional'];
                $detalle = $row['detalle'];

				//$precio_venta=$row['precio_producto'];
				//$precio_m=(100*$precio_venta)/118;
				//$precio_m_f=number_format($precio_m,2);//Formateo variables
				//$precio_m_r=str_replace(",","",$precio_m_f);//Reemplazo las comas
				$var=($row['precio_producto']*$impuesto)/118;
				$var2=$row['precio_producto']+$var;
				$var3=$row['precio_producto'];


				$precio_m_f2=number_format($var3,2);//Formateo variables
				$precio_m_r2=str_replace(",","",$precio_m_f2);//Reemplazo las comas

				$precio_m_f=number_format($var2,2);//Formateo variables
				$precio_m_r=str_replace(",","",$precio_m_f);//Reemplazo las comas

				$precio_total=$precio_m_r*$cantidad;

				//---------------------------------------------------------------

				$precio_total2=$precio_m_r2*$cantidad;

				$precio_total_f2=number_format($precio_total2,2);//Precio total formateado
				$precio_total_r2=str_replace(",","",$precio_total_f2);//Reemplazo las comas
				

				$precio_total_f=number_format($precio_total,2);//Precio total formateado
				$precio_total_r=str_replace(",","",$precio_total_f);//Reemplazo las comas
				$sumador_total+=$precio_total_r2;//Sumador

				$iva_t+=$var*$cantidad; 



					if ($nums%2==0){
						$clase="clouds";
					} else {
						$clase="silver";
					}
					?>

        <tr>
            <td class='<?php echo $clase;?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
            <td class='<?php echo $clase;?>' style="width: 60%; text-align: left"><?php echo $nombre_producto;echo '  ';echo $detalle;echo ' - ';echo $detalle_adicional?></td>
            <td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_m_r2;?></td>
            <td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_total_r2;?></td>

        </tr>

        <?php 
			}
			else
			{
				
				$id_producto=$row["id_producto"];
				$codigo_producto=$row['codigo_producto'];
				$cantidad=$row['cantidad'];
				$nombre_producto=$row['nombre_producto'];
                $detalle_adicional=$row['detalle_adicional'];
                $detalle = $row['detalle'];
							
				$precio_venta=$row['precio_producto'];
				$precio_m_f=number_format($precio_venta,2);//Formateo variables
				$precio_m_r=str_replace(",","",$precio_m_f);//Reemplazo las comas



				$precio_total=$precio_m_r*$cantidad;

							

				$precio_total_f=number_format($precio_total,2);//Precio total formateado
				$precio_total_r=str_replace(",","",$precio_total_f);//Reemplazo las comas
				$sumador_total+=$precio_total_r;//Sumador

				$iva_t+=0;

					if ($nums%2==0){
						$clase="clouds";
					} else {
						$clase="silver";
					}
					?>

        <tr>
            <td class='<?php echo $clase;?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
            <td class='<?php echo $clase;?>' style="width: 60%; text-align: left"><?php echo $nombre_producto;echo '  ';echo $detalle;
            ;echo ' - ';echo $detalle_adicional?></td>
            <td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_m_r;?></td>
            <td class='<?php echo $clase;?>' style="width: 15%; text-align: right"><?php echo $precio_total_r;?></td>

        </tr>

        <?php 
			}


		}
			
	

	
	$nums++;
	}


	$subtotal=number_format($sumador_total,2,'.','');

	$total_factura=$subtotal+$iva_t;

?>



    </table>

    <!-- Subtotal, IGV, and Total displayed below the table -->
    <div style="width: 100%; text-align: right; font-size: 10pt; margin-top: 20px; border: 1px solid #000; padding: 10px; border-radius: 5px;">
    <!-- Cuadro para subtotales, IGV y total -->
    <p style="margin: 0; margin-bottom: 10px;">
        <strong>SUBTOTAL</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $simbolo_moneda; ?>
        <?php echo number_format($subtotal, 2); ?>
    </p>
    
    <p style="margin: 0; margin-bottom: 10px;">
        <strong>IGV (<?php echo $impuesto; ?>)%</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $simbolo_moneda; ?>
        <?php echo number_format($iva_t, 2); ?>
    </p>
    
    <p style="margin: 0;">
        <strong>TOTAL</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $simbolo_moneda; ?>
        <?php echo number_format($total_factura, 2); ?>
    </p>
</div>


<?php
function numberToWords($num) {
    $ones = [
        0 => 'cero', 1 => 'uno', 2 => 'dos', 3 => 'tres', 4 => 'cuatro',
        5 => 'cinco', 6 => 'seis', 7 => 'siete', 8 => 'ocho', 9 => 'nueve',
        10 => 'diez', 11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce',
        15 => 'quince', 16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho',
        19 => 'diecinueve', 20 => 'veinte'
    ];

    $tens = [
        30 => 'treinta', 40 => 'cuarenta', 50 => 'cincuenta', 60 => 'sesenta',
        70 => 'setenta', 80 => 'ochenta', 90 => 'noventa'
    ];

    $hundreds = [
        100 => 'cien', 200 => 'doscientos', 300 => 'trescientos',
        400 => 'cuatrocientos', 500 => 'quinientos', 600 => 'seiscientos',
        700 => 'setecientos', 800 => 'ochocientos', 900 => 'novecientos'
    ];

    if ($num < 21) {
        return $ones[$num];
    } elseif ($num < 100) {
        $dec = intval($num / 10) * 10;
        $unit = $num % 10;
        return $tens[$dec] . ($unit > 0 ? ' y ' . $ones[$unit] : '');
    } elseif ($num < 200) { // Para números entre 100 y 199
        $rest = $num % 100;
        return 'ciento' . ($rest > 0 ? ' ' . numberToWords($rest) : '');
    } elseif ($num < 1000) {
        $hund = intval($num / 100) * 100;
        $rest = $num % 100;
        return $hundreds[$hund] . ($rest > 0 ? ' ' . numberToWords($rest) : '');
    }

    return '';
}

$total_factura = $subtotal + $iva_t;

foreach ($productos as $producto) {
    $total_factura += $producto['precio_total'];
}

$total_en_texto = numberToWords($total_factura);

?>
<table>
        <td colspan='3'></td>
        <td><?php echo ucfirst($total_en_texto) . " Nuevos Soles"; ?></td> <!-- Muestra el total en texto -->
</table>

<br>

<div style="width: 100%; text-align: justify; font-size: 10pt; margin-top: 20px; border: 1px solid #000; padding: 10px; border-radius: 5px;">
    Esta es una representación impresa de la Boleta de Venta Electrónica, generada en el Sistema de la SUNAT. El Emisor Electrónico puede
    verificarla utilizando su clave SOL. El Adquirente o Usuario puede consultar su validez en SUNAT Virtual: www.sunat.gob.pe, en Opciones sin
    Clave SOL/Consulta de Validez del CPE.
</div>

<div style="font-size: 11pt; text-align: center; font-weight: bold;">
    ¡Gracias por su compra!
</div>


</page>