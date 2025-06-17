		$(document).ready(function(){
			load(1);
		});

		function load(page){
			var q = $("#q").val();
			var precioVenta = $("#precioVenta").val(); // Asegúrate de que este campo exista
			var marca = $("#marcaSelect").val(); // Nuevo: capturamos la marca seleccionada
			var almacen = $("#id_almacen").val(); // Nuevo: capturamos la marca seleccionada
			
			$("#loader").fadeIn('slow');
			$.ajax({
				url: './ajax/buscar_productos.php?action=ajax&page=' + page + '&q=' + q + '&precioVenta=' + precioVenta + '&marca=' + marca + '&almacen=' + almacen,
				beforeSend: function(objeto){
					$('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
				},
				success: function(data){
					$(".outer_div").html(data).fadeIn('slow');
						$('#dataproducto').html(response);
					$('#resultados').html('');
				}


			});
		}


	
		
			function eliminar (id)
		{
			var q= $("#q").val();
		if (confirm("Realmente deseas eliminar el producto")){	
		$.ajax({
        type: "GET",
        url: "./ajax/buscar_productos.php",
        data: "id="+id,"q":q,
		 beforeSend: function(objeto){
			$("#resultados").html("Mensaje: Cargando...");
		  },
        success: function(datos){
		$("#resultados").html(datos);
		load(1);
		}
			});
		}
		}
		
		
		
		

