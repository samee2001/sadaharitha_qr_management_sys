<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow-sm mb-4 px-3 text-black"
    style="opacity: 0.9;">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php" target="_parent" rel="noopener">
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
        <div class="collapse navbar-collapse justify-content-center" style="margin-left: 350px;" id="navbarNav">
            <ul class="navbar-nav ms-lg-5">
                <?php if ($_SESSION['handle_csv_privillages'] >= 20): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php" target="_parent" rel="noopener">Handle CSV</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['handle_csv_privillages'] < 20): ?>
                    <li class="nav-item">
                        <a class="nav-link disabled" <?php echo $_SESSION['handle_csv_privillages'] < 20 ? 'aria-disabled="true"' : 'href="#" target="_parent" rel="noopener"'; ?>>
                            Handle CSV
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['gen_qr_privillages'] > 0): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="generate_pdf.php" target="_parent" rel="noopener">Generate QR Codes</a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['gen_qr_privillages'] == 0): ?>
                    <li class="nav-item">
                        <a class="nav-link disabled" <?php echo $_SESSION['gen_qr_privillages'] < 20 ? 'aria-disabled="true"' : 'href="#" target="_parent" rel="noopener"'; ?>>
                            Generate QR Codes
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['qr_details_privillages'] < 10): ?>
                    <li class="nav-item">
                        <a class="nav-link disabled" <?php echo $_SESSION['qr_details_privillages'] < 10 ? 'aria-disabled="true"' : 'href="#" target="_parent" rel="noopener"'; ?>>
                            QR Details
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['qr_details_privillages'] >= 10): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="plantation_management.php" target="_parent" rel="noopener">QR Details</a>
                    </li>
                <?php endif; ?>


                <?php if ($_SESSION['estate_privillages'] < 20): ?>
                    <li class="nav-item">
                        <a class="nav-link disabled" <?php echo $_SESSION['estate_privillages'] < 10 ? 'aria-disabled="true"' : 'href="#" target="_parent" rel="noopener"'; ?>>
                            Estate Management
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['estate_privillages'] >= 20): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="estate_details_page.php" target="_parent" rel="noopener">Estate
                            Management</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="about.php" target="_parent" rel="noopener">About</a>
                </li>
            </ul>
            <!-- Static logout button -->
            <p><?php echo $_SESSION['email']; ?></p>
            <div class="ms-auto p-2">
                <a href="logout.php" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>