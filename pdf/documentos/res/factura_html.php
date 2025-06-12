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
<page backtop="" backbottom="15mm" backleft="15mm" backright="15mm" style="font-size: 12pt; font-family: arial">
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 50%; text-align: left">
                    P&aacute;gina [[page_cu]]/[[page_nb]]
                </td>
                <td style="width: 50%; text-align: right">
                    &copy; <?php echo $anio = date('Y'); ?>
                </td>
            </tr>
        </table>
    </page_footer>

    <?php include("encabezado_factura.php"); ?>

    <br>

    <table cellspacing="0" style="width: 135%; text-align: left; font-size: 11pt;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div style="border: 1px solid black; border-radius: 5px; padding: 10px; width: 100%; text-align: left;">
                    <strong>CLIENTE</strong><br>
                    <?php
                    $sql_cliente = mysqli_query($con, "select * from clientes where id_cliente='$id_cliente'");
                    $rw_cliente = mysqli_fetch_array($sql_cliente);
                    $doc = $rw_cliente['tipo_doc'] == 1 ? 'DNI' : 'RUC';
                    echo "<strong>" . $doc . "</strong> " . $rw_cliente['num_cliente'] . "<br>";
                    echo "<strong>DENOMINACION:</strong> " . $rw_cliente['nombre_cliente'] . "<br>";
                    echo "<strong>NÚMERO:</strong> " . $rw_cliente['num_cliente'] . "<br>";
                    echo "<strong>DIRECCION:</strong> " . $rw_cliente['direccion_cliente'] . "<br>";
                    ?>
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <div style="border: 1px solid black; border-radius: 5px; padding: 5px; width: 50%; text-align: left;">
                    <table cellspacing="0" style="width: 100%; font-size: 11pt; border-spacing: 0;">
                        <tr>
                            <td style="width: 100%; font-weight: bold;">FECHA</td>
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
                                if ($condiciones == 1) {
                                    echo "Efectivo";
                                } elseif ($condiciones == 2) {
                                    echo "Cheque";
                                } elseif ($condiciones == 3) {
                                    echo "Transferencia bancaria";
                                } elseif ($condiciones == 4) {
                                    echo "Crédito";
                                }
                                ?>
                            </td>
                        </tr>
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
            <th style="width: 10%; text-align: center" class='midnight-blue'>CANT.</th>
            <th style="width: 60%" class='midnight-blue'>DESCRIPCION</th>
            <th style="width: 15%; text-align: right" class='midnight-blue'>PRECIO UNIT.</th>
            <th style="width: 15%; text-align: right" class='midnight-blue'>PRECIO TOTAL</th>
        </tr>

        <?php
        require_once("../../funciones.php");
        $nums = 1;
        $sumador_total = 0;
        $sql = mysqli_query($con, "SELECT * FROM cji_producto, temporal_detalle WHERE cji_producto.PROD_Codigo = temporal_detalle.PROD_Codigo AND temporal_detalle.TEMPDE_SESSION = '" . $session_id . "'");
        $var21 = 0;
        $var22 = 0;
        $var23 = 0;
        $var24 = 0;

        $iva_t = 0;
        $igv_total = get_row('perfil', 'igv_total', 'id_perfil', 1);
        $impuesto = get_row('perfil', 'impuesto', 'id_perfil', 1);

        while ($row = mysqli_fetch_array($sql)) {
            $igv_get = get_row('products', 'igv', 'id_producto', $row['id_producto']);

            if ($igv_total == 1) {
                if ($igv_get == 1) {
                    $var21++;
                    //annel detalle
                    $id_producto = $row["id_producto"];
                    $codigo_producto = $row['codigo_producto'];
                    $cantidad = $row['cantidad_tmp'];
                    $nombre_producto = $row['nombre_producto'];
                    $detalle_adicional_tmp = $row['detalle_adicional_tmp'];

                    $precio_venta = $row['precio_tmp'];
                    $precio_m = (100 * $precio_venta) / 118;
                    $precio_m_f = number_format($precio_m, 2);
                    $precio_m_r = str_replace(",", "", $precio_m_f);

                    $precio_total = $precio_m_r * $cantidad;

                    $precio_total_f = number_format($precio_total, 2);
                    $precio_total_r = str_replace(",", "", $precio_total_f);
                    $sumador_total += $precio_total_r;

                    $iva_t += ($precio_venta - $precio_m_r) * $cantidad;

                    $clase = ($nums % 2 == 0) ? "clouds" : "silver";
                    ?>
                    /*annel detalle*/
                    <tr>
                        <td class='<?php echo $clase; ?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
                        <td class='<?php echo $clase; ?>' style="width: 60%; text-align: left">
                            <?php echo $nombre_producto;
                            echo $detalle;
                            echo ' - ';
                            echo $detalle_adicional_tmp ?>
                        </td>
                        <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_m_r; ?></td>
                        <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_total_r; ?></td>
                    </tr>
                    <?php
                } else {
                    $var22++;
                    $id_producto = $row["id_producto"];
                    $codigo_producto = $row['codigo_producto'];
                    $cantidad = $row['cantidad_tmp'];
                    $nombre_producto = $row['nombre_producto'];
                    $detalle_adicional_tmp = $row['detalle_adicional_tmp'];
                    $detalle = $row['detalle'];


                    $precio_venta = $row['precio_tmp'];
                    $precio_m_f = number_format($precio_venta, 2);
                    $precio_m_r = str_replace(",", "", $precio_m_f);

                    $precio_total = $precio_m_r * $cantidad;

                    $precio_total_f = number_format($precio_total, 2);
                    $precio_total_r = str_replace(",", "", $precio_total_f);
                    $sumador_total += $precio_total_r;

                    $iva_t += 0;

                    $clase = ($nums % 2 == 0) ? "clouds" : "silver";
                    ?>
                    <tr>
                        <td class='<?php echo $clase; ?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
                        <td class='<?php echo $clase; ?>' style="width: 60%; text-align: left">
                            <?php echo $nombre_producto;
                            echo $detalle;
                            echo ' - ';
                            echo $detalle_adicional_tmp ?>
                        </td>
                        <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_m_r; ?></td>
                        <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_total_r; ?></td>
                    </tr>
                    <?php
                }
            } else {
                if ($igv_get == 1) {
                    $var23++;
                    $id_producto = $row["id_producto"];
                    $codigo_producto = $row['codigo_producto'];
                    $cantidad = $row['cantidad_tmp'];
                    $nombre_producto = $row['nombre_producto'];
                    $detalle_adicional_tmp = $row['detalle_adicional_tmp'];
                    $detalle = $row['detalle'];

                    $var = ($row['precio_tmp'] * $impuesto) / 118;
                    $var2 = $row['precio_tmp'] + $var;
                    $var3 = $row['precio_tmp'];

                    $precio_m_f2 = number_format($var3, 2);
                    $precio_m_r2 = str_replace(",", "", $precio_m_f2);

                    $precio_m_f = number_format($var2, 2);
                    $precio_m_r = str_replace(",", "", $precio_m_f);

                    $precio_total = $precio_m_r * $cantidad;

                    $precio_total2 = $precio_m_r2 * $cantidad;

                    $precio_total_f2 = number_format($precio_total2, 2);
                    $precio_total_r2 = str_replace(",", "", $precio_total_f2);

                    $precio_total_f = number_format($precio_total, 2);
                    $precio_total_r = str_replace(",", "", $precio_total_f);
                    $sumador_total += $precio_total_r2;

                    $iva_t += $var * $cantidad;

                    $clase = ($nums % 2 == 0) ? "clouds" : "silver";
                    ?>
                    <tr>
                        <td class='<?php echo $clase; ?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
                        <td class='<?php echo $clase; ?>' style="width: 60%; text-align: left">
                            <?php echo $nombre_producto;
                            echo $detalle;
                            echo ' - ';
                            echo $detalle_adicional_tmp ?>
                        </td>
                        <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_m_r2; ?></td>
                        <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_total_r2; ?></td>
                    </tr>
                    <?php
                } else {
                    $var24++;
                    $id_producto = $row["id_producto"];
                    $codigo_producto = $row['codigo_producto'];
                    $cantidad = $row['cantidad_tmp'];
                    $nombre_producto = $row['nombre_producto'];
                    $detalle_adicional_tmp = $row['detalle_adicional_tmp'];
                    $detalle = $row['detalle'];

                    $precio_venta = $row['precio_tmp'];
                    $precio_m_f = number_format($precio_venta, 2);
                    $precio_m_r = str_replace(",", "", $precio_m_f);

                    $precio_total = $precio_m_r * $cantidad;

                    $precio_total_f = number_format($precio_total, 2);
                    $detalle_adicional_tmp = $row['detalle_adicional_tmp'];
                    $precio_total_r = str_replace(",", "", $precio_total_f);
                    $sumador_total += $precio_total_r;

                    $iva_t += 0;

                    $clase = ($nums % 2 == 0) ? "clouds" : "silver";
                    ?>
                    <tr>
                        <td class='<?php echo $clase; ?>' style="width: 10%; text-align: center"><?php echo $cantidad; ?></td>
                        <td class='<?php echo $clase; ?>' style="width: 60%; text-align: left">
                            <?php echo $nombre_producto;
                            echo $detalle;
                            echo ' - ';
                            echo $detalle_adicional_tmp ?>
                        </td>
                        <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_m_r; ?></td>
                        <td class='<?php echo $clase; ?>' style="width: 15%; text-align: right"><?php echo $precio_total_r; ?></td>
                    </tr>
                    <?php
                }
            }

            $insert_detail = mysqli_query($con, "INSERT INTO detalle_factura VALUES ('', '$numero_factura', '$id_producto', '$detalle_adicional_tmp','$cantidad', '$precio_venta')");

            $nums++;
        }

        $subtotal = number_format($sumador_total, 2, '.', '');
        $total_factura = $subtotal + $iva_t;
        ?>

    </table>

    <!-- Subtotal, IGV, and Total displayed below the table -->
    <div style="width: 100%; text-align: right; font-size: 10pt; margin-top: 20px;">
        <!-- Ajusta el margen superior según sea necesario -->
        <p style="margin: 0; margin-bottom: 10px; margin-left: 10px;">
            SUBTOTAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $simbolo_moneda; ?>
            <?php echo number_format($subtotal, 2); ?>
        </p>
        <p style="margin: 0; margin-bottom: 10px; margin-left: 10px;">IGV
            (<?php echo $impuesto; ?>)%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $simbolo_moneda; ?>
            <?php echo number_format($iva_t, 2); ?>
        </p>
        <p style="margin: 0; margin-left: 10px;">TOTAL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $simbolo_moneda; ?>
            <?php echo number_format($total_factura, 2); ?>
        </p>
    </div>




    <br>
    <div style="font-size:11pt;text-align:center;font-weight:bold">Gracias por su compra!</div>





</page>

<?php

$date = date("Y-m-d H:i:s");
$insert = mysqli_query($con, "INSERT INTO facturas VALUES (NULL,'$numero_factura','$date','$id_cliente','$id_vendedor','$condiciones','$total_factura','1','$igv_total','0','null')");
$delete = mysqli_query($con, "DELETE FROM tmp WHERE session_id='" . $session_id . "'");





if ($numrows5 > 0) {


    $new = $row6["cod_doc"] + 1;
    $update_tipo = mysqli_query($con, "UPDATE facturas SET tipo_doc='" . $tipo_doc . "', cod_doc='" . $new . "' WHERE numero_factura='" . $numero_factura . "'");
    if ($update_tipo) {
        //echo "1 -".$new."-".$tipo_doc."-".$numero_factura;		

    }
} else {
    $update_tipo = mysqli_query($con, "UPDATE facturas SET tipo_doc='" . $tipo_doc . "', cod_doc='1' WHERE numero_factura='" . $numero_factura . "'");
    if ($update_tipo) {
        //echo "2 -1-".$tipo_doc."-".$numero_factura;			

    }
}



?>