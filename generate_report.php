<?php
require('libs/fpdf.php');
$conn = new mysqli("localhost", "root", "", "lost_and_found_db");

// Fetch lost item report data
$result = $conn->query("SELECT item_name, last_seen_location, date_time FROM lost_items");

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Lost Items Report', 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Item Name', 1);
$pdf->Cell(70, 10, 'Location', 1);
$pdf->Cell(50, 10, 'Date/Time', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(60, 10, $row['item_name'], 1);
    $pdf->Cell(70, 10, $row['last_seen_location'], 1);
    $pdf->Cell(50, 10, $row['date_time'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'lost_items_report.pdf');
?>
