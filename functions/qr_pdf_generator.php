<?php
require_once('fpdf186/fpdf.php');
require_once('phpqrcode/qrlib.php');

function generateQRPDF($mysqli, $tableName, $params)
{
    // Validate and extract parameters
    $start = isset($params['start']) ? (int) $params['start'] : 1;
    $step = isset($params['step']) ? (int) $params['step'] : 1;
    $width = isset($params['width']) ? (int) $params['width'] : 305;
    $height = isset($params['height']) ? (int) $params['height'] : 336;

    // Fetch color mapping from database
    function getColorMap($mysqli)
    {
        $colorMap = [];
        $result = $mysqli->query("SELECT color_name, color_code FROM colors");

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Normalize color name key
                $key = strtolower(trim($row['color_name']));

                // Validate and parse RGB values
                $rgbValues = explode(',', $row['color_code']);
                if (count($rgbValues) !== 3)
                    continue;

                $rgb = array_map('intval', $rgbValues);
                if (
                    $rgb[0] < 0 || $rgb[0] > 255 ||
                    $rgb[1] < 0 || $rgb[1] > 255 ||
                    $rgb[2] < 0 || $rgb[2] > 255
                ) {
                    continue;
                }

                $colorMap[$key] = $rgb;
            }
        }
        return $colorMap;
    }
    // Get selected color with fallback
    $colorMap = getColorMap($mysqli);
    $selectedColor = isset($_POST['cellColorSelect'])
        ? strtolower(trim($_POST['cellColorSelect']))
        : 'white';

    $cellColor = $colorMap[$selectedColor] ?? [255, 255, 255]; // Fallback to white
    $cellColorStr = implode(',', $cellColor); // Convert to string like "255,255,255"

    // Create QR code directory if not exists
    if (!file_exists('qrcodes')) {
        mkdir('qrcodes', 0777, true);
    }

    // Fetch data
    $offset = $start - 1;
    $limit = $step;
    $result = $mysqli->query("SELECT * FROM `$tableName` LIMIT $offset,$limit");
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $qrContent = isset($row['QR Code Details']) ? 'SPL ' . $row['QR Code Details'] : 'N/A';
        $plantNumber = isset($row['Plant Number']) ? $row['Plant Number'] : 'N/A';
        $filename = "qrcodes/{$row['id']}.png";
        QRcode::png($qrContent, $filename, QR_ECLEVEL_L, 10);

        // Insert into qr_pdf table
        $currentDateTime = date('Y-m-d H:i:s');
        $generatedBy = $_SESSION['email']; // Current date and time
        $estateName = isset($_POST['qrManagementId']) ? mysqli_real_escape_string($mysqli, $_POST['qrManagementId']) : 'No Mentioned';
        $sql_insert = "INSERT INTO qr_details (qr_content, plant_number, cell_color, created_at, generated_by, estate_id ) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($mysqli, $sql_insert);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $qrContent, $plantNumber, $cellColorStr, $currentDateTime, $generatedBy, $estateName);
            if (!mysqli_stmt_execute($stmt)) {
                echo "Error inserting QR PDF record: " . mysqli_error($mysqli);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Prepare failed: " . mysqli_error($mysqli);
        }

        $items[] = [
            'file' => $filename,
            'text' => $qrContent,
            'plantNumber' => $plantNumber,
        ];
    }

    // Generate PDF
    $pdf = new FPDF('P', 'mm', [$width, $height]);
    $pdf->SetMargins(0, 0);
    $pdf->SetAutoPageBreak(false);

    $qrSize = 26;
    $cols = 3;
    $rows = 12;
    $cellWidth = $width / $cols;
    $cellHeight = $qrSize + 2;
    $lineColor = [50, 100, 50];

    $count = 0;
    foreach ($items as $item) {
        if ($count % ($cols * $rows) == 0) {
            $pdf->AddPage();
            $pdf->SetFillColor($cellColor[0], $cellColor[1], $cellColor[2]);
            $pdf->Rect(0, 0, $width, $height, 'F');
        }
        $positionInPage = $count % ($cols * $rows);
        $col = $positionInPage % $cols;
        $row = floor($positionInPage / $cols);
        $x = $col * $cellWidth;
        $y = $row * $cellHeight;

        // Cell background
        $pdf->SetFillColor($cellColor[0], $cellColor[1], $cellColor[2]);
        $pdf->Rect($x, $y, $cellWidth, $cellHeight, 'F');

        // Dividing line
        $pdf->SetDrawColor($lineColor[0], $lineColor[1], $lineColor[2]);
        $lineX = $x + $cellWidth - $qrSize - 23;
        $pdf->Line($lineX, $y, $lineX, $y + $cellHeight);

        // Cell border
        $pdf->Rect($x, $y, $cellWidth, $cellHeight);

        // QR code (right-aligned)
        $qrX = $x + $cellWidth - $qrSize - 2;
        $pdf->Image($item['file'], $qrX, $y + 1, $qrSize, $qrSize);

        // Description (left side)
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY($x + 2, $y + 10);
        $textWidth = $cellWidth - $qrSize - 6;
        $pdf->MultiCell($textWidth, 5, $item['plantNumber'], 0, 'L');
        $yOffset = $pdf->GetY() + 1;

        // Second text: Sadaharitha Plantations Limited
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->SetXY($x + 2, $yOffset);
        $logoX = $x + 2;
        $logoY = $yOffset;
        $logoWidth = 5;
        $logoHeight = 5;
        $pdf->Image('sadaharitha_logo_black.png', $logoX, $logoY, $logoWidth, $logoHeight);
        $textX = $logoX + $logoWidth;
        $pdf->SetXY($textX, $yOffset);
        $pdf->MultiCell($textWidth, 5, "Sadaharitha Plantations Limited", 0, 'L');

        $count++;
    }
    // Cleanup QR PNGs
    foreach ($items as $item) {
        if (file_exists($item['file']))
            unlink($item['file']);
    }
    // Return PDF as string
    return $pdf->Output('S', 'QR_Codes.pdf');
}
?>