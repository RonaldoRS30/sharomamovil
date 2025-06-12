<?php

$servername = "localhost"; // o la dirección IP del servidor MySQL
$username = "sistemadefactura_sharoma"; // el nombre de usuario de la base de datos
$password = "sistemadefactura_sharoma"; // la contraseña de la base de datos
$dbname = "sistemadefactura_sharoma"; // el nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta SQL
$sql = "SELECT * FROM cji_almacen";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $data = array();
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data); // Enviar respuesta en formato JSON
} else {
    echo json_encode(array("message" => "No se encontraron datos"));
}

$conn->close();


?>