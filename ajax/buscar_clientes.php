<?php
/*-------------------------
   Autor: Obed Alvarado
   Web: obedalvarado.pw
   Mail: info@obedalvarado.pw
   ---------------------------*/
include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
/* Connect To Database*/
require_once("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
require_once("../config/conexion.php");//Contiene funcion que conecta a la base de datos

$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
if (isset($_GET['id'])) {
    $id_cliente = intval($_GET['id']);
    $query = mysqli_query($con, "select * from cji_pedido where CLIP_Codigo='" . $id_cliente . "'");
    $count = mysqli_num_rows($query);
    if ($count == 0) {
        if ($delete1 = mysqli_query($con, "DELETE FROM cji_cliente WHERE CLIP_Codigo='" . $id_cliente . "'")) {
            ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong>Aviso!</strong> Datos eliminados exitosamente.
            </div>
            <?php
        } else {
            ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <strong>Error!</strong> Lo siento algo ha salido mal intenta nuevamente.
            </div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <strong>Error!</strong> No se pudo eliminar éste cliente. Existen facturas vinculadas a éste producto.
        </div>
        <?php
    }
}

if ($action == 'ajax') {
    // escaping, additionally removing everything that could be (html/javascript-) code
    $q = mysqli_real_escape_string($con,(strip_tags($_REQUEST['q'], ENT_QUOTES)));
    $aColumns = array('p.PERSC_Nombre', 'p.PERSC_NumeroDocIdentidad', 'e.EMPRC_RazonSocial', 'e.EMPRC_Ruc'); //Columnas de busqueda
    $sTable = "cji_cliente AS c
              LEFT JOIN cji_persona AS p ON c.PERSP_Codigo = p.PERSP_Codigo
              LEFT JOIN cji_empresa AS e ON c.EMPRP_Codigo = e.EMPRP_Codigo";
    $sWhere = "";
    
    if ($_GET['q'] != "") {
        $sWhere = "WHERE (";
        for ($i=0 ; $i<count($aColumns) ; $i++) {
            $sWhere .= $aColumns[$i]." LIKE '%".$q."%' OR ";
        }
        $sWhere = substr_replace($sWhere, "", -3);
        $sWhere .= ')';
    }
    
    $sWhere.=" order by c.CLIP_Codigo desc";
    
    include 'pagination.php'; //include pagination file
    //pagination variables
    $page = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page = 10; //how much records you want to show
    $adjacents = 4; //gap between pages after number of adjacents
    $offset = ($page - 1) * $per_page;
    
    //Count the total number of row in your table*/
    $count_query = mysqli_query($con, "SELECT count(*) AS numrows FROM $sTable $sWhere");
    $row = mysqli_fetch_array($count_query);
    $numrows = $row['numrows'];
    $total_pages = ceil($numrows/$per_page);
    $reload = './clientes.php';
    
    //main query to fetch the data
    $sql = "SELECT c.*, p.*, e.*,
            (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Ruc ELSE p.PERSC_NumeroDocIdentidad end) as documento,
            (CASE c.CLIC_TipoPersona WHEN '1'
                THEN e.EMPRC_RazonSocial
                ELSE CONCAT(p.PERSC_Nombre, ' ', p.PERSC_ApellidoPaterno, ' ', p.PERSC_ApellidoMaterno) end) nombre,
            (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Direccion ELSE p.PERSC_Direccion end) as direccion,
            (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Telefono ELSE p.PERSC_Telefono end) as telefono
            FROM $sTable $sWhere LIMIT $offset,$per_page";
    
    $query = mysqli_query($con, $sql);
    
    if ($numrows > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Numero</th>
                        <th>Nombre o Razón social</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                        <th>Agregado</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = mysqli_fetch_array($query)) {
                        $tipoCliente = $row['CLIC_TipoPersona'];
                        $tipo_doc = $row['PERSC_TipoDocIdentidad'];
                        $id_cliente = $row['CLIP_Codigo'];
                        
                        if($tipoCliente != 1) {
                            $nombresPersona = $row['PERSC_Nombre'];
                            $apepaPersona = $row['PERSC_ApellidoPaterno'];
                            $apemaPersona = $row['PERSC_ApellidoMaterno'];
                            $idSec = $row['PERSP_Codigo'];
                        } else {
                            $idSec = $row['EMPRP_Codigo'];
                        }
                        
                        $nombre_cliente = $row['nombre'];
                        $num_cliente = $row['documento'];
                        $telefono_cliente = $row['telefono'];
                        $direccion_cliente = $row['direccion'];
                        $status_cliente = $row['CLIC_FlagEstado'];
                        
                        $estado = ($status_cliente == 1) ? "Activo" : "Inactivo";
                        $date_added = date('d/m/Y', strtotime($row['CLIC_FechaRegistro']));
                        ?>
                        <input type="hidden" value="<?php echo htmlspecialchars($num_cliente); ?>"
                            id="num_cliente<?php echo $id_cliente; ?>">
                        <input type="hidden" value="<?php echo htmlspecialchars($telefono_cliente); ?>"
                            id="telefono_cliente<?php echo $id_cliente; ?>">
                        <input type="hidden" value="<?php echo htmlspecialchars($nombre_cliente); ?>"
                            id="nombre_cliente<?php echo $id_cliente; ?>">
                        <input type="hidden" value="<?php echo htmlspecialchars($direccion_cliente); ?>"
                            id="direccion_cliente<?php echo $id_cliente; ?>">
                        <input type="hidden" value="<?php echo htmlspecialchars($status_cliente); ?>"
                            id="status_cliente<?php echo $id_cliente; ?>">
                        <input type="hidden" value="<?php echo htmlspecialchars($idSec); ?>"
                            id="idSec_<?php echo $id_cliente; ?>">
                        <?php if($tipoCliente != 1) { ?>
                            <input type="hidden" value="<?php echo htmlspecialchars($nombresPersona); ?>"
                            id="nombre_persona<?php echo $id_cliente; ?>">
                            <input type="hidden" value="<?php echo htmlspecialchars($apepaPersona); ?>"
                            id="apepa_persona<?php echo $id_cliente; ?>">
                            <input type="hidden" value="<?php echo htmlspecialchars($apemaPersona); ?>"
                            id="apema_persona<?php echo $id_cliente; ?>">
                        <?php } ?>
                        <tr>
                            <td><?php echo htmlspecialchars($num_cliente); ?></td>
                            <td><?php echo htmlspecialchars($nombre_cliente); ?></td>
                            <td><?php echo htmlspecialchars($direccion_cliente); ?></td>
                            <td><?php echo htmlspecialchars($estado); ?></td>
                            <td><?php echo htmlspecialchars($date_added); ?></td>
                            <td class="text-right">
                                <span class="pull-right">
                                    <div class="btn-group">
                                        <a href="#" class="btn btn-warning" title="Editar cliente"
                                            onclick="obtener_datos('<?=$id_cliente?>','<?=$tipoCliente?>');" data-toggle="modal"
                                            data-target="#myModal2">
                                            <i class="glyphicon glyphicon-edit"></i>
                                        </a>
                                    </div>
                                </span>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="6">
                            <span class="pull-right">
                                <?php echo paginate($reload, $page, $total_pages, $adjacents); ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}
?>