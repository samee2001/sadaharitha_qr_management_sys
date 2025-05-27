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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Cards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body style="background-image: url('sdh_bg.png'); background-size: cover;">
    <?php include 'components/navbar.php'; ?>
    <div class="container mt-5">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" style="margin-top: 150px;">

            <!-- Handle CSV Card -->
            <?php if ($_SESSION['role'] === "user"): ?>
                <!-- Disabled Card for User Role -->
                <div class="col">
                    <a href="index.php" class="card text-white bg-primary text-decoration-none"
                        style="pointer-events: none; opacity: 0.5;" title="This option is disabled for users">
                        <div class="card-body text-center">
                            <div class="icon fs-1 mb-3"><i class="fas fa-book"></i></div>
                            <h5 class="card-title">Handle-CSV</h5>
                            <div class="card-footer text-center">
                                <small>More Details</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php elseif ($_SESSION['role'] === "admin"): ?>
                <!-- Active Card for Admin Role -->
                <div class="col">
                    <a href="index.php" class="card text-white bg-primary text-decoration-none">
                        <div class="card-body text-center">
                            <div class="icon fs-1 mb-3"><i class="fas fa-book"></i></div>
                            <h5 class="card-title">Handle-CSV</h5>
                            <div class="card-footer text-center">
                                <small>More Details</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>


            <!-- Generate PDF Card -->

            <div class="col">
                <a href="generate_pdf.php" class="card text-white bg-success text-decoration-none">
                    <div class="card-body text-center">
                        <div class="icon fs-1 mb-3"><i class="bi bi-file-pdf"></i></div>
                        <h5 class="card-title">Generate PDF</h5>
                        <div class="card-footer text-center">
                            <small>More Details</small>
                        </div>
                    </div>
                </a>
            </div>


            <!-- QR Details Card -->
            <div class="col">
                <a href="plantation_management.php" class="card text-white bg-warning text-decoration-none">
                    <div class="card-body text-center">
                        <div class="icon fs-1 mb-3"><i class="bi bi-files"></i></div>
                        <h5 class="card-title">QR Details</h5>
                        <div class="card-footer text-center">
                            <small>More Details</small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Estate Management Card -->
            <div class="col">
                <a href="estate_details_page.php" class="card text-white bg-info text-decoration-none">
                    <div class="card-body text-center">
                        <div class="icon fs-1 mb-3"><i class="bi bi-geo-alt"></i></div>
                        <h5 class="card-title">Estate Management</h5>
                        <div class="card-footer text-center">
                            <small>More Details</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <?php include 'components/footer.php'; ?>
</body>

</html>