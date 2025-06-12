$(document).ready(function () {
	load(1);

});

function load(page, session) {
	var fecha_inicio = $("#fecha_inicio").val();
	var fecha_fin = $("#fecha_fin").val();
	var documentoCliente = $("#documentoCliente").val();
	var nombreCliente = $("#nombreCliente").val();
	var tipo_search = $("#tipo_search").val();
	var encargado = $("#encargado_search").val();
	var doc = $("#doc_search").val();
	var pais = $("#pais_search").val();

	$("#loader").fadeIn('slow');
	$.ajax({
		url: './ajax/buscar_facturas.php?action=ajax&page=' + page + '&fecha_inicio=' + fecha_inicio + '&fecha_fin=' + fecha_fin + '&tipo_search=' 
		+ tipo_search + "&encargado_search=" + encargado + "&doc=" + doc + "&pais=" + pais + "&documentoCliente=" + documentoCliente + "&nombreCliente=" + nombreCliente,
		beforeSend: function (objeto) {
			$('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
		},
		success: function (data) {
			$(".outer_div").html(data).fadeIn('slow');
			$('#loader').html('');
			$('[data-toggle="tooltip"]').tooltip({ html: true });

		}
	})
}
function editar_factura(id,session){
	$.ajax({
		type: "POST",
		url:'./editar_factura.php?id='+id+'&session='+session,
		beforeSend: function(objeto){
			$('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
		},
		success:function(data){
			$(".outer_div").html(data).fadeIn('slow');
			$('#loader').html('');				
		}
	})
}



function eliminar(id) {
	var q = $("#q").val();
	if (confirm("¿Realmente desea Cancelar este documento?")) {
		$.ajax({
			type: "GET",
			url: "./ajax/buscar_facturas.php",
			data: "id=" + id, "q": q,
			beforeSend: function (objeto) {
				$("#resultados").html("Mensaje: Cargando...");
			},
			success: function (datos) {
				$("#resultados").html(datos);
				load(1);
			}
		});
	}
}

function aprobar(id) {
	var q = $("#q").val();
	if (confirm("¿Realmente desea Aprobar este documento?")) {
		$.ajax({
			type: "GET",
			url: "./ajax/buscar_facturas.php",
			data: "id2=" + id, "q": q,
			beforeSend: function (objeto) {
				$("#resultados").html("Mensaje: Cargando...");
			},
			success: function (datos) {
				$("#resultados").html(datos);
				load(1);
			}
		});
	}
}

function imprimir_factura(id_factura) {
	location.href= "./pdf/documentos/ver_factura.php?id_factura=" + id_factura;
}

