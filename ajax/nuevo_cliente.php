<?php
include('is_logged.php'); // Archivo verifica que el usuario que intenta acceder a la URL esté logueado

/* Inicia validación del lado del servidor */
if (empty($_POST['numero_documento'])) {
    $errors[] = "Nombre vacío";
} else if (!empty($_POST['numero_documento'])) {
    /* Conectar a la base de datos */
    require_once("../config/db.php"); // Contiene las variables de configuración para conectar a la base de datos
    require_once("../config/conexion.php"); // Contiene función que conecta a la base de datos

    // Escapar y eliminar todo lo que pueda ser código (html/javascript)
    $nombres = mysqli_real_escape_string($con, (strip_tags($_POST["nombres"], ENT_QUOTES)));
    $apepa = mysqli_real_escape_string($con, (strip_tags($_POST["apepa"], ENT_QUOTES)));
    $apema = mysqli_real_escape_string($con, (strip_tags($_POST["apema"], ENT_QUOTES)));
    $razon = mysqli_real_escape_string($con, (strip_tags($_POST["razon"], ENT_QUOTES)));
    $num = mysqli_real_escape_string($con, (strip_tags($_POST["numero_documento"], ENT_QUOTES))); // Cambié 'num' a 'numero_documento' para coincidir
    $telefono = mysqli_real_escape_string($con, (strip_tags($_POST["telefono"], ENT_QUOTES)));
    $email = isset($_POST["email"]) ? mysqli_real_escape_string($con, strip_tags($_POST["email"], ENT_QUOTES)) : '';
    $direccion = mysqli_real_escape_string($con, (strip_tags($_POST["direccion"], ENT_QUOTES)));
    $estado = intval($_POST["estado"]);
    $tipodoc = intval($_POST['tipo_documento']);
    $date_added = date("Y-m-d H:i:s");

    // Verificar si el número de documento ya está registrado en la tabla persona o empresa
    $checkPersona = mysqli_query($con, "SELECT * FROM cji_persona WHERE PERSC_NumeroDocIdentidad = '$num'");
    $checkEmpresa = mysqli_query($con, "SELECT * FROM cji_empresa WHERE EMPRC_Ruc = '$num'");

    if (mysqli_num_rows($checkPersona) > 0) {
        $errors[] = "El número de documento ya está registrado en la tabla de personas.";
    } else if (mysqli_num_rows($checkEmpresa) > 0) {
        $errors[] = "El número de documento ya está registrado en la tabla de empresas.";
    } else {
        //Realizar la inserción si el número de documento no está registrado

        //Persona
        if ($tipodoc == 1) {
    $sqlPersona = "INSERT INTO cji_persona (UBIGP_LugarNacimiento, UBIGP_Domicilio, PERSC_TipoDocIdentidad, PERSC_Nombre, PERSC_ApellidoPaterno, PERSC_ApellidoMaterno, PERSC_NumeroDocIdentidad, PERSC_Direccion, PERSC_Domicilio, PERSC_Telefono, PERSC_Email, PERSC_FechaRegistro, PERSC_FlagEstado, PERSC_EmpCod)
    VALUES('000000','150101','$tipodoc','$nombres','$apepa','$apema','$num','$direccion','$direccion','$telefono','$email','$date_added','$estado','1')";
    $queryPersona = mysqli_query($con, $sqlPersona);
    if (!$queryPersona) {
        echo json_encode(['error' => "Error al insertar persona: " . mysqli_error($con)]);
        exit;
    }
    $idPersona = mysqli_insert_id($con);

    $sqlCliente = "INSERT INTO cji_cliente(PERSP_Codigo, CLIC_FechaRegistro, CLIC_TipoPersona, TIPCLIP_Codigo, CLIC_Vendedor, FORPAP_Codigo, CLIC_FlagEstado, CLIC_flagCalifica) 
    VALUES ('$idPersona', '$date_added', '0', '0','0','0','$estado','1')";
    $queryCliente = mysqli_query($con, $sqlCliente);
    if (!$queryCliente) {
        echo json_encode(['error' => "Error al insertar cliente: " . mysqli_error($con)]);
        exit;
    }
    $idCliente = mysqli_insert_id($con);

    $codiCli = "CL0".$idCliente;
    mysqli_query($con, "UPDATE cji_cliente SET CLIC_CodigoUsuario = '$codiCli' WHERE CLIC_Codigo = $idCliente");
    mysqli_query($con, "INSERT INTO cji_clientecompania (CLIC_Codigo, COMPP_Codigo) VALUES('$idCliente','1')");

    echo json_encode([
        'id_cliente' => $idCliente,
        'nombre_cliente' => $nombres . ' ' . $apepa . ' ' . $apema,
        'ruc_cliente' => $num,
        'direc_cliente' => $direccion
    ]);
    exit;

} else {
    $sqlEmpresa = "INSERT INTO cji_empresa (EMPRC_Ruc, TIPCOD_Codigo, EMPRC_RazonSocial, EMPRC_Telefono, EMPRC_Email, EMPRC_FechaRegistro, EMPRC_FlagEstado, EMPRC_Direccion, EMPRC_EstadoPago)
    VALUES('$num','6','$razon','$telefono','$email','$date_added','1','$direccion','1')";
    $queryEmpresa = mysqli_query($con, $sqlEmpresa);
    if (!$queryEmpresa) {
        echo json_encode(['error' => "Error al insertar empresa: " . mysqli_error($con)]);
        exit;
    }
    $idEmpresa = mysqli_insert_id($con);

    $sqlCliente = "INSERT INTO cji_cliente(EMPRP_Codigo, PERSP_Codigo, CLIC_FechaRegistro, CLIC_TipoPersona, TIPCLIP_Codigo, CLIC_Vendedor, FORPAP_Codigo, CLIC_FlagEstado, CLIC_flagCalifica) 
    VALUES ('$idEmpresa','0', '$date_added', '1', '0','0','0','1','1')";
    $queryCliente = mysqli_query($con, $sqlCliente);
    if (!$queryCliente) {
        echo json_encode(['error' => "Error al insertar cliente: " . mysqli_error($con)]);
        exit;
    }
    $idCliente = mysqli_insert_id($con);

    $codiCli = "CL0".$idCliente;
    mysqli_query($con, "UPDATE cji_cliente SET CLIC_CodigoUsuario = '$codiCli' WHERE CLIC_Codigo = $idCliente");
    mysqli_query($con, "INSERT INTO cji_clientecompania (CLIC_Codigo, COMPP_Codigo) VALUES('$idCliente','1')");

    echo json_encode([
        'id_cliente' => $idCliente,
        'nombre_cliente' => $razon,
        'ruc_cliente' => $num,
        'direc_cliente' => $direccion
    ]);
    exit;
}
    }

    if ($queryCliente) {
        $messages[] = "Cliente ha sido ingresado satisfactoriamente.";
    } else {
        $errors[] = "Lo siento, algo ha salido mal. Intenta nuevamente." . mysqli_error($con);
    }
} else {
    $errors[] = "Error desconocido.";
}

/* Mostrar errores o mensajes */
if (isset($errors)) {
    ?>
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>Error!</strong>
        <?php
        foreach ($errors as $error) {
            echo $error; // Aquí se imprimen todos los errores, como el del cliente duplicado
        }
        ?>
    </div>
    <?php
}
if (isset($messages)) {
    ?>
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>¡Bien hecho!</strong>
        <?php
        foreach ($messages as $message) {
            echo $message;
        }
        ?>
    </div>
    <?php
}
?>
