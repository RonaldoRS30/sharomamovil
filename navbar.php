	<?php
  if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
    header("location: login.php");
exit;
    }
		if (isset($title))
		{
	?>
<nav class="navbar navbar-default ">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Pedido</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="<?php echo $active_facturas;?>"><a href="facturas.php"><i class='glyphicon glyphicon-list-alt'></i> Pedido <span class="sr-only">(current)</span></a></li>
        <li class="<?php echo $active_productos;?>"><a href="productos.php"><i class='glyphicon glyphicon-barcode'></i> Productos</a></li>
		<li class="<?php echo $active_clientes;?>"><a href="clientes.php"><i class='glyphicon glyphicon-user'></i> Clientes</a></li>
    <?php if($_SESSION['user_id'] == 1) {?>
		<li class="<?php echo $active_usuarios;?>"><a href="usuarios.php"><i  class='glyphicon glyphicon-lock'></i> Usuarios</a></li>
		<li class="<?php echo $active_mapa;?>"><a href="mapa.php"><i  class='glyphicon glyphicon-globe'></i> Mapa</a></li>

		<li class="<?php echo $active_reportes;?>">
			
				<!-- <a href="#" class="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i  class='glyphicon glyphicon-list-alt'></i> Reportes
							<div class="dropdown-menu" >
								<br>
                <p><a class="dropdown-item" href="reportes.php"><i  class='glyphicon glyphicon-usd'></i> Ganacias</a></p>

                <p><a class="dropdown-item" href="#"><i  class='glyphicon glyphicon-chevron-right'></i>Ganacias</a></p>
					  	</div>
					</a> -->


			

		</li>


		<li class="<?php if(isset($active_perfil)){echo $active_perfil;}?>"><a href="perfil.php"><i  class='glyphicon glyphicon-cog'></i> Configuración</a></li>
    <?php } ?>
       </ul>
      <ul class="nav navbar-nav navbar-right">

		<li><a href="login.php?logout"><i class='glyphicon glyphicon-off'></i> Salir</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
	<?php
		}
	?>

