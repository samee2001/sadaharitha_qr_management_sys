<?php
session_start();
include 'connect.php'; // Include your database connection file
if (!isset($_SESSION['email'])) {
    header("Location: logIn.php");
    exit();
}

// // Include libraries
// require('fpdf186/fpdf.php');
// require('phpqrcode/qrlib.php');
// require_once 'functions/qr_pdf_generator.php';

// // Database Configuration
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'qrcode_db'); // Your database name

// // Create database connection
// $conn = new$conn(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

$generateSuccess = false;
// Check if the form is submitted for PDF generation
if (isset($_POST['generate'])) {
    $params = [
        'start' => (int) ($_POST['rangeStart'] ?? 1),
        'step' => (int) ($_POST['rangeStep'] ?? 1),
        'width' => (int) ($_POST['customWidth'] ?? 305),
        'height' => (int) ($_POST['customHeight'] ?? 336),
        'cellColorSelect' => $_POST['cellColorSelect'] ?? $_POST['cellColorSelectHidden'] ?? '255,255,255'
    ];
    try {
        $pdfContent = generateQRPDF($conn, $params);
        $generateSuccess = true;
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
    <?php if ($generateSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ PDF generated and processed successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <!-- Start of the new form -->
    <br><br><br>
    <div class="container my-4">
        <section id="qrFormSection">
            <div class="card p-3 mx-auto shadow-sm rounded-3 bg-light" style="max-width: 700px;">
                <form method="post" enctype="multipart/form-data" id="myForm" novalidate>
                    <h4 class="mb-2 text-success">PDF Dimensions</h4>
                    <div class="row mb-2 g-2">
                        <div class="col-md-6">
                            <label for="customWidth" class="form-label fw-medium">Width (mm)</label>
                            <input type="number" class="form-control rounded-3" id="customWidth" name="customWidth"
                                value="305" min="100" max="1000" placeholder="e.g., 305" required
                                aria-describedby="widthHelp">
                            <div id="widthHelp" class="form-text">Width of the PDF in millimeters (100–1000).</div>
                            <div id="customWidthError" class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="customHeight" class="form-label fw-medium">Height (mm)</label>
                            <input type="number" class="form-control rounded-3" id="customHeight" name="customHeight"
                                value="336" min="100" max="1000" placeholder="e.g., 336" required
                                aria-describedby="heightHelp">
                            <div id="heightHelp" class="form-text">Height of the PDF in millimeters (100–1000).</div>
                            <div id="customHeightError" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <hr class="my-2">
                    <h4 class="mb-2 text-success">Select Batch Record</h4>
                    <div class="mb-2">
                        <label for="qrManagementId" class="form-label fw-medium">Batch Record</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-database"></i></span>
                            <select name="qrManagementId" id="qrManagementId" class="form-select rounded-3" required>
                                <option value="" data-start="" data-step="" data-background-color="">Select a Record</option>
                                <?php
                                $result = $conn->query("SELECT batch_id,  start, step, background_color FROM qr_batch_details");
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $id = $row['batch_id'];
                                        $start = $row['start'];
                                        $step = $row['step'];
                                        //$estate = htmlspecialchars($row['estate']);
                                        $background_color = htmlspecialchars($row['background_color']);

                                ?>
                                        <option value="<?= $id ?>" data-start="<?= $start ?>" data-step="<?= $step ?>"
                                            data-background-color="<?= $background_color ?>">
                                            ID: <?= $id ?>
                                        </option>
                                <?php
                                    }
                                    $result->free();
                                } else {
                                    echo "<option value='' data-start='' data-step='' data-background-color=''>No records available</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div id="qrManagementIdHelp" class="form-text">Select a QR batch to generate the PDF.</div>
                    </div>
                    <div class="mb-2">
                        <label for="cellColorSelect" class="form-label fw-medium">Background Color</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-palette"></i></span>
                            <select name="cellColorSelect" id="cellColorSelect" class="form-select rounded-3" required>
                                <option value="">Select a Color</option>
                                <?php
                                $result = $conn->query("SELECT color_name, color_code FROM colors");
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $color_code = htmlspecialchars($row['color_code']);
                                        $color_name = ucfirst(htmlspecialchars(strtolower(trim($row['color_name']))));
                                ?>
                                        <option value="<?= $color_code ?>" style="background-color: rgb(<?= $color_code ?>)"
                                            data-color="<?= $color_code ?>">
                                            <?= $color_name ?>
                                        </option>
                                <?php
                                    }
                                    $result->free();
                                } else {
                                    echo "<option value=''>No colors available</option>";
                                }
                                ?>
                            </select>
                            <span id="colorPreview" class="badge rounded-pill ms-2"
                                style="background-color: rgb(255,255,255); width: 30px; height: 30px;"></span>
                        </div>
                        <div id="colorHelp" class="form-text">Color is locked to the batch’s background color.</div>
                    </div>
                    <!-- Add this hidden input just after the color select -->
                    <input type="hidden" name="cellColorSelect" id="cellColorSelectHidden">
                    <hr class="my-2">
                    <h4 class="mb-2 text-success">QR Code Range</h4>
                    <div class="row g-2 mb-2 align-items-end">
                        <div class="col-md-6">
                            <label for="rangeStart" class="form-label fw-medium">Start</label>
                            <input type="number" class="form-control rounded-3" id="rangeStart" name="rangeStart"
                                required readonly aria-describedby="rangeStartHelp">
                            <div id="rangeStartHelp" class="form-text">Starting row number (auto-filled).</div>
                        </div>
                        <div class="col-md-6">
                            <label for="rangeStep" class="form-label fw-medium">Step</label>
                            <input type="number" class="form-control rounded-3" id="rangeStep" name="rangeStep"
                                max="5000" min="1" required readonly aria-describedby="rangeStepHelp">
                            <div id="rangeStepHelp" class="form-text">Number of QR codes (max 5000, auto-filled).</div>
                            <div id="rangeStepError" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="generate" class="btn btn-primary w-100 shadow-sm rounded-3" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Generate PDF
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <br>
    <?php include 'components/footer.php'; ?>
    <script src="capture_datetime.js"></script>
    <script>
        // JavaScript to dynamically update rangeStart, rangeStep, and cellColorSelect based on selected QR Management Record
        document.getElementById('qrManagementId').addEventListener('change', function() {
            const select = this;
            const rangeStart = document.getElementById('rangeStart');
            const rangeStep = document.getElementById('rangeStep');
            const cellColorSelect = document.getElementById('cellColorSelect');
            const colorPreview = document.getElementById('colorPreview');
            const selectedOption = select.options[select.selectedIndex];

            // Update input fields with data attributes from the selected option
            rangeStart.value = selectedOption.getAttribute('data-start') || '';
            rangeStep.value = selectedOption.getAttribute('data-step') || '';
            const backgroundColor = selectedOption.getAttribute('data-background-color') || '';
            cellColorSelect.value = backgroundColor;
            colorPreview.style.backgroundColor = `rgb(${backgroundColor})`;

            // Always set the hidden input value
            document.getElementById('cellColorSelectHidden').value = backgroundColor;

            // Disable cellColorSelect if a valid batch is selected
            cellColorSelect.disabled = !!selectedOption.value;
        });

        // On page load, also set the hidden value in case a batch is pre-selected
        document.addEventListener('DOMContentLoaded', function() {
            const cellColorSelect = document.getElementById('cellColorSelect');
            document.getElementById('cellColorSelectHidden').value = cellColorSelect.value;
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>