$(document).ready(function () {
	load(1);
});

function load(page) {
	var q = $("#q").val();
	$("#loader").fadeIn('slow');
	$.ajax({
		url: './ajax/buscar_clientes.php?action=ajax&page=' + page + '&q=' + q,
		beforeSend: function (objeto) {
			$('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
		},
		success: function (data) {
			$(".outer_div").html(data).fadeIn('slow');
			$('#loader').html('');

		}
	})
}



function eliminar(id) {
	var q = $("#q").val();
	if (confirm("Realmente deseas eliminar el cliente")) {
		$.ajax({
			type: "GET",
			url: "./ajax/buscar_clientes.php",
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



$("#guardar_cliente").submit(function (event) {
	event.preventDefault(); // Evita el envío por defecto
	$('#guardar_datos').attr("disabled", true);

	var parametros = $(this).serialize();
	$.ajax({
		type: "POST",
		url: "ajax/nuevo_cliente.php",
		data: parametros,
		dataType: "json", // Esperar JSON del servidor
		beforeSend: function () {
			$("#resultados_ajax").html("Mensaje: Cargando...");
		},
		success: function (datos) {
			// Rellenar campos en nueva_factura.php con datos recibidos
			$("#id_cliente").val(datos.id_cliente);
			$("#nombre_cliente").val(datos.nombre_cliente);
			$("#ruc_cliente").val(datos.ruc_cliente);
			// Reemplazar saltos de línea por espacios
			// Reemplaza las secuencias literales '\r\n' por saltos de línea reales
			// Reemplazar las secuencias literales '\r\n' por un espacio
				var textoLimpio = datos.direc_cliente.replace(/\\r\\n/g, " ");

				// Asignar el texto limpio al input o textarea
				$("#direc_cliente").val(textoLimpio);



			$("#resultados_ajax").html("Cliente registrado correctamente.");
			$('#guardar_datos').attr("disabled", false);
			load(1); // Recarga la lista

			// Mostrar alerta con SweetAlert
			Swal.fire({
				icon: 'success',
				title: 'Cliente registrado',
				text: 'El cliente fue añadido correctamente.',
				timer: 2000,
				showConfirmButton: false
			});

			// Cerrar el modal
			$('#nuevoCliente').modal('hide');

			// Limpiar campos del formulario
			$('#guardar_cliente')[0].reset();

			// Ocultar el mensaje de resultados
			setTimeout(function () {
				$("#resultados_ajax").fadeOut(500);
			}, 1000);
		},
		error: function (xhr, status, error) {
			$("#resultados_ajax").html("Error al registrar cliente: " + error);
			$('#guardar_datos').attr("disabled", false);
		}
	});
});




$("#editar_cliente").submit(function (event) {
	$('#actualizar_datos').attr("disabled", true);

	var parametros = $(this).serialize();
	$.ajax({
		type: "POST",
		url: "ajax/editar_cliente.php",
		data: parametros,
		beforeSend: function (objeto) {
			$("#resultados_ajax2").html("Mensaje: Cargando...");
		},
		success: function (datos) {
			$("#resultados_ajax2").html(datos);
			$('#actualizar_datos').attr("disabled", false);
			load(1);
		}
	});
	event.preventDefault();
})

function obtener_datos(id,tipo) {
	var nombre_cliente = $("#nombre_cliente" + id).val();
	var num_cliente = $("#num_cliente" + id).val();
	var telefono_cliente = $("#telefono_cliente" + id).val();
	var email_cliente = $("#email_cliente" + id).val();
	var direccion_cliente = $("#direccion_cliente" + id).val();
	var status_cliente = $("#status_cliente" + id).val();
	var nombre_persona = $("#nombre_persona" + id).val();
	var apepa_persona = $("#apepa_persona" + id).val();
	var apema_persona = $("#apema_persona" + id).val();
	var idSec = $("#idSec_" + id).val();
	nom = document.getElementById("divNomMod")
	ruc = document.getElementById("divRucMod")


	$("#mod_ruc").val(num_cliente);
	$("#mod_telefono").val(telefono_cliente);
	$("#mod_email").val(email_cliente);
	$("#mod_direccion").val(direccion_cliente);
	$("#mod_estado").val(status_cliente);
	$("#mod_id").val(id);
	$("#mod_tipo").val(tipo);
	$("#mod_idSec").val(idSec);

	if(tipo != 1){
		$("#mod_nombres").val(nombre_persona);
		$("#mod_apepa").val(apepa_persona);
		$("#mod_apema").val(apema_persona);
		nom.style.display = "block";
		ruc.style.display = "none";
		$('#mod_razon').removeAttr("required");

		$('#mod_nombres').prop("required", true);
		$('#mod_apepa').prop("required", true);
		$('#mod_apema').prop("required", true);
	}else{
		$("#mod_razon").val(nombre_cliente);
		nom.style.display = "none";
		ruc.style.display = "block";
		$('#mod_nombres').removeAttr("required");
		$('#mod_apepa').removeAttr("required");
		$('#mod_apema').removeAttr("required");

		$('#mod_razon').prop("required", true);
	}
}




