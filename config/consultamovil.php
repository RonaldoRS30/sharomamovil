<?php

$servername = "localhost"; // o la dirección IP del servidor MySQL
$username = "root"; // el nombre de usuario de la base de datos
$password = ""; // la contraseña de la base de datos
$dbname = "sistemadefactura_sharoma"; // el nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos enviados desde la solicitud POST
$telefonoUsuario = $_POST['telefonoUsuario'];
$latitud = $_POST['latitud'];
$longitud = $_POST['longitud'];
$direccion = $_POST['direccion'];
$provincia = $_POST['provincia'];
$departamento = $_POST['departamento'];
$distrito = $_POST['distrito'];
$bateria = $_POST['bateria'];
$fechaRegistro = $_POST['fechaRegistro'];

// Llamar al procedimiento almacenado
$sql = "CALL insertarRastreo(?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Preparar la sentencia
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssis", $telefonoUsuario, $latitud, $longitud, $direccion, $provincia, $departamento, $distrito, $bateria, $fechaRegistro);

// Ejecutar el procedimiento
if ($stmt->execute()) {
    echo json_encode(array("message" => "Datos insertados correctamente"));
} else {
    echo json_encode(array("message" => "Error al insertar datos"));
}

$stmt->close();
$conn->close();
?>