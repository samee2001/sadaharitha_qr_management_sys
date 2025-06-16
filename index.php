<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: logIn.php');
    exit();
}

include('functions/handle_csv.php');
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

$tableName = 'plant_data';
$uploadSuccess = false;
$errorMessage = ''; // Initialize error message variable

if (isset($_POST['upload'])) {
    $uploadResult = processCSVUpload(
        $mysqli,
        $_FILES['csvfile'],
        $tableName,
    );

    if ($uploadResult['success']) {
        // Show success message (you already have this)
        $uploadSuccess = true;
        $_SESSION['tableName'] = $tableName;
    } else {
        // Store error message for rendering later
        $errorMessage = $uploadResult['error'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Handle CSV</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <div class="container my-5">
        <div class="text-center mb-4">
            <br>
            <h1 class="fw-bold text-success">Sadaharitha QR Code Management System</h1>
            <p class="text-muted fw-semibold text-success">Upload data and generate QR codes in one click</p>
        </div>
        <?php if ($uploadSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                ✅ CSV file uploaded, processed and Duplicates Handled successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                <?= htmlspecialchars($errorMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="card p-4 mx-auto " style="max-width: 700px;">
            <form method="post" enctype="multipart/form-data" id="myForm">
                <h4 class="mb-3">1️⃣ Upload CSV File</h4>
                <div class="mb-3">
                    <label for="csvfile" class="form-label">Choose CSV File</label>
                    <input type="file" class="form-control" id="csvfile" name="csvfile" accept=".csv">
                    <br>
                </div>
                <div class="text-center"><button type="submit" name="upload"
                        class="btn btn-success mb-4 btn-custom ">Upload CSV</button>
                </div>
                <hr>
            </form>
        </div>
    </div>
    <?php include 'components/footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>