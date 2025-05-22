<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow-sm mb-4 px-3">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="https://www.sadaharitha.com" target="_blank"
            rel="noopener">
            <img src="sadaharitha_logo_black.png" alt="Sadaharitha Logo" class="me-2"
                style="height: 40px; width: auto;">
            Sadaharitha
        </a>
        <!-- Toggler for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar content -->
        <?php
        if (
            isset($_SESSION['handle_csv_privillages']) && $_SESSION['handle_csv_privillages'] > 20 && $_SESSION['handle_csv_privillages'] <= 30 &&
            isset($_SESSION['gen_qr_privillages']) && $_SESSION['gen_qr_privillages'] > 20 && $_SESSION['gen_qr_privillages'] <= 30 &&
            isset($_SESSION['qr_details_privillages']) && $_SESSION['qr_details_privillages'] > 20 && $_SESSION['qr_details_privillages'] <= 30 &&
            isset($_SESSION['estate_privillages']) && $_SESSION['estate_privillages'] > 20 && $_SESSION['estate_privillages'] <= 30
        ): ?>
            <!-- Full access navbar for Admin (20-30 range) -->
            <div class="collapse navbar-collapse" style="margin-left: 350px;" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item ">
                        <a class="nav-link" aria-current="page" href="index.php" target="_blank">Handle CSV</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="generate_pdf.php" target="_blank">Generate QR
                            Codes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="plantation_management.php" target="_blank">QR Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="estate_details_page.php" target="_blank">Estate Management</a>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php" target="_blank">About</a>
                    </li>
                </ul>
                <!-- Logout button -->
                <?php if (isset($_SESSION['email'])): ?>
                    <div class="ms-auto p-2">
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (
            isset($_SESSION['handle_csv_privillages']) && $_SESSION['handle_csv_privillages'] > 10 && $_SESSION['handle_csv_privillages'] <= 20 &&
            isset($_SESSION['gen_qr_privillages']) && $_SESSION['gen_qr_privillages'] > 10 && $_SESSION['gen_qr_privillages'] <= 20 &&
            isset($_SESSION['qr_details_privillages']) && $_SESSION['qr_details_privillages'] > 10 && $_SESSION['qr_details_privillages'] <= 20 &&
            isset($_SESSION['estate_privillages']) && $_SESSION['estate_privillages'] > 10 && $_SESSION['estate_privillages'] <= 20
        ): ?>
            <!-- Partial access: Super User (10-20 range, Handle CSV, Generate QR Codes, QR Details) -->
            <div class="collapse navbar-collapse " style="margin-left: 450px;" id=" navbarNav">
                <ul class="navbar-nav ms-lg-5">
                    <li class="nav-item">
                        <a class="nav-link" href="generate_pdf.php" target="_blank">Generate QR Codes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="plantation_management.php" target="_blank">QR Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php" target="_blank">About</a>
                    </li>
                </ul>
                <!-- Logout button -->
                <?php if (isset($_SESSION['email'])): ?>
                    <div class="ms-auto p-2">
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (
            isset($_SESSION['handle_csv_privillages']) && $_SESSION['handle_csv_privillages'] >= 0 && $_SESSION['handle_csv_privillages'] <= 10 &&
            isset($_SESSION['gen_qr_privillages']) && $_SESSION['gen_qr_privillages'] >= 0 && $_SESSION['gen_qr_privillages'] <= 10 &&
            isset($_SESSION['qr_details_privillages']) && $_SESSION['qr_details_privillages'] >= 0 && $_SESSION['qr_details_privillages'] <= 10 &&
            isset($_SESSION['estate_privillages']) && $_SESSION['estate_privillages'] >= 0 && $_SESSION['estate_privillages'] <= 10
        ): ?>
            <!-- Limited access: Basic User (0-10 range, Generate QR Codes and About only) -->
            <div class="collapse navbar-collapse " style="margin-left: 500px;" id="navbarNav">
                <ul class="navbar-nav ms-lg-5">
                    <li class="nav-item">
                        <a class="nav-link" href="generate_pdf.php" target="_blank">Generate QR Codes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php" target="_blank">About</a>
                    </li>
                </ul>
                <!-- Logout button -->
                <?php if (isset($_SESSION['email'])): ?>
                    <div class="ms-auto p-2">
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</nav>