<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
	include('is_logged.php'); // Archivo verifica que el usuario que intenta acceder a la URL esté logueado
	/* Conexión a la base de datos */
	require_once ("../config/db.php"); // Contiene las variables de configuración para conectar a la base de datos
	require_once ("../config/conexion.php"); // Contiene función que conecta a la base de datos
	
	$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';

	if (isset($_GET['session'])) {
		$session = $_GET['session'];
	} else if (isset($_POST['session'])){
		$session = $_POST['session'];
	}

	if ($action == 'ajax') {
		// Escaping, adicionalmente eliminando todo lo que podría ser código (html/javascript)
		$aColumns = array('p.PROD_Codigo', 'p.PROD_Nombre'); // Columnas de búsqueda
		$sTable = "cji_producto p";
		$sWhere = "WHERE PROD_FlagEstado != 0"; // Excluir productos con estado 0
	
		// Obtener el id del almacén (por GET)
	    $id_almacen = isset($_GET['almacen']) ? $_GET['almacen'] : 1;  
		include 'pagination.php'; // Incluir archivo de paginación

		// Variables de paginación
		$page = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
		$per_page = 5; // Cuántos registros quieres mostrar por página
		$adjacents = 4; // Espacio entre páginas después de un número de páginas adyacentes
		$offset = ($page - 1) * $per_page;

		// Contar el número total de filas en la tabla
		$count_query = mysqli_query($con, "SELECT count(*) AS numrows FROM $sTable $sWhere");
		$row = mysqli_fetch_array($count_query);
		$numrows = $row['numrows'];
		$total_pages = ceil($numrows / $per_page);
		$reload = './index.php';

		// Consulta de productos con paginación
		$sql="SELECT * FROM  $sTable $sWhere LIMIT $offset, $per_page"; 
		$query = mysqli_query($con, $sql);

		// Iterar a través de los productos obtenidos
		if ($numrows > 0) {
		?>
		<div class="table-responsive" id="dataproducto" > <!-- Agregar id="resultados" -->
		    <table class="table">
		        <tr class="warning">
		            <th>Producto</th>
					<th></th>
		        </tr>
		        <?php
		          $limit = isset($_GET['precioVenta']) ? $_GET['precioVenta'] : 1;

		        	// Iterar a través de los productos obtenidos
		        	while ($row = mysqli_fetch_array($query)) {
						$id_producto = $row['PROD_Codigo'];
						$nombre_producto = $row['PROD_Nombre'];
						$detalle = $row['PROD_DescripcionBreve'];
						
						// Obtener el stock según el almacén
						$queryGetStock = mysqli_query($con, "SELECT ALMPROD_Stock, ALMPROD_StockComprometido 
															FROM cji_almacenproducto 
															WHERE ALMAC_Codigo = $id_almacen AND PROD_Codigo = $id_producto");

						// Inicializar la variable antes de comprobar
						$StockDisponible = 0; // Valor por defecto
						
						if (mysqli_num_rows($queryGetStock) > 0) {
							while ($gs = mysqli_fetch_array($queryGetStock)) {
								$stock = $gs['ALMPROD_Stock'];
								$stockComp = $gs['ALMPROD_StockComprometido'];
								$StockDisponible = $stock - $stockComp; // Cálculo del stock disponible
							}
						} else {
							$StockDisponible = 0; // Asignación cuando no hay resultados
						}

						// Obtener el precio
						$precioSelect = 0; // Valor predeterminado
						$sqlPrecios = "SELECT * FROM cji_productoprecio pp WHERE PROD_Codigo = $id_producto AND MONED_Codigo = 1 LIMIT $limit";
						$queryPrecios = mysqli_query($con, $sqlPrecios);
						if ($queryPrecios && mysqli_num_rows($queryPrecios) > 0) {
							$rowPrecios = mysqli_fetch_array($queryPrecios);
							$precioSelect = $rowPrecios['PRODPREC_Precio'];
						}

						$precio_venta = number_format($precioSelect, 2, '.', '');
						$producto_y_detalle = $nombre_producto . " - " . $detalle;
		        ?>

		        <!-- Fila principal con el nombre del producto -->
		        <tr>
		            <td colspan="2">
		                <div>
		                    <strong><?= $producto_y_detalle ?></strong><br>
		                    <span>Stock Disponible: <?= $StockDisponible ?></span><br>
		                    <span>Precio: <?= $precio_venta ?></span><br>
		                    <button id="btnToggle_<?= $id_producto ?>" class="btn btn-success btn-sm" onclick="toggleDetalle('<?= $id_producto ?>')">
		                        <i class="glyphicon glyphicon-plus"></i>
		                    </button>
		                </div>
		            </td>
		        </tr>

		        <!-- Fila oculta con los detalles -->
		        <tr id="detalle_<?php echo $id_producto; ?>" style="display: none;">
		            <td colspan="6">
		                <input type="hidden" id="codigo_<?=$id_producto?>" value="<?=$id_producto?>">
		                <input type="hidden" id="nombre_<?=$id_producto?>" value="<?=$nombre_producto?>">
		                <input type="hidden" id="stock_<?=$id_producto?>" value="<?=$StockDisponible?>">
		                <input type="hidden" id="select_<?php echo $id_producto; ?>" value="false">
		                
		                <div class="row">
		                    <div class="col-md-3"><strong>Stock Disponible:</strong> <?php echo $StockDisponible; ?></div>
		                    <div class="col-md-3">
		                        <strong>Cantidad:</strong>
		                        <input type="text" class="form-control" id="cantidad_<?php echo $id_producto; ?>" style="text-align:right">
		                    </div>
		                    <div class="col-md-3">
		                        <strong>Precio:</strong>
		                        <input type="text" class="form-control" id="precio_venta_<?php echo $id_producto; ?>" value="<?php echo $precio_venta;?>" style="text-align:right">
		                    </div>
		                    <div class="col-md-3">
		                        <strong>&nbsp;</strong><br>
		                        <a class="btn btn-info" href="#" onclick="agregar('<?php echo $id_producto; ?>','<?php echo $session; ?>')"><i class="glyphicon glyphicon-plus"></i> Agregar</a>
		                    </div>
		                </div>
		            </td>
		        </tr>
		        <?php } ?>
					 
		        <tr>
		            <td colspan="6">
		                <span class="pull-right">
		                    <?php
		                    	// Generar la paginación
		                    	echo paginate($reload, $page, $total_pages, $adjacents);
		                    ?>
		                </span>
		            </td>
		        </tr>
		    </table>
		</div>
		<?php
		}
	}
?>

<script>
function toggleDetalle(id) {
    const fila = document.getElementById('detalle_' + id);
    const boton = document.getElementById('btnToggle_' + id);
    const icono = boton.querySelector('i');

    const estaVisible = fila.style.display !== 'none';

    if (estaVisible) {
        // Ocultar detalles
        fila.style.display = 'none';
        boton.classList.remove('btn-danger');
        boton.classList.add('btn-success');
        icono.classList.remove('glyphicon-minus');
        icono.classList.add('glyphicon-plus');
    } else {
        // Mostrar detalles
        fila.style.display = '';
        boton.classList.remove('btn-success');
        boton.classList.add('btn-danger');
        icono.classList.remove('glyphicon-plus');
        icono.classList.add('glyphicon-minus');
    }
}  

</script>
