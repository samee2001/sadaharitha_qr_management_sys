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
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body style="background-image: url('sdh_bg.png'); background-size: cover;">
    <?php include 'components/navbar.php'; ?>
    <div class="container mt-5">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" style="margin-top: 150px;">


            <!-- QR Details Card -->
            <?php if ($_SESSION['qr_details_privillages'] < 10): ?>
                <div class="col">
                    <a href="#" class="card text-white bg-primary text-decoration-none"
                        style="pointer-events: none; opacity: 0.75;">
                        <div class="card-body text-center">
                            <div class="icon fs-1 mb-3"><i class="bi bi-files"></i></div>
                            <h5 class="card-title">Create Batch</h5>
                            <div class="card-footer text-center">
                                <small>More Details</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['qr_details_privillages'] >= 10): ?>
                <div class="col">
                    <a href="plantation_management.php" class="card text-white bg-primary text-decoration-none">
                        <div class="card-body text-center">
                            <div class="icon fs-1 mb-3"><i class="bi bi-files"></i></div>
                            <h5 class="card-title">Create Batch</h5>
                            <div class="card-footer text-center">
                                <small>More Details</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Generate PDF Card -->
            <?php if ($_SESSION['gen_qr_privillages'] > 0): ?>
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
            <?php endif; ?>
            <?php if ($_SESSION['gen_qr_privillages'] == 0): ?>
                <div class="col">
                    <a href="#" class="card text-white bg-success text-decoration-none"
                        style="pointer-events: none; opacity: 0.75;">
                        <div class="card-body text-center">
                            <div class="icon fs-1 mb-3"><i class="bi bi-file-pdf"></i></div>
                            <h5 class="card-title">Generate PDF</h5>
                            <div class="card-footer text-center">
                                <small>More Details</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>


            <!-- Estate Management Card -->
            <?php if ($_SESSION['estate_privillages'] < 20): ?>
                <div class="col">
                    <a href="#" class="card text-white bg-primary text-decoration-none"
                        style="pointer-events: none; opacity: 0.75;">
                        <div class="card-body text-center">
                            <div class="icon fs-1 mb-3"><i class="bi bi-geo-alt"></i></div>
                            <h5 class="card-title">Estate Management</h5>
                            <div class="card-footer text-center">
                                <small>More Details</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
            <?php if ($_SESSION['estate_privillages'] >= 20): ?>
                <div class="col">
                    <a href="estate_details_page.php" class="card text-white bg-primary text-decoration-none">
                        <div class="card-body text-center">
                            <div class="icon fs-1 mb-3"><i class="bi bi-geo-alt"></i></div>
                            <h5 class="card-title">Estate Management</h5>
                            <div class="card-footer text-center">
                                <small>More Details</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
            <?php if ($_SESSION['user_level'] >= 20): ?>
                <div class="col">
                    <a href="manage_users.php" class="card text-white bg-success text-decoration-none">
                        <div class="card-body text-center">
                            <div class="icon fs-1 mb-3"><i class="bi bi-people"></i></div>
                            <h5 class="card-title">Manage Users</h5>
                            <div class="card-footer text-center">
                                <small>More Details</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
            <?php if ($_SESSION['user_level'] < 20): ?>
                <div class="col">
                    <a href="#" class="card text-white bg-success text-decoration-none"
                        style="pointer-events: none; opacity: 0.75;">
                        <div class="card-body text-center">
                            <div class="icon fs-1 mb-3"><i class="bi bi-people"></i></div>
                            <h5 class="card-title">Manage Users</h5>
                            <div class="card-footer text-center">
                                <small>More Details</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'components/footer.php'; ?>
</body>

</html>