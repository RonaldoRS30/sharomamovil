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
    // Escaping para prevenir inyección SQL
$q = isset($_REQUEST['q']) ? mysqli_real_escape_string($con, strip_tags($_REQUEST['q'], ENT_QUOTES)) : '';
    
    // Columnas de búsqueda
    $aColumns = array(
        'pe.PERSC_NumeroDocIdentidad', 
        'e.EMPRC_Ruc',
        'pe.PERSC_Nombre',
        'pe.PERSC_ApellidoPaterno',
        'e.EMPRC_RazonSocial',
        'p.PEDIC_Numero'
    );
    
    // Estructura base de la consulta
    $sTable = "cji_pedido p
              INNER JOIN cji_cliente c ON c.CLIP_Codigo=p.CLIP_Codigo
              LEFT JOIN cji_persona pe ON pe.PERSP_Codigo=c.PERSP_Codigo AND c.CLIC_TipoPersona ='0'
              LEFT JOIN cji_empresa e ON e.EMPRP_Codigo=c.EMPRP_Codigo AND c.CLIC_TipoPersona='1'";
    
    // Campos adicionales para mostrar
    $sSelect = "p.*, 
               (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Ruc ELSE pe.PERSC_NumeroDocIdentidad end) as documento,
               (CASE c.CLIC_TipoPersona WHEN '1'
                   THEN e.EMPRC_RazonSocial
                   ELSE CONCAT(pe.PERSC_Nombre, ' ', pe.PERSC_ApellidoPaterno, ' ', pe.PERSC_ApellidoMaterno) end) nombre, 
               (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Telefono ELSE pe.PERSC_Telefono end) as telefono,
               (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Email ELSE pe.PERSC_Email end) as email";
    
    $sWhere = "WHERE p.PEDIC_FlagEstado >= 0";
    
    // Construcción dinámica del WHERE para búsqueda general
   if (!empty($q)) {
    $sWhere .= " AND (";
    for ($i = 0; $i < count($aColumns); $i++) {
        $sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR ";
    }
    $sWhere = substr_replace($sWhere, "", -3);
    $sWhere .= ")";
}


    // Filtros adicionales
    $hoy = date("Y-m-d");
    $fecha_inicio = mysqli_real_escape_string($con, $_GET["fecha_inicio"]);
    $fecha_fin = mysqli_real_escape_string($con, $_GET["fecha_fin"]);
    $documento = mysqli_real_escape_string($con, $_GET["documentoCliente"]);
    $nombre = mysqli_real_escape_string($con, $_GET["nombreCliente"]);

    if($documento != ""){
        $sWhere .= " AND CONCAT_WS(' ',pe.PERSC_NumeroDocIdentidad, e.EMPRC_Ruc) LIKE '%$documento%'";
    }
    if($nombre != ""){
        $sWhere .= " AND CONCAT_WS(' ',c.CLIC_CodigoUsuario, pe.PERSC_Nombre, pe.PERSC_ApellidoPaterno, e.EMPRC_RazonSocial) LIKE '%$nombre%'";
    }
    if ($fecha_inicio != "" && $fecha_fin == "") {
        $sWhere .= " AND p.PEDIC_FechaRegistro BETWEEN '$fecha_inicio 00:00:00' AND '$hoy 23:59:59'";
    } else if($fecha_fin != "" && $fecha_inicio == "") {
        $sWhere .= " AND p.PEDIC_FechaRegistro BETWEEN '2000-01-01 00:00:00' AND '$fecha_fin 23:59:59'";
    } else if($fecha_inicio != "" && $fecha_fin != "") {
        $sWhere .= " AND p.PEDIC_FechaRegistro BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59'";
    }

    $sWhere .= " ORDER BY p.PEDIC_Numero DESC";

    // Incluir archivo de paginación
    include 'pagination.php';
    
    // Variables de paginación
    $page = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page = 10; // Registros por página
    $adjacents = 4; // Brecha entre páginas
    $offset = ($page - 1) * $per_page;
    
    // Contar total de registros
    $count_query = mysqli_query($con, "SELECT COUNT(*) AS numrows FROM $sTable $sWhere");
    $row = mysqli_fetch_array($count_query);
    $numrows = $row['numrows'];
    $total_pages = ceil($numrows/$per_page);
    $reload = './pedidos.php';
    
    // Consulta principal con paginación
    $sql = "SELECT $sSelect FROM $sTable $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($con, $sql);

    if ($numrows > 0) {
        ?>
        <div class="table-responsive" id="datapedidos">
            <table class="table table-striped table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Fecha</th>
                        <th>Numero</th>
                        <th>Ruc/Dni</th>
                        <th>Razon Social</th>
                        <th>Total</th>
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
                                    onclick="copyNum('<?=$telefono_cliente;?>')">
                                    <?php echo $nombre_cliente; ?>
                                </a>
                                <div id="Alert" style="display:none;">
                                
                                </div>
                            </td>
                            <td class='text-right'><?php echo number_format($total_venta, 2); ?></td>
                            
                            <td style="text-align: center;">
                                <span class="label <?php echo $label_class; ?>"><?php echo $text_estado; ?></span>
                            </td>

                            <td class="text-right">
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
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        <div class="row">
            <div class="col-md-12">
                <span class="pull-right">
                    <?php echo paginate($reload, $page, $total_pages, $adjacents); ?>
                </span>
            </div>
        </div>
        
        <script>
            function copyNum(num){
                if(num != null){
                    var inp = document.createElement('input');
                    document.body.appendChild(inp);
                    inp.value = num;
                    inp.select();
                    document.execCommand('copy',false);
                    inp.remove();
                }
            }
        </script>
        <?php
    } else {
        ?>
        <div class="alert alert-warning">
            No se encontraron resultados para la búsqueda.
        </div>
        <?php
    }
}
?>