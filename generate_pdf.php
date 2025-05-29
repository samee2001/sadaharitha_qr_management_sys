<?php

session_start();

if (!isset($_SESSION['email'])) {
    header("Location: logIn.php");
    exit();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'qrcode_db'); // Your database name

// Include libraries
require('fpdf186/fpdf.php');
require('phpqrcode/qrlib.php');
require_once 'functions/qr_pdf_generator.php';
// Create database connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$generateSucess = false;
// Check if the form is submitted for PDF generation
if (isset($_POST['generate'])) {
    $params = [
        'start' => (int) ($_POST['rangeStart'] ?? 1),
        'step' => (int) ($_POST['rangeStep'] ?? 1),
        'width' => (int) ($_POST['customWidth'] ?? 305),
        'height' => (int) ($_POST['customHeight'] ?? 336),
        'color' => $_POST['cellColorSelect'] ?? 'white'
    ];
    try {
        $pdfContent = generateQRPDF($mysqli, $tableName = "plant_data", $params);
        $generateSucess = true;
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="QR_Codes.pdf"');
        echo $pdfContent;
        header("Location: plantation_management.php");
        exit;
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <?php if ($generateSucess): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ PDF generated and processed successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <!-- Start of the new form -->
    <div class="card p-4 mx-auto " style="max-width: 700px; margin-top: 100px;">
        <form method="post" enctype="multipart/form-data" id="myForm">
            <hr>
            <h4 class="mb-3">2️⃣ PDF Dimensions</h4>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="customWidth" class="form-label">Width (mm)</label>
                    <input type="number" class="form-control" id="customWidth" name="customWidth" value="305" min="100"
                        max="1000">
                </div>
                <div class="col-md-6">
                    <label for="customHeight" class="form-label">Height (mm)</label>
                    <input type="number" class="form-control" id="customHeight" name="customHeight" value="336"
                        min="100" max="1000">
                </div>
            </div>
            <br>
            <label for="cellColorSelect" class="form-label"> Background Color</label>
            <select name="cellColorSelect" class="form-select">
                <?php
                $result = $mysqli->query("SELECT color_name, color_code FROM colors");
                while ($row = $result->fetch_assoc()):
                    $normalized = strtolower(trim($row['color_name']));
                    $rgb = explode(',', $row['color_code']);
                    ?>
                    <option value="<?= htmlspecialchars($normalized) ?>"
                        style="background-color: rgb(<?= $row['color_code'] ?>)">
                        <?= ucfirst(htmlspecialchars($normalized)) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <hr>
            <h4 class="mb-3">3️⃣QR Code Range</h4>
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="rangeStart" class="form-label">Start</label>
                    <input type="number" class="form-control" id="rangeStart" name="rangeStart" required>
                </div>
                <div class="col-md-6">
                    <label for="rangeStep" class="form-label">Step</label>
                    <input type="number" class="form-control" id="rangeStep" name="rangeStep" max="5000" min="1"
                        required>
                </div>
            </div>
            <div class="form-text">Specify the starting row and how many QR codes to generate (step, max 5000)</div>
            <hr>
            <div class="text-center">
                <button type="submit" name="generate" class="btn btn-primary btn-custom">Generate PDF</button>
            </div>

        </form>
    </div>
    <br>
    <?php include 'components/footer.php'; ?>
    <script src="capture_datetime.js"></script>
    <!-- End of the new form -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>