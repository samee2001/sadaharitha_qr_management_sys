<?php
require_once('fpdf186/fpdf.php');
require_once('phpqrcode/qrlib.php');

function generateQRPDF($mysqli, $params)
{ // Validate and extract parameters
    $start = isset($params['start']) ? (int) $params['start'] : 1;
    $step = isset($params['step']) ? (int) $params['step'] : 1;
    $width = isset($params['width']) ? (int) $params['width'] : 305;
    $height = isset($params['height']) ? (int) $params['height'] : 336;
    $selectedColor = isset($params['cellColorSelect']) ? trim(str_replace(' ', '', $params['cellColorSelect'])) : '255,255,255';
    $rgb = explode(',', $selectedColor);
    if (count($rgb) === 3) {
        $cellColor = array_map('intval', $rgb);
        foreach ($cellColor as $c) {
            if ($c < 0 || $c > 255) {
                $cellColor = [255,255,255];
                break;
            }
        }
    } else {
        $cellColor = [255,255,255];
    }

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

   

    // Create QR code directory if not exists
    if (!file_exists('qrcodes')) {
        mkdir('qrcodes', 0777, true);
    }
    // Fetch data
    $startPlantNumber = $start; // Starting Plant_Number (integer)
    $limit = $step;
    $tableName = 'plant_data';

    $stmt = $mysqli->prepare("SELECT plant_number, qr_code_details FROM `$tableName` WHERE plant_number >= ? ORDER BY plant_number LIMIT ?");
    $stmt->bind_param("ii", $startPlantNumber, $limit); // 'i' for both
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];

    while ($row = $result->fetch_assoc()) {
        $qrContent = isset($row['qr_code_details']) ? 'SPL ' . $row['qr_code_details'] : 'N/A';
        $plantNumber = 'Plant No. ' . (isset($row['plant_number']) ? $row['plant_number'] : 'N/A');
        $filename = "qrcodes/{$row['plant_number']}.png";
        QRcode::png($qrContent, $filename, QR_ECLEVEL_L, 10);

        $currentDateTime = date('Y-m-d H:i:s');
        $generatedBy = $_SESSION['email'];
        $batchId = isset($_POST['qrManagementId']) ? $_POST['qrManagementId'] : 'No Mentioned';
        $sql_insert = "INSERT INTO qr_details (qr_content, plant_number, created_at, generated_by, batch_id) VALUES ( ?, ?, ?, ?, ?)";
        $stmt_insert = $mysqli->prepare($sql_insert);
        if ($stmt_insert) {
            mysqli_stmt_bind_param($stmt_insert, "sssss", $qrContent, $plantNumber,  $currentDateTime, $generatedBy, $batchId);
            if (!mysqli_stmt_execute($stmt_insert)) {
                echo "Error inserting QR PDF record: " . $mysqli->error;
            }
            mysqli_stmt_close($stmt_insert);
        } else {
            echo "Prepare failed: " . $mysqli->error;
        }

        $items[] = [
            'file' => $filename,
            'text' => $qrContent,
            'plantNumber' => $plantNumber,
        ];
    }

    $stmt->close();

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
