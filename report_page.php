<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: logIn.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body style="background-image: url('sdh_bg_2.png');">
    <div>
        <?php include 'components/navbar.php'; ?>
    </div>
    <div class="d-flex justify-content-center align-items-center" style="min-height: 100vh; ">
        <?php include 'components/report.php'; ?>
    </div>
    <div>
        <?php include 'components/footer.php'; ?>
    </div>
</body>
</html>