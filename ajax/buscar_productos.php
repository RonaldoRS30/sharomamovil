<?php

	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	//Archivo de funciones PHP
	include("../funciones.php");
	$action = (isset($_REQUEST['action'])&& $_REQUEST['action'] !=NULL)?$_REQUEST['action']:'';
	if (isset($_GET['id'])){
		$id_producto=intval($_GET['id']);
		$query=mysqli_query($con, "select * from cji_pedidodetalle where PROD_Codigo='".$id_producto."'");
		$count=mysqli_num_rows($query);
		if ($count==0){
			if ($delete1=mysqli_query($con,"DELETE FROM cji_producto WHERE PROD_Codigo ='".$id_producto."'")){
			?>
			<div class="alert alert-success alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <strong>Aviso!</strong> Datos eliminados exitosamente.
			</div>
			<?php 
		}else {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <strong>Error!</strong> Lo siento algo ha salido mal intenta nuevamente.
			</div>
			<?php
			
		}
			
		} else {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <strong>Error!</strong> No se pudo eliminar éste  producto. Existen cotizaciones vinculadas a éste producto. 
			</div>
			<?php
		}
		
		
		
	}
	if($action == 'ajax'){
		// escaping, additionally removing everything that could be (html/javascript-) code
         $q = mysqli_real_escape_string($con,(strip_tags($_REQUEST['q'], ENT_QUOTES)));
		 $aColumns = array('PROD_CodigoInterno', 'PROD_Nombre');//Columnas de busqueda
		$sTable = "cji_producto p 
           LEFT JOIN cji_marca m ON p.MARCP_Codigo = m.MARCP_Codigo 
           LEFT JOIN cji_almacenproducto ap ON p.PROD_Codigo = ap.PROD_Codigo 
           LEFT JOIN cji_almacen a ON ap.ALMAC_Codigo = a.ALMAP_Codigo";
		 $sWhere = "";
		if ( $_GET['q'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				$sWhere .= $aColumns[$i]." LIKE '%".$q."%' OR ";
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		$marca = '';
		if (isset($_GET['marca'])) {
			$marca = mysqli_real_escape_string($con, strip_tags($_GET['marca'], ENT_QUOTES));
		} else if (isset($_POST['marca'])) {
			$marca = mysqli_real_escape_string($con, strip_tags($_POST['marca'], ENT_QUOTES));
		}

		if (!empty($marca)) {
			if ($sWhere == "") {
				$sWhere = "WHERE p.MARCP_Codigo = '$marca'";
			} else {
				// Ya hay un WHERE, así que agrega AND
				$sWhere .= " AND p.MARCP_Codigo = '$marca'";
			}
		}


		$almacen = '';
		if (isset($_GET['almacen'])) {
			$almacen = mysqli_real_escape_string($con, strip_tags($_GET['almacen'], ENT_QUOTES));
		} else if (isset($_POST['almacen'])) {
			$almacen = mysqli_real_escape_string($con, strip_tags($_POST['almacen'], ENT_QUOTES));
		}

		if (!empty($almacen)) {
			if ($sWhere == "") {
				$sWhere = "WHERE ap.ALMAC_Codigo = '$almacen'";
			} else {
				// Ya hay un WHERE, así que agrega AND
				$sWhere .= " AND a.ALMAP_Codigo = '$almacen'";
			}
		}

		$sWhere.=" order by PROD_CodigoInterno desc";
		include 'pagination.php'; //include pagination file
		//pagination variables
		$page = (isset($_REQUEST['page']) && !empty($_REQUEST['page']))?$_REQUEST['page']:1;
		$per_page = 10; //how much records you want to show
		$adjacents  = 4; //gap between pages after number of adjacents
		$offset = ($page - 1) * $per_page;
		//Count the total number of row in your table*/
		$count_query   = mysqli_query($con, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
		$row= mysqli_fetch_array($count_query);
		$numrows = $row['numrows'];
		$total_pages = ceil($numrows/$per_page);
		$reload = './productos.php';
		//main query to fetch the data
		$sql="SELECT p.*, m.MARCC_Descripcion FROM  $sTable $sWhere  LIMIT $offset,$per_page";
		$query = mysqli_query($con, $sql);
		//loop through fetched data
		if ($numrows>0){
			?>
			<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="thead-light">
            <tr>
                <th>Código</th>
                <th>Producto</th>
				<th>Marca</th>
                <th class='text-right'>Stock Disponible</th>
				<!-- <th class='text-right'>Stock Comprometido</th> -->
				<?php
					$sqlHead = "SELECT * FROM cji_tipocliente WHERE TIPCLIC_FlagEstado = 1";
					$queryHead = mysqli_query($con, $sqlHead);
					while($rowHead=mysqli_fetch_array($queryHead)){
				?><th class='text-right'><?=$rowHead['TIPCLIC_Descripcion']?></th>
				<?php
					}
				?>
                <!-- <th class='text-right'>Acciones</th> -->
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_array($query)) {
                $id_producto = $row['PROD_Codigo'];
                $codigo_producto = $row['PROD_CodigoInterno'];
                $nombre_producto = $row['PROD_Nombre'];
				$marca = $row['MARCC_Descripcion'];
                $detalle = $row['PROD_DescripcionBreve'];
                $queryGetStock = mysqli_query($con, "SELECT ALMPROD_Stock, ALMPROD_StockComprometido FROM cji_almacenproducto WHERE ALMAC_Codigo = $almacen AND COMPP_Codigo = 1 AND PROD_Codigo = $id_producto");
				while($gs = mysqli_fetch_array($queryGetStock)){
					$stock = $gs['ALMPROD_Stock'];
					$stockComp = $gs['ALMPROD_StockComprometido'];
					$StockDisponible = $stock - $stockComp;
					// $StockComprometido = $stockComp;
				}
				
				
                // Concatenar nombre del producto con el detalle
                $producto_con_detalle = !empty($detalle) ? $nombre_producto . " - " . $detalle : $nombre_producto;

                $status_producto = $row['PROD_FlagEstado'];
                $estado = ($status_producto == 1) ? "Activo" : "Inactivo"; // Usar operador ternario
                $date_added = date('d/m/Y', strtotime($row['PROD_FechaRegistro']));
                $precio_producto = $row['PROD_UltimoCosto'];
                //$igv = $row['igv'];
                $medida = $row['PROD_Modelo'];
				

                // Mostrar badge si tiene IGV
                //$badge = ($row['igv'] == 1) ? '<span class="badge" style="background-color: #37ab29;">IGV</span>' : '';
            ?>
                <input type="hidden" value="<?php echo htmlspecialchars($codigo_producto); ?>" id="codigo_producto<?php echo $id_producto; ?>">
                <input type="hidden" value="<?php echo htmlspecialchars($nombre_producto); ?>" id="nombre_producto<?php echo $id_producto; ?>">
                <input type="hidden" value="<?php echo htmlspecialchars($detalle); ?>" id="detalle<?php echo $id_producto; ?>">
                <input type="hidden" value="<?php echo htmlspecialchars($status_producto); ?>" id="estado<?php echo $id_producto; ?>">
                <input type="hidden" value="<?php echo number_format($precio_producto, 2, '.', ''); ?>" id="precio_producto<?php echo $id_producto; ?>">
                <input type="hidden" value="<?php echo number_format($precio_venta, 2, '.', ''); ?>" id="precio_costo<?php echo $id_producto; ?>">
                <!--<input type="hidden" value="<?php echo $igv; ?>" id="igv<?php echo $id_producto; ?>">-->
                <input type="hidden" value="<?php echo $medida; ?>" id="PROD_Modelo<?php echo $id_producto; ?>">

                <tr>
                    <td><?php echo htmlspecialchars($codigo_producto); ?></td>
                    <td><?php echo htmlspecialchars($producto_con_detalle); ?></td>
					<td><?php echo htmlspecialchars($marca); ?></td>
                    <td class='text-right'><?php echo htmlspecialchars($StockDisponible); ?></td>
					<!-- <td class='text-right'><//?php echo htmlspecialchars($StockComprometido); ?></td> -->
					<?php
						$sqlPrecios = "SELECT * FROM cji_productoprecio pp WHERE PROD_Codigo = $id_producto AND MONED_Codigo = 1";
						$queryPrecios = mysqli_query($con, $sqlPrecios);
						while($rowPrecios = mysqli_fetch_array($queryPrecios)){
							?><td class="text-right"><?php echo 'S/ ' . number_format($rowPrecios['PRODPREC_Precio'], 2, '.', ','); ?></td>
						<?php };
					?>
					

                    <!--<td><?php echo $badge; ?></td>-->
                    <!-- <td class="text-right">
                        <span class="pull-right">
                            <a href="#" class="btn btn-warning" title="Editar producto" onclick="obtener_datos('<?php echo $id_producto; ?>');" data-toggle="modal" data-target="#myModal2">
                                <i class="glyphicon glyphicon-edit"></i>
                            </a>
                            <a href="#" class="btn btn-danger" title="Borrar producto" onclick="eliminar('<?php echo $id_producto; ?>')">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </span>
                    </td> -->
                </tr>
            <?php
            }
            ?>
            <tr>
                <td colspan="10">
                    <span class="pull-right">
                        <?php echo paginate($reload, $page, $total_pages, $adjacents); ?>
                    </span>
                </td>
            </tr>
            <tr>
            </tr>
        </tbody>
    </table>
</div>

			<?php
		}
	}
?>