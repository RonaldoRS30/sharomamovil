<?php
session_start();
error_reporting(0);
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
    header("location: ../../login.php");
    exit;
}

require_once('../../TCPDF/tcpdf.php');
include("../../config/db.php");
include("../../config/conexion.php");

// Function to get invoice status
function get_estado_factura($estado) {
    switch ($estado) {
        case 1:
            return 'Pagado';
        case 0:
            return 'Cancelada';
        case 2:
            return 'Aprobado';
        default:
            return 'Desconocido';
    }
}

// Custom PDF class to override header and footer
class CustomPDF extends TCPDF {
    public function Header() {
        // Logo (replace with your company logo path)
        $image_file = '../../assets/img/logo.png';
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 40, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        $titulo = !empty($id_cliente) ? "Reporte de Facturas - Cliente: $id_cliente" : "Reporte de Facturas";
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 15, $titulo, 0, 1, 'R', false, '', 0, false, 'M', 'M');
        
        // Subheader with date
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, 'Generado: ' . date('Y-m-d H:i:s'), 0, 1, 'R', false, '', 0, false, 'T', 'M');
        
        // Line
        $this->Line(15, 25, $this->getPageWidth() - 15, 25);
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Filter parameters (previous code remains the same)
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$estado_doc = isset($_GET['estado_doc']) ? $_GET['estado_doc'] : '';
$encargado = isset($_GET['encargado']) ? $_GET['encargado'] : '';
$pais = isset($_GET['pais']) ? $_GET['pais'] : '';
$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';
$nombre_cliente = isset($_GET['nombre_cliente']) ? $_GET['nombre_cliente'] : '';

$sql_query = "SELECT 
    f.numero_factura, 
    f.fecha_factura, 
    f.total_venta, 
    f.estado_factura, 
    c.nombre_cliente,
    c.num_cliente,  
    p.nombre AS pais,  
    CONCAT(u.firstname, ' ', u.lastname) AS encargado  
FROM 
    facturas f
JOIN 
    clientes c ON f.id_cliente = c.id_cliente 
JOIN 
    pais p ON c.pais = p.id
JOIN 
    users u ON f.id_vendedor = u.user_id
WHERE 
    1";


// Filters (previous code remains the same)
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $sql_query .= " AND f.fecha_factura BETWEEN '$fecha_inicio 00:00:00' AND '$fecha_fin 23:59:59'";
}

if ($estado_doc != '' && $estado_doc !== 'A') {
    $sql_query .= " AND f.estado_factura = '$estado_doc'";
}
if (!empty($encargado) && $encargado !== 'A') {
    $sql_query .= " AND f.id_vendedor = " . intval($encargado);
}

if (!empty($pais) && $pais !== 'A') {
    $sql_query .= " AND p.id = " . intval($pais);
}
if ($id_cliente != '') {
    $sql_query .= " AND f.id_cliente = '$id_cliente'";
}

if ($nombre_cliente != '') {
    $sql_query .= " AND c.nombre_cliente LIKE '%$nombre_cliente%'";
}

$sql_query .= " ORDER BY f.fecha_factura ASC";
$result = mysqli_query($con, $sql_query);

// Initialize PDF
$pdf = new CustomPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Facturación');
$pdf->SetTitle('Reporte de Facturas');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(15, 30, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(20);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add page
$pdf->AddPage();

// Prepare table
$pdf->SetFont('helvetica', '', 9);

// Initialize state totals
$estado_totales = [
    'Pagado' => 0,
    'Cancelada' => 0,
    'Aprobado' => 0,
];

$html = '
<style>
    table.report {
        border-collapse: collapse;
        width: 100%;
        font-family: helvetica, sans-serif;
    }
    table.report th {
        background-color: #f2f2f2;
        color: #333;
        font-weight: bold;
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    table.report td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    table.report tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    table.report tr:hover {
        background-color: #f5f5f5;
    }
    .state-summary {
        background-color: #f2f2f2;
        font-weight: bold;
    }
</style>
<table class="report">
    <thead>
        <tr>
            <th>Número Factura</th>
            <th>Fecha</th>
            <th>Total Venta</th>
            <th>Estado</th>
            <th>Cliente</th>
            <th>Número Cliente</th>
            <th>País</th>
            <th>Encargado</th>
        </tr>
    </thead>
    <tbody>';

$total_ventas = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $total_ventas += $row['total_venta'];
    $estado = get_estado_factura($row['estado_factura']);
    $estado_totales[$estado] += $row['total_venta'];
    
    $html .= '<tr>
        <td>' . htmlspecialchars($row['numero_factura']) . '</td>
        <td>' . htmlspecialchars($row['fecha_factura']) . '</td>
        <td>$' . number_format($row['total_venta'], 2) . '</td>
        <td>' . htmlspecialchars($estado) . '</td>
        <td>' . htmlspecialchars($row['nombre_cliente']) . '</td>
        <td>' . htmlspecialchars($row['num_cliente']) . '</td>
        <td>' . htmlspecialchars($row['pais']) . '</td>
        <td>' . htmlspecialchars($row['encargado']) . '</td>
    </tr>';
}
$html .= '</tbody>';
$html .= '<tfoot>';
foreach ($estado_totales as $estado => $total) {
    if ($total > 0) {
        $html .= '
        <tr class="state-summary">
            <td colspan="2" style="text-align:right;"><strong>Total ' . htmlspecialchars($estado) . ':</strong></td>
            <td><strong>$' . number_format($total, 2) . '</strong></td>
            <td colspan="5"></td>
        </tr>';
    }
}
$html .= '</tfoot></table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('reporte_facturas.pdf', 'I');
?>