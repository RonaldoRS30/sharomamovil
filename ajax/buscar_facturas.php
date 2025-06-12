<?php
 include('is_logged.php'); // Archivo que verifica que el usuario que intenta acceder a la URL está logueado

/* Conexión a la base de datos */
 require_once("../config/db.php"); // Configuración de la base de datos
 require_once("../config/conexion.php"); // Función para conectar a la base de datos

$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';

$hoy = date('Y-m-d H:i:s');
$cadena = strtotime($hoy).substr((string)microtime(), 1, 8);
$session_id = str_replace('.','',$cadena);
if (isset($_GET['id'])) {
    $numero_factura = intval($_GET['id']);
    // Actualización del estado del documento
    $del1 = "UPDATE cji_pedido SET estado_factura='0' WHERE numero_factura='" . $numero_factura . "'";
    // Aquí se realizaría la consulta para cancelar el documento
}

if (isset($_GET['id2'])) {
    $numero_factura = intval($_GET['id2']);
    // Actualización del estado del documento
    $del1 = "UPDATE cji_pedido SET estado_factura='2' WHERE numero_factura='" . $numero_factura . "'";
    // Aquí se realizaría la consulta para aprobar el documento
}

if ($action == 'ajax') {
    // Filtro de búsqueda y generación de consulta
    $q = ""; // Parámetro de búsqueda (escapado para prevenir inyección SQL)
    $sTable = "
            (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) as documento,
            (CASE c.CLIC_TipoPersona  WHEN '1'
                THEN e.EMPRC_RazonSocial
                ELSE CONCAT(pe.PERSC_Nombre , ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre, 
            (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Telefono ELSE pe.PERSC_Telefono end) as telefono,
            (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Email ELSE pe.PERSC_Email end) as email
            FROM cji_pedido p
            INNER JOIN cji_cliente c ON c.CLIP_Codigo=p.CLIP_Codigo
            LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND c.CLIC_TipoPersona ='0'
            LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND c.CLIC_TipoPersona='1'";
    $sWhere = "";

    // Condiciones adicionales para filtrar por fechas, tipo y encargado
    $hoy = date("Y-m-d");
    $fecha_inicio = $_GET["fecha_inicio"];
    $fecha_fin = $_GET["fecha_fin"];
    $documento = $_GET["documentoCliente"];
    $nombre = $_GET["nombreCliente"];

    if($documento != ""){
        $sWhere .= " AND CONCAT_WS(' ',pe.PERSC_NumeroDocIdentidad, e.EMPRC_Ruc) LIKE '%$documento%'";
    }
    if($nombre != ""){
        $sWhere .= " AND CONCAT_WS(' ',c.CLIC_CodigoUsuario, pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, e.EMPRC_RazonSocial) LIKE '%$nombre%'";
    }
    if ($fecha_inicio != "" && $fecha_fin == "") {
        $sWhere .= " AND PEDIC_FechaRegistro BETWEEN '$fecha_inicio 00:00:00' AND '$hoy 23:59:59'";
    }else if($fecha_fin != "" && $fecha_inicio == ""){
        $sWhere .= " AND PEDIC_FechaRegistro BETWEEN '2000-01-01 00:00:00' AND '$fecha_fin 23:59:59'";
    }else if($fecha_inicio != ""  && $fecha_fin != ""){
        $sWhere .= " AND PEDIC_FechaRegistro BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59'";
    }

    

    $sWhere .= "ORDER BY p.PEDIC_Numero DESC";
    // if ($_GET["tipo_search"] != "A") {
    //     $sWhere .= " AND cji_pedido.estado_factura='" . $_GET["tipo_search"] . "'";
    // }
    // if ($_GET["encargado_search"] != "A") {
    //     $sWhere .= " AND PERSP_Codigo='" . $_GET["encargado_search"] . "'";
    // }

    // Paginación y consulta principal
    $page = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    $count_sql="SELECT count(*) AS numrows, $sTable WHERE PEDIC_FlagEstado >= 0 $sWhere";
    $count_query = mysqli_query($con, $count_sql);
    // Aquí se contaría el total de registros

    if ($count_query) {
        $row = mysqli_fetch_array($count_query);
        $numrows = $row['numrows'];
    } else {
        die("Error en la consulta: " . mysqli_error($con)); // Muestra el error si hay un problema
    }

    

    $sql = "SELECT *, $sTable WHERE PEDIC_FlagEstado >= 0 $sWhere";
    
    $query = mysqli_query($con, $sql);

    // Renderizado de la tabla de resultados
    ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Numero</th>
                    <th>Ruc/Dni</th>
                    <th>Razon Social</th>
                    <th>Total</th>
                    <!--<th></th>-->
                    <!--<th></th>-->
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>  
            </thead>
            <tbody>
            <?php
                    while ($row = mysqli_fetch_array($query)) {
                        $id_factura = $row['PEDIP_Codigo'];
                        $tipo_doc = $row['PEDIC_Serie'];
                        $num_cliente = $row['documento'];
                        $numero_factura = $row['PEDIC_Numero'];
                        $fecha = date("d/m/Y", strtotime($row['PEDIC_FechaRegistro']));
                        $nombre_cliente = $row['nombre'];
                        $estado_factura = $row['PEDIC_FlagEstado'];
                        $total_venta = $row['PEDIC_PrecioTotal'];
                        $telefono_cliente = $row['telefono'];
                        $email_cliente = $row['email'];
                        $pais_cliente = "Peru";
                        $label_class = '';
                        $text_estado = '';
                        switch ($estado_factura) {
                            case 0:
                                $text_estado = "EN ESPERA";
                                $label_class = 'label-warning';
                                break;
                            case 1:
                                $text_estado = "ENTREGADO";
                                $label_class = 'label-success'; 
                                break;
                            case 2:
                                $text_estado = "EN PROCESO";
                                $label_class = 'label-info';
                                break;
                            case 3:
                                $text_estado = "ANULADO";
                                $label_class = 'label-danger';
                        }
                        ?>
                        <tr>
                            <td><?php echo $fecha; ?></td>
                            <td><?php echo $tipo_doc."-".$numero_factura; ?></td>
                            <td><?php echo $num_cliente; ?></td>
                            <td>
                                <a data-toggle="tooltip" data-html="true"
                                    title="<strong>Tel:</strong> <?php echo $telefono_cliente; ?><br><strong>Email:</strong> <?php echo $email_cliente; ?>"
                                    onclick ="copyNum('<?=$telefono_cliente;?>')">
                                    <?php echo $nombre_cliente; ?>
                                </a>
                                <div id="Alert" style="display:none;">
                                    hola
                                </div>
                            </td>
                            <td class='text-right'><?php echo number_format($total_venta, 2); ?></td>
                            
                            

                            <td style="text-align: center;">
                                <span class="label <?php echo $label_class; ?>"><?php echo $text_estado; ?></span>
                            </td>

                            <!--<td><?php echo $pais_cliente; ?></td>-->

                            <td class="text-right">
                                <!-- Acciones -->
                                <?php if($estado_factura == 0) {?>
                                    <a href="./editar_factura.php?id=<?=$id_factura?>" class='btn btn-sm btn-warning'
                                        title='Editar Documento'>
                                        <i class="glyphicon glyphicon-edit"></i>
                                    </a>
                                <?php } ?>
                                <a class='btn btn-sm btn-info' title='Descargar Documento'
                                    onclick="imprimir_factura('<?php echo $id_factura; ?>');">
                                    <i class="glyphicon glyphicon-download"></i>
                                </a>
                                <!--<a href="#" class='btn btn-sm btn-danger' title='Cancelar Documento'
                                    onclick="eliminar('<?php echo $numero_factura; ?>')">
                                    <i class="glyphicon glyphicon-remove"></i>
                                </a>            onclick="editar_factura('<?=$id_factura;?>','<?=$session_id?>')"-->
                                <!--<a href="#" class='btn btn-sm btn-success' title='Aprobar Documento'
                                    onclick="aprobar('<?php echo $numero_factura; ?>')">
                                    <i class="glyphicon glyphicon-ok"></i>
                                </a>-->
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
            </tbody>
        </table>
    </div>
    <script>
        function copyNum(num){
            if(num != null){
                var inp =document.createElement('input');
                document.body.appendChild(inp)
                inp.value = num
                inp.select();
                document.execCommand('copy',false);
                inp.remove();
            }
        }
    </script>
    <?php
}