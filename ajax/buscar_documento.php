<?php
header('Content-Type: application/json');

if (!isset($_POST['numero'])) {
    echo json_encode(['success' => false, 'message' => 'No se recibió número de documento']);
    exit;
}

$numero = trim($_POST['numero']);
$length = strlen($numero);

if ($length == 11) {
    // RUC
    $url = "https://www.amcsolutionstec.com/produccion/api/api/getRuc/" . $numero;
} elseif ($length == 8) {
    // DNI
    $url = "https://www.amcsolutionstec.com/produccion/api/api/getDni/" . $numero;
} else {
    echo json_encode(['success' => false, 'message' => 'Número de documento inválido']);
    exit;
}

// Inicializar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
$result = curl_exec($ch);
curl_close($ch);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Error al conectar con la API']);
    exit;
}

$data = json_decode($result);

if (!$data || !isset($data->result)) {
    echo json_encode(['success' => false, 'message' => 'Documento no encontrado en SUNAT']);
    exit;
}

if ($length == 11) {
    // RUC
    $response = [
        'success' => true,
        'tipo_cliente' => 1,
        'razon_social' => $data->result->razon_social ?? '',
        'direccion' => $data->result->direccion ?? '',
        'ubigeo' => $data->result->ubigeo ?? ''
    ];
} else {
    // DNI
    $response = [
        'success' => true,
        'tipo_cliente' => 0,
        'nombres' => html_entity_decode($data->result->nombres ?? ''),
        'apellido_paterno' => html_entity_decode($data->result->apellido_paterno ?? ''),
        'apellido_materno' => html_entity_decode($data->result->apellido_materno ?? '')
    ];
}

echo json_encode($response);
