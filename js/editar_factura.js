$(document).ready(function(){
	load(1);
	var session = $("#session").val();
	$( "#resultados" ).load( "ajax/editar_facturacion.php?session="+session );
	tipoForpap()
});

function load(page){
	var q= $("#q").val();
	var precioVenta = $("#precioVenta").val();
	var session = $("#session").val();
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'./ajax/productos_factura.php?action=ajax&page='+page+'&q='+q + '&precioVenta=' + precioVenta,
		data: '&session=' + session,
		beforeSend: function(objeto){
			$('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
		},
		success:function(data){
			$(".outer_div").html(data).fadeIn('slow');
			$('#loader').html('');				
		}
	})
}

function editar(session, total){
	id_cliente = $("#id_cliente").val();
	id_vendedor = $("#id_vendedor").val();
	id_almacen = $("#id_almacen").val();
	obs = $("#observacion").val();
	fechaEntMin = $("#fechaEntMin").val();
	fechaEntMax = $("#fechaEntMax").val();
	hoy = $("#hoy").val();
	MontoNecesario = $("#SumaTotal").val();
	MontoIngresado = $("#montoDefault").val();
	ForpapDefault = $("#FormaPagoDefault").val();

	if(id_cliente == ""){
		Swal.fire({
			title: "Agrege un cliente",
			icon: "error",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
			timer: 1100
		});
		return
	}

	if(fechaEntMin != "" && fechaEntMin < hoy){
		Swal.fire({
			title: "Las fechas no pueden ser antes que hoy",
			icon: "error",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
			timer: 1100
		});
		return
	}

	if(fechaEntMax != "" && fechaEntMax < hoy){
		Swal.fire({
			title: "Las fechas no pueden ser antes que hoy",
			icon: "error",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
			timer: 1100
		});
		return
	}
	
	if(fechaEntMax < fechaEntMin && fechaEntMax != ""){
		Swal.fire({
			title: "Fechas inválidas",
			icon: "error",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
			timer: 1100
		});
		return
	}

	var n = 0;
	var allcode = Array();
	var allcant = Array();
	var allnom = Array();
	var allpu = Array();
	var allpt = Array();
	$("#tableEditar tr").each(function() {
		n++;
		cod = $("#prod_cod_"+n).val();
		cant = $("#prod_cant_"+n).val();
		nom = $("#prod_nom_"+n).val();
		pu = $("#prod_pu_"+n).val();
		pt = $("#prod_pt_"+n).val();
		if(cod != null){
			allcode[n-1]=cod;
			allcant[n-1]=cant;
			allnom[n-1]=nom;
			allpu[n-1]=pu;
			allpt[n-1]=pt;
		}
	})

	if(allcode == ""){
		Swal.fire({
			title: "Agrege al menos un producto",
			icon: "error",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
		});
		return
	}

	CodArray   = Array();
	MontoArray = Array();
	TotalMult = 0;
	if(MontoIngresado == ""){
		CodMultiple = document.getElementsByClassName("FormaPagoModal");
		for (var i=0; i<CodMultiple.length; i++) {
			CodArray[i]=CodMultiple[i].value;
		}
		MontoMultiple = document.getElementsByClassName("montoModal");
		for (var i=0; i<MontoMultiple.length; i++) {
			MontoArray[i]=MontoMultiple[i].value;
			TotalMult += Number(MontoMultiple[i].value);
		}
		if(TotalMult != MontoNecesario){
			Swal.fire({
				title: "Monto multiple inválido",
				icon: "error",
				confirmButtonColor: "#3085d6",
				confirmButtonText: "Ok",
				timer: 1100
			});
			return
		}
	}else if(Number(MontoIngresado)!=MontoNecesario){
		Swal.fire({
			title: "Monto ingresado inválido",
			icon: "error",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
			timer: 1100
		});
		return
	}

	$.ajax({
		type: "GET",
		url: "./ajax/editar_facturacion.php",
		dataType: "json",
		data: "editarS=" + session + "&id_cliente=" + id_cliente + "&id_vendedor=" + id_vendedor + "&id_almacen=" + id_almacen + "&total=" + total 
		+ "&allcode=" + allcode + "&allcant=" + allcant + "&allnom=" + allnom + "&allpu=" + allpu + "&allpt=" + allpt+ "&fechaEntMin=" + fechaEntMin + "&fechaEntMax=" + fechaEntMax + "&obs="+ obs
		+ "&montoDefault=" + MontoIngresado + "&forpapDefault=" + ForpapDefault + "&montoMult=" + MontoArray + "&forpapMult=" + CodArray,
		beforeSend: function (objeto) {

		},
		success: function (datos) {
			if(datos.result == "success"){
				Swal.fire({
					title: datos.message,
					icon: "success",
					confirmButtonColor: "#3085d6",
					confirmButtonText: "Ok",
				}).then((result) => {
					if (result.isConfirmed){
						location.href = "./facturas.php";
					}else{
						location.href = "./facturas.php";
					}
				})
			}else if(datos.result == "error"){
				Swal.fire({
					title: datos.message,
					icon: "error",
					confirmButtonColor: "#3085d6",
					confirmButtonText: "Ok",
				})
			}
		},
		error: function (datos){
			Swal.fire({
				title: datos.message,
				icon: "error",
				confirmButtonColor: "#3085d6",
				confirmButtonText: "Ok",
			})
		}
	});
}


function agregar(id,session) {
	var cantidad = document.getElementById('cantidad_' + id).value;
	var detalle = document.getElementById('detalle_' + id).value;
	var precio = document.getElementById('precio_venta_' + id).value;
	var select = document.getElementById('select_' + id).value;
	var nombre = document.getElementById('nombre_' + id).value;
	var stockDis = document.getElementById('stock_' + id).value;
	var n = 0;
	var error;

	if (isNaN(cantidad) || cantidad <= 0) {
		Swal.fire({
			title: "Cantidad Inválida",
			icon: "error",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
			timer: 1100
		});
		document.getElementById('cantidad_' + id).focus();
		return false;
	}

	if (Number(cantidad) > Number(stockDis)) {
		Swal.fire({
			title: "Cantidad fuera de Stock",
			icon: "error",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
			timer: 1100
		});
		document.getElementById('cantidad_' + id).focus();
		return false;
	}

	if (isNaN(precio) || precio <= 0) {
		Swal.fire({
			title: "Precio Inválida",
			icon: "error",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
			timer: 1100
		});
		document.getElementById('precio_venta_' + id).focus();
		return false;
	}

	if(select != "false"){
		Swal.fire({
			title: "El producto ya se encuentra ingresado en la lista de detalles.",
			icon: "warning",
			confirmButtonColor: "#3085d6",
			confirmButtonText: "Ok",
			timer: 1100
		});
		return false;
	}

	$("#tableEditar tr").each(function() {
		n++;
		cod = $("#prod_cod_"+n).val();
		if(cod == id){
			Swal.fire({
				title: "El producto ya se encuentra ingresado en la lista de detalles.",
				icon: "warning",
				confirmButtonColor: "#3085d6",
				confirmButtonText: "Ok",
				timer: 1100
			});
			error = "false";
			return false;
		}
	})

	if (error == "false"){
		return false;
	}

	
	//Fin validacion
	//annel detalle adicional
	$.ajax({
		type: "POST",
		url: "./ajax/editar_facturacion.php",
		data: "id=" + id + "&cantidad=" + cantidad + "&detalle=" + detalle + "&precio_venta=" + precio + "&session=" + session + "&nombre=" + nombre,
		beforeSend: function (objeto) {
			$("#resultados").html("Mensaje: Cargando...");
		},
		success: function (datos) {
			$("#resultados").html(datos);
			$("#select_"+id).val("select");
			total = $("#SumaTotal").val();
			$("#montoDefault").val(total);
		}
	});
}

function eliminar(id, session) {
	idprod = document.getElementById('get_' + id).value;
	total = document.getElementById('total_' + id).value;
	Swal.fire({
		title: "¿Desea eliminar el producto?",
		icon: "info",
		confirmButtonColor: "#3085d6",
		showDenyButton: true,
		confirmButtonText: "Si",
		denyButtonText: "No",
	  }).then((result) => {
		if (result.isConfirmed) {
			newtotal = ($("#SumaTotal").val()) - total;
			$("#montoDefault").val(newtotal);
			$.ajax({
				type: "GET",
				url: "./ajax/editar_facturacion.php",
				data: "id=" + id + "&sesionE=" + session,
				beforeSend: function (objeto) {
					$("#resultados").html("Mensaje: Cargando...");
				},
				success: function (datos) {
					$("#resultados").html(datos);
					$("#select_"+idprod).val("false");
					
				}
			});
		}
	  });
}
		
$("#datos_factura").submit(function(event){
	var id_cliente = $("#id_cliente").val();	  
	if (id_cliente==""){
		alert("Debes seleccionar un cliente");
		$("#nombre_cliente").focus();
		return false;
	}
	var parametros = $(this).serialize();
	$.ajax({
		type: "POST",
		url: "ajax/editar_factura.php",
		data: parametros,
		beforeSend: function(objeto){
			$(".editar_factura").html("Mensaje: Cargando...");
		},
		success: function(datos){
			$(".editar_factura").html(datos);
		}
	});		
	event.preventDefault();
});
		
$("#guardar_cliente").submit(function( event ) {
	$('#guardar_datos').attr("disabled", true);	  
	var parametros = $(this).serialize();
	$.ajax({
		type: "POST",
		url: "ajax/nuevo_cliente.php",
		data: parametros,
		beforeSend: function(objeto){
			$("#resultados_ajax").html("Mensaje: Cargando...");
		},
		success: function(datos){
			$("#resultados_ajax").html(datos);
			$('#guardar_datos').attr("disabled", false);
			load(1);
		}
	});
	event.preventDefault();
})
		
$( "#guardar_producto" ).submit(function( event ) {
	$('#guardar_datos').attr("disabled", true);  
	var parametros = $(this).serialize();
	$.ajax({
		type: "POST",
		url: "ajax/nuevo_producto.php",
		data: parametros,
		beforeSend: function(objeto){
			$("#resultados_ajax_productos").html("Mensaje: Cargando...");
		},
		success: function(datos){
			$("#resultados_ajax_productos").html(datos);
			$('#guardar_datos').attr("disabled", false);
			load(1);
		}
	});
	event.preventDefault();
})


const tableFormasPago = $('#tableFormaPago');


function tipoForpap(){
	if($("#FormaPagoDefault").val() == 22){
		$("#Monto").css("display","none");
		$("#Multiple").css("display","block");
		$("#montoDefault").val("");
	}else{
		$("#Multiple").css("display","none");
		$("#Monto").css("display","block");
	}
}

function agregarFila(){
	var tr = tableFormasPago.find('tbody tr').last().clone();
    $(tr).find('input[type="number"]').val('')
    $(tr).find('.FormaPago').val('')
    $(tr).find('.monto').val('')
    $(tr).find('.borrar').on('click', function(){
        if (tableFormasPago.find('tbody tr').length > 1)
            $(tr).remove();
        });
    tableFormasPago.find('tbody').append(tr);
}

$('.borrar').click(function(){
var tr = $(this).closest('tr');
    if (tableFormasPago.find('tbody tr').length > 2)
        $(tr).remove();
});

// function imprimir_factura(id_factura){
// 	VentanaCentrada('./pdf/documentos/ver_factura.php?id_factura='+id_factura,'Factura','','1024','768','true');
// }