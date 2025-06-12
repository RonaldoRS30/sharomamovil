<?php
if ($con) {
    ?>
    <table cellspacing="0" style="width: 90%; font-family: Arial, sans-serif; color: #444444;">
        <tr>
            <!-- Logo de la empresa -->
            <td style="width: 75%;"><!-- Ajusta el ancho de la celda -->
                <img src="../../<?php echo get_row('perfil', 'logo_url', 'id_perfil', 1); ?>" alt="Logo"
                    style="width: 280px; max-width: 10px; margin-bottom: ;"> <!-- Aumenta el max-width -->
                <br>
                <strong
                    style="font-size: 17px; color: black; text-align: left; display: block;"><?php echo get_row('perfil', 'nombre_empresa', 'id_perfil', 1); ?>
                </strong>
                <br>
                <div style="max-width: 50px;">
                    <strong>Dirección:</strong> <?php
                    echo get_row('perfil', 'direccion', 'id_perfil', 1) . ", " .
                        get_row('perfil', 'ciudad', 'id_perfil', 1) . ", " .
                        get_row('perfil', 'estado', 'id_perfil', 1);
                    ?><br>
                    <strong>Teléfono:</strong> <?php echo get_row('perfil', 'telefono', 'id_perfil', 1); ?><br>
                    <strong>Email:</strong> <?php echo get_row('perfil', 'email', 'id_perfil', 1); ?>
                </div>
            </td>
            <!-- Información del documento, alineado a la derecha -->
            <td style="width: 25%; text-align: left; vertical-align: top; font-size: 14px;">
                <?php
                $documentTypes = [
                    //1 => 'FACTURA DE VENTA ELECTRONICA',
                    //2 => 'BOLETA DE VENTA ELECTRONICA',
                    3 => 'COMPROBANTE DE VENTA ELECTRONICA'
                ];

                // Determina el nombre del tipo de documento
                $nombre_d = isset($documentTypes[$tipo_doc]) ? $documentTypes[$tipo_doc] : 'DOCUMENTO';
                ?>

                <!-- Cuadrado para el texto del documento -->
                <div
                    style="background-color:#eeeeee;border: 1px solid #444; border-radius: 5px; padding: 5px; display: inline-block; width: 215px; text-align: center;">
                    <strong style="font-size: 16px; margin: 2px 0;">R.U.C.
                        <?php echo get_row('perfil', 'ruc_empresa', 'id_perfil', 1); ?></strong><br>
                    <strong style="font-size: 16px; margin: 2px 0;"><?php echo $nombre_d; ?></strong><br>
                    <strong style="font-size: 16px; margin: 2px 0;">CPP- <?php echo $numero_f; ?></strong><br>
                    <strong style="font-size: 14px; margin: 2px 0;">ESTE NO ES UN COMPROBANTE DE PAGO VÁLIDO</strong><br>

                    <!-- Muestra si el documento está cancelado dentro del cuadrado -->
                    <?php if ($estado_doc == 0) { ?>
                        <p style="color: #d9534f; font-weight: bold; margin: 2px 0;">DOCUMENTO SIN APROBAR</p>
                    <?php } ?>
                </div>
            </td>
        </tr>
        
    </table>
    <?php
}
?>