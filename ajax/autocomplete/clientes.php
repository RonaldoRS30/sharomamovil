<?php
if (isset($_GET['term'])){
include("../../config/db.php");
include("../../config/conexion.php");
$return_arr = array();
/* If connection to database, run sql statement. */
if ($con) {
    $term = mysqli_real_escape_string($con, $_GET['term']);
    $sql = "SELECT c.*, p.*, e.*,
            (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Ruc ELSE p.PERSC_NumeroDocIdentidad end) as documento,
            (CASE c.CLIC_TipoPersona  WHEN '1'
                THEN e.EMPRC_RazonSocial
                ELSE CONCAT(p.PERSC_Nombre , ' ', p.PERSC_ApellidoPaterno, ' ', p.PERSC_ApellidoMaterno) end) nombre,
            (CASE c.CLIC_TipoPersona WHEN 1 THEN e.EMPRC_Direccion ELSE p.PERSC_Direccion end) as direccion
         FROM cji_cliente AS c
         LEFT JOIN cji_persona AS p ON c.PERSP_Codigo = p.PERSP_Codigo
         LEFT JOIN cji_empresa AS e ON c.EMPRP_Codigo = e.EMPRP_Codigo
         WHERE (p.PERSC_Nombre LIKE '%$term%'
                OR p.PERSC_ApellidoPaterno LIKE '%$term%'
                OR p.PERSC_ApellidoMaterno LIKE '%$term%'
                OR p.PERSC_NumeroDocIdentidad LIKE '%$term%'
                OR e.EMPRC_RazonSocial LIKE '%$term%'
                OR e.EMPRC_Ruc LIKE '%$term%')
         AND c.CLIC_FlagEstado != 0 
         LIMIT 0, 50";
    $fetch = mysqli_query($con, $sql);

    /* Retrieve and store in array the results of the query. */
    while ($row = mysqli_fetch_array($fetch)) {
        $id_cliente = $row['CLIP_Codigo'];
        $row_array['value'] = $row['nombre'];
        $row_array['PERSP_Codigo'] = $id_cliente;
        $row_array['nombre_cliente'] = $row['nombre'];
        $row_array['ruc_cliente'] = $row['documento'];
        $row_array['direc_cliente'] = $row['direccion'];
        array_push($return_arr, $row_array);
    }
}
/* Free connection resources. */
mysqli_close($con);

/* Toss back results as json encoded array. */
echo json_encode($return_arr);
}
/* function search_documento() {
    // Simulación de acceso a la base de datos
    $empresa_pertenece = 'nombre_empresa'; // Cambiar por el valor adecuado
    $numero = trim($_POST['numero']);
    $exists = false; // Supón que verificas la existencia en tu base de datos aquí.

    if ($exists == false) {
        $getCode = generateCodeCliente();

        if (strlen($numero) == 11) {
            $url = "https://www.macada.com/prod/api/api/getRuc/" . $numero;

            $result = makeApiCall($url);

            if ($result == NULL) {
                response(array("message" => "El cliente no está registrado en Sunat."));
            } else {
                $con = json_decode($result);

                $info = new stdClass();
                $info->result = new stdClass();
                $info->result->razon_social = $con->result->razon_social;
                $info->result->direccion = $con->result->direccion;
                $info->success = true;

                if ($info->success == true) {
                    $info->result->ubigeo = $con->result->ubigeo; // Puedes obtener el ubigeo aquí

                    $json = array(
                        "exists" => $exists,
                        "match" => true,
                        "tipo_cliente" => 1,
                        "message" => "El documento fue encontrado",
                        "info" => $info,
                        "id_cliente" => $getCode
                    );
                } else {
                    $json = array("exists" => $exists, "match" => false, "message" => "El documento no está registrado en Sunat.");
                }
            }
        } else {
            $url = "https://www.macada.com/prod/api/api/getDni/" . $numero;

            $result = makeApiCall($url);
            $datos = array();

            if ($result == NULL) {
                $json = array(
                    "exists" => $exists,
                    "match" => false,
                    "tipo_cliente" => 0,
                    "message" => "El documento no fue encontrado",
                    "info" => $datos,
                    "id_cliente" => $getCode
                );
            } else {
                $con = json_decode($result);
                $datos = array(
                    "dni" => $numero,
                    "nombre" => html_entity_decode($con->result->nombres),
                    "paterno" => html_entity_decode($con->result->apellido_paterno),
                    "materno" => html_entity_decode($con->result->apellido_materno)
                );

                $json = array(
                    "exists" => $exists,
                    "match" => true,
                    "tipo_cliente" => 0,
                    "message" => "El documento fue encontrado",
                    "info" => $datos,
                    "id_cliente" => $getCode
                );
            }
        }

        echo json_encode($json);
    } else {
        $json = array("exists" => $exists, "match" => true, "message" => "El documento $numero fue registrado anteriormente.");
        echo json_encode($json);
    }
	
} */
?>