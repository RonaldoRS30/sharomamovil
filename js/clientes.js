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
	$('#guardar_datos').attr("disabled", true);

	var parametros = $(this).serialize();
	$.ajax({
		type: "POST",
		url: "ajax/nuevo_cliente.php",
		data: parametros,
		beforeSend: function (objeto) {
			$("#resultados_ajax").html("Mensaje: Cargando...");
		},
		success: function (datos) {
			$("#resultados_ajax").html(datos);
			$('#guardar_datos').attr("disabled", false);
			load(1);

			// Limpiar el modal
			$('#nombres').val('');
			$('#apepa').val('');
			$('#apema').val('');
			$('#razon').val('');
			$('#telefono').val('');
			$('#numero_documento').val('');
			$('#documento').val('');
			$('#email').val('');
			$('#direccion').val('');
			$('#pais').val('');

			// Mostrar el modal si no está abierto
			$('#modal_cliente').modal('show'); // Asegúrate de que el ID de tu modal sea correcto

			// Temporizador para ocultar el mensaje después de 3 segundos
			setTimeout(function () {
				$("#resultados_ajax").fadeOut(500); // Ocultar el mensaje después de 3 segundos
			}, 1000);
		}
	});
	event.preventDefault();
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




