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
	;
	
	$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';

	if (isset($_GET['session'])) {
		$session = $_GET['session'];
	}else if (isset($_POST['session'])){
		$session = $_POST['session'];
	}

	

	if ($action == 'ajax') {
		// Escaping, additionally removing everything that could be (html/javascript-) code
		$q = mysqli_real_escape_string($con, (strip_tags($_REQUEST['q'], ENT_QUOTES)));
		$aColumns = array('p.PROD_Codigo', 'p.PROD_Nombre'); // Columnas de búsqueda
		$sTable = "cji_producto p";
		$sWhere = "WHERE PROD_FlagEstado != 0"; // Excluir productos con status 0
	
		if ($_GET['q'] != "") {
			$sWhere .= " AND ("; // Agregar condición adicional
			for ($i = 0; $i < count($aColumns); $i++) {
				$sWhere .= $aColumns[$i] . " LIKE '%" . $q . "%' OR ";
			}
			$sWhere = substr_replace($sWhere, "", -3); // Eliminar el último 'OR'
			$sWhere .= ')';
		}
		include 'pagination.php'; //include pagination file
		//pagination variables
		$page = (isset($_REQUEST['page']) && !empty($_REQUEST['page']))?$_REQUEST['page']:1;
		$per_page = 5; //how much records you want to show
		$adjacents  = 4; //gap between pages after number of adjacents
		$offset = ($page - 1) * $per_page;
		//Count the total number of row in your table*/
		$count_query   = mysqli_query($con, "SELECT count(*) AS numrows FROM $sTable  $sWhere");
		$row= mysqli_fetch_array($count_query);
		$numrows = $row['numrows'];
		$total_pages = ceil($numrows/$per_page);
		$reload = './index.php';
		//main query to fetch the data
		$sql="SELECT * FROM  $sTable $sWhere"; 				// SI QUEREMOS PAGINACIÓN AL FINAL VA LIMIT $offset,$per_page
		$query = mysqli_query($con, $sql);

		
		//loop through fetched data
		if ($numrows>0){
			
			?>
<div class="table-responsive">
    <table class="table">
        <tr class="warning">
            <th>Producto</th>
			<th></th>
			<th>Stock Disponible</th>
            <th><span class="pull-right">Cant.</span></th>
			<th><span class="pull-right">Precio</span></th>
            <th class='text-center' style="width: 36px;">Agregar</th>
        </tr>
        <?php
				$_SESSION['Zona'] = $_GET['precioVenta'];
				$limit = $_SESSION['Zona'];
				while ($row=mysqli_fetch_array($query)){
					$id_producto=$row['PROD_Codigo'];
					$codigo_producto=$row['PROD_Codigo'];
					$nombre_producto=$row['PROD_Nombre'];
					$detalle = $row['PROD_DescripcionBreve'];
					$queryGetStock = mysqli_query($con, "SELECT ALMPROD_Stock, ALMPROD_StockComprometido FROM cji_almacenproducto WHERE ALMAC_Codigo = 1 AND COMPP_Codigo = 1 AND PROD_Codigo = $id_producto");
					while($gs = mysqli_fetch_array($queryGetStock)){
						$stock = $gs['ALMPROD_Stock'];
						$stockComp = $gs['ALMPROD_StockComprometido'];
						$StockDisponible = $stock - $stockComp;
					}
					$sqlPrecios = "SELECT * FROM cji_productoprecio pp WHERE PROD_Codigo = $id_producto AND MONED_Codigo = 1 LIMIT $limit";
					$queryPrecios = mysqli_query($con, $sqlPrecios);
					while($rowPrecios = mysqli_fetch_array($queryPrecios)){
						$precioSelect = $rowPrecios['PRODPREC_Precio'];
					};
					$precio_venta=$precioSelect;
					$precio_venta=number_format($precio_venta,2,'.','');
					$producto_y_detalle = $nombre_producto . " - " . $detalle;
					?>
        <tr>
            <td><?php echo $producto_y_detalle; ?></td>

			<input type="hidden" id="codigo_<?=$id_producto?>" value="<?=$id_producto?>">
			<input type="hidden" id="nombre_<?=$id_producto?>" value="<?=$nombre_producto?>">
			
			<td class='col-xs-2'>
            <input type="hidden" class="form-control" style="text-align:left"
                    id="detalle_<?php echo $id_producto; ?>" value=""><!-- ANTES ERA TYPE TEXT-->
            </td>
			<td class='col-xs-2'><?php echo $StockDisponible; ?></td> 
			<input type="hidden" id="stock_<?=$id_producto?>" value="<?=$StockDisponible?>">

			<td class='col-xs-1'>
                <div class="pull-right">
                    <input type="text" class="form-control" style="text-align:right"
                        id="cantidad_<?php echo $id_producto; ?>" value="">
                </div>
            </td>
            <td class='col-xs-2'>
                <div class="pull-right">
                    <input type="text" class="form-control" style="text-align:right"
                        id="precio_venta_<?php echo $id_producto; ?>" value="<?php echo $precio_venta;?>">
                </div>
            </td>
			<input type="hidden" id="select_<?php echo $id_producto; ?>" value="false">
            <td class='text-center'><a class='btn btn-info' href="#" onclick="agregar('<?=$id_producto?>','<?=$session?>')"><i class="glyphicon glyphicon-plus"></i></a></td>
        </tr>
        <?php
				}
				?>
        <!-- <tr>
            <td colspan=5><span class="pull-right">
                    <?php
					 echo paginate($reload, $page, $total_pages, $adjacents);
					?></span></td>
        </tr> -->
    </table>
</div>
<?php
		}
	}
?>