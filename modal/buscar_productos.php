	<?php
		if (isset($con))
		{
	
	?>	
			<!-- Modal -->
			<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			  <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Buscar productos</h4>
				  </div>
				  <div class="modal-body">
					<form class="form-horizontal">
					  <div class="form-group">
						<div class="col-sm-6">
						  <input type="hidden" id="session" value="<?=$session_id?>">
						  <input type="text" class="form-control" id="q" placeholder="Buscar productos" onkeyup="load(1)">
						</div>
						<label for="precioVenta" class="col-md-3 control-label">Precios:</label>
							<div class="col-md-3">
								<select class="form-control" id="precioVenta" onchange='load(1);'>
								<?php
										$sql = "SELECT * FROM cji_tipocliente WHERE TIPCLIC_FlagEstado = 1";
										$query = mysqli_query($con, $sql);
										$j=1;
										while($row=mysqli_fetch_array($query)){
											if(isset($_SESSION['Zona']) && $_SESSION['Zona']==$j){
												$select= "selected";
											}else{
												$select = "";
											}
												?>
												<option value="<?=$j?>" id="" <?=$select?>><?=$row['TIPCLIC_Descripcion']?></option>
											<?php
											$j++;
										}
									?>
								</select>
							</div>
						<!--<button type="button" class="btn btn-default" onclick="load(1)"><span class='glyphicon glyphicon-search'></span> Buscar</button>-->
					  </div>
					</form>
					<div id="loader" style="position: absolute;	text-align: center;	top: 55px;	width: 100%;display:none;"></div><!-- Carga gif animado -->
					<div class="outer_div">
					</div><!-- Datos ajax Final -->
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					
				  </div>
				</div>
			  </div>
			</div>
	<?php
		}
	?>