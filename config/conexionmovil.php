<?php
$servername = "localhost"; // o la dirección IP del servidor MySQL
$username = "root"; // tu usuario
$password = ""; // tu contraseña
$dbname = "sistemadefactura_sharoma"; // el nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>