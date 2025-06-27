<?php
require_once('../fpdf186/fpdf.php');
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_from = $_POST['date_from'] ?? '';
    $date_to = $_POST['date_to'] ?? '';


    // If both dates are given, filter by date range; otherwise, select all
    if ($date_from && $date_to) {
        $stmt = $conn->prepare("
            SELECT e.estate_name, i.range_start, i.range_end, i.issued_at, i.issued_by
            FROM issued_estate i
            JOIN estate e ON i.estate_id = e.id
            WHERE DATE(i.issued_at) BETWEEN ? AND ?
            ORDER BY i.issued_at ASC
        ");
        $stmt->bind_param("ss", $date_from, $date_to);
    } else {
        $stmt = $conn->prepare("
            SELECT e.estate_name, i.range_start, i.range_end, i.issued_at, i.issued_by
            FROM issued_estate i
            JOIN estate e ON i.estate_id = e.id
            ORDER BY i.issued_at ASC
        ");
    }
    $stmt->execute();
    $result = $stmt->get_result();

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage('P', 'A4');

    // Set logo path and size
    $logoPath = '../sadaharitha_logo_black.png'; // Change to your actual logo path
    $logoWidth = 10; // width in mm
    $logoHeight = 10; // height in mm

    // Place image at the left margin, current Y
    $pdf->Image($logoPath, 62, $pdf->GetY(), $logoWidth, $logoHeight);

    // Move X to the right of the image with a little space
    $pdf->SetXY(10 + $logoWidth + 3, $pdf->GetY());
    // Title
    $pdf->SetFont('Arial', '', 15);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, 'Sadaharitha Plantations Limited', 0, 1, 'C');

    $pdf->SetFont('Arial', '', 13);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 6, 'Issued Estate QR Report', 0, 1, 'C');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);

    if ($date_from && $date_to) {
        $pdf->Cell(0, 6, "From: $date_from   To: $date_to", 0, 1, 'C');
    } else {
        $pdf->Cell(0, 6, "All Records", 0, 1, 'C');
    }

    $pdf->Ln(2);

    // Table header
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(22, 7, 'Start', 1, 0, 'C', true);
    $pdf->Cell(22, 7, 'End', 1, 0, 'C', true);
    $pdf->Cell(58, 7, 'Issued At', 1, 0, 'C', true);
    $pdf->Cell(35, 7, 'Issued By', 1, 0, 'C', true);
    $pdf->Cell(48, 7, 'Estate Name', 1, 1, 'C', true);

    // Table body
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0,0,0);
    $fill = false;
    $rowCount = 0;
    $maxRowsPerPage = 36; // Adjust as needed for your layout

    while ($row = $result->fetch_assoc()) {
        // Truncate estate name and issued by if too long
        $estateName = mb_strimwidth($row['estate_name'], 0, 32, '...');
        $issuedBy = mb_strimwidth($row['issued_by'], 0, 18, '...');

        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        $pdf->Cell(22, 6, $row['range_start'], 1, 0, 'C', $fill);
        $pdf->Cell(22, 6, $row['range_end'], 1, 0, 'C', $fill);
        $pdf->Cell(58, 6, $row['issued_at'], 1, 0, 'C', $fill);
        $pdf->Cell(35, 6, $issuedBy, 1, 0, 'L', $fill);
        $pdf->Cell(48, 6, $estateName, 1, 1, 'L', $fill);

        $fill = !$fill;
        $rowCount++;

        // Add page break and repeat header if needed
        if ($rowCount % $maxRowsPerPage == 0) {
            $pdf->AddPage();
            // Table header
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(40, 167, 69);
            $pdf->SetTextColor(255,255,255);
            $pdf->Cell(22, 7, 'Start', 1, 0, 'C', true);
            $pdf->Cell(22, 7, 'End', 1, 0, 'C', true);
            $pdf->Cell(58, 7, 'Issued At', 1, 0, 'C', true);
            $pdf->Cell(35, 7, 'Issued By', 1, 0, 'C', true);
            $pdf->Cell(48, 7, 'Estate Name', 1, 1, 'C', true);
        }
    }

    if ($result->num_rows == 0) {
        $pdf->Cell(185, 10, 'No records found.', 1, 1, 'C');
    }
    date_default_timezone_set('Asia/Colombo');
    $pdf->Ln(4);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell(0, 8, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'R');

    $pdf->Output('I', 'Estate_QR_Report.pdf');
    exit;
}
