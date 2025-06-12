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
class DetailedInvoiceReport extends TCPDF {
    private $reportTitle;
    private $reportPeriod;

    public function __construct($reportTitle, $reportPeriod) {
        parent::__construct('L', 'mm', 'A4', true, 'UTF-8', false);
        $this->reportTitle = $reportTitle;
        $this->reportPeriod = $reportPeriod;
    }

    public function Header() {
        // Logo (replace with your company logo path)
        $image_file = '../../assets/img/logo.png';
        if (file_exists($image_file)) {
            $this->Image($image_file, 15, 10, 40, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(50, 50, 50);
        $this->Cell(0, 15, $this->reportTitle, 0, 1, 'R', false, '', 0, false, 'M', 'M');
        
        // Report Period
        if (!empty($this->reportPeriod)) {
            $this->SetFont('helvetica', '', 10);
            $this->SetTextColor(100, 100, 100);
            $this->Cell(0, 10, $this->reportPeriod, 0, 1, 'R', false, '', 0, false, 'T', 'M');
        }
        
        // Subheader with date
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, 'Generado: ' . date('Y-m-d H:i:s'), 0, 1, 'R', false, '', 0, false, 'T', 'M');
        
        // Line
        $this->Line(15, 35, $this->getPageWidth() - 15, 35);
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Get filter parameters
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$pais = isset($_GET['pais']) ? $_GET['pais'] : '';
$encargado = isset($_GET['encargado']) ? $_GET['encargado'] : '';
$estado_doc = isset($_GET['estado_doc']) ? $_GET['estado_doc'] : '';
$id_cliente = isset($_GET['id_cliente']) ? $_GET['id_cliente'] : '';
$nombre_cliente = isset($_GET['nombre_cliente']) ? $_GET['nombre_cliente'] : '';

// Prepare date range for display and query
$fecha_inicio_query = empty($fecha_inicio) ? null : $fecha_inicio . " 00:00:00";
$fecha_fin_query = empty($fecha_fin) ? null : $fecha_fin . " 23:59:59";
$report_period = (!empty($fecha_inicio) && !empty($fecha_fin)) 
    ? "Período: $fecha_inicio - $fecha_fin" 
    : "Todos los Períodos";

// Construct SQL Query
$sql_query = "SELECT 
    c.nombre_cliente,
    c.num_cliente,
    p.nombre AS pais,
    CONCAT(u.firstname, ' ', u.lastname) AS encargado,
    f.numero_factura, 
    f.fecha_factura, 
    f.total_venta, 
    f.estado_factura
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

// Apply filters
if ($fecha_inicio_query && $fecha_fin_query) {
    $sql_query .= " AND f.fecha_factura BETWEEN '$fecha_inicio_query' AND '$fecha_fin_query'";
}

if ($estado_doc !== '' && $estado_doc !== 'A') {
    $sql_query .= " AND f.estado_factura = '" . intval($estado_doc) . "'";
}

if ($id_cliente != '') {
    $sql_query .= " AND f.id_cliente = '$id_cliente'";
}

if ($nombre_cliente != '') {
    $sql_query .= " AND c.nombre_cliente LIKE '%$nombre_cliente%'";
}

if (!empty($pais)) {
    $sql_query .= " AND p.id = " . intval($pais);
}

if (!empty($encargado)) {
    $sql_query .= " AND f.id_vendedor = " . intval($encargado);
}

$sql_query .= " ORDER BY c.nombre_cliente, f.fecha_factura";

$result = mysqli_query($con, $sql_query);

// Initialize PDF
// Título por defecto
$report_title = 'Reporte Detallado de Facturas por Cliente';

// Si se ha proporcionado un id_cliente
if (!empty($id_cliente)) {
    // Consulta para obtener el nombre del cliente
    $query = "SELECT nombre_cliente FROM clientes WHERE id_cliente = ?";
    $stmt = $con->prepare($query);  // Usamos la conexión existente
    $stmt->bind_param('s', $id_cliente);  // 's' indica que el parámetro es una cadena
    $stmt->execute();
    $stmt->bind_result($nombre_cliente);
    $stmt->fetch();
    $stmt->close();

    // Si encontramos un cliente, actualizamos el título
    if (!empty($nombre_cliente)) {
        $report_title = 'Reporte Detallado de Facturas - ' . $nombre_cliente;
    } else {
        $report_title = 'Reporte Detallado de Facturas - Cliente No Encontrado';
    }

    // Si hay un nombre de cliente proporcionado, se aplica el filtro en la consulta
    if ($nombre_cliente != '') {
        $sql_query .= " AND c.nombre_cliente LIKE '%$nombre_cliente%'";
    }
}

// Crear el reporte PDF con el título adecuado
$pdf = new DetailedInvoiceReport(
    $report_title,  // Título con el nombre del cliente
    $report_period  // El período de reporte
);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Facturación');
$pdf->SetTitle('Reporte Detallado de Facturas');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(15, 40, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(20);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add first page
$pdf->AddPage();

// Prepare variables
$total_general = 0;

// Temporary array to store rows grouped by client
$client_rows = [];
$current_group_client = null;

// First, group rows by client
while ($row = mysqli_fetch_assoc($result)) {
    if ($current_group_client !== $row['nombre_cliente']) {
        $current_group_client = $row['nombre_cliente'];
        $client_rows[$current_group_client] = [];
    }
    $client_rows[$current_group_client][] = $row;
}

// Table header
$html = '
<style>
    table.report {
        border-collapse: collapse;
        width: 100%;
        font-family: helvetica, sans-serif;
    }
    table.report th {
        background-color: #2h32ds;
        color: white;
        font-weight: bold;
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    table.report td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .client-header {
        background-color: #F1F8E9;
        font-weight: bold;
        text-align: center;
    }
    .paid { background-color: #E8F5E9; }
    .canceled { background-color: #FFEBEE; }
    .approved { background-color: #E3F2FD; }
    .total-row {
        background-color: #E0E0E0;
        font-weight: bold;
    }
</style>
<table class="report">';

// Add table headers
$html .= '<thead>
    <tr>
        <th>DNI/RUC</th>
        <th>País</th>
        <th>Encargado</th>
        <th>Número Factura</th>
        <th>Fecha Factura</th>
        <th>Estado</th>
        <th>Total Venta</th>
    </tr>
</thead>
<tbody>';

// No results handling
if (empty($client_rows)) {
    $html .= '<tr><td colspan="8" style="text-align:center;">No se encontraron resultados</td></tr>';
} else {
    // Iterate through grouped client rows
    foreach ($client_rows as $client_name => $client_invoices) {
        // Client header
        $html .= '<tr>
            <td colspan="8" class="client-header">' . htmlspecialchars($client_name) . '</td>
        </tr>';

        $client_total = 0;

        // Process each invoice for the client
        foreach ($client_invoices as $row) {
            // Determina la clase de estado para la fila
            $status_class = '';
            $estado = get_estado_factura($row['estado_factura']);
            switch ($estado) {
                case 'Pagado':
                    $status_class = 'paid';
                    break;
                case 'Cancelada':
                    $status_class = 'canceled';
                    break;
                case 'Aprobado':
                    $status_class = 'approved';
                    break;
            }

            // Agrega una fila de factura
            $html .= '<tr class="' . $status_class . '">
                <td>' . htmlspecialchars($row['num_cliente']) . '</td>
                <td>' . htmlspecialchars($row['pais']) . '</td>
                <td>' . htmlspecialchars($row['encargado']) . '</td>
                <td>' . htmlspecialchars($row['numero_factura']) . '</td>
                <td>' . htmlspecialchars($row['fecha_factura']) . '</td>
                <td>' . $estado . '</td>
                <td>$' . number_format($row['total_venta'], 2) . '</td>
            </tr>';

            // Acumula los totales del cliente actual y general (solo pagado o aprobado)
            if ($estado === 'Pagado' || $estado === 'Aprobado') {
                $client_total += $row['total_venta'];
                $total_general += $row['total_venta'];
            }
        }

        // Total for the current client
        $html .= '<tr class="total-row">
            <td colspan="6" style="text-align:right;">Total Cliente</td>
            <td>$' . number_format($client_total, 2) . '</td>
        </tr>';
    }

    // Total general
    $html .= '<tr class="total-row" style="font-size: 1.1em;">
        <td colspan="6" style="text-align:right;">Total General</td>
        <td>$' . number_format($total_general, 2) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// Write HTML to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('reporte_detallado_facturas.pdf', 'I');
exit;
?>