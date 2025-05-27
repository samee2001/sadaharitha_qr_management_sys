<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Extract session details into an array
$sessionDetails = [];
$sessionKeys = ['handle_csv_privillages', 'gen_qr_privillages', 'qr_details_privillages', 'estate_privillages', 'email'];
foreach ($sessionKeys as $key) {
    if (isset($_SESSION[$key])) {
        $sessionDetails[$key] = $_SESSION[$key];
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow-sm mb-4 px-3 " style="opacity: 0.85; ">
    <div class="container-fluid ">
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
        // Render navbar if at least one relevant privilege is set
        if (
            isset($sessionDetails['handle_csv_privillages']) ||
            isset($sessionDetails['gen_qr_privillages']) ||
            isset($sessionDetails['qr_details_privillages']) ||
            isset($sessionDetails['estate_privillages'])
        ): ?>
            <div class="collapse navbar-collapse justify-content-center" style="margin-left: 350px; " id="navbarNav">
                <ul class="navbar-nav ms-lg-5" style="content-visibility: hidden;">
                    <?php
                    // Admin-level privillages (>20 and <=30)
                    if (isset($sessionDetails['handle_csv_privillages']) && $sessionDetails['handle_csv_privillages'] > 20 && $sessionDetails['handle_csv_privillages'] <= 30): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? ' active' : ''; ?>"
                                href="index.php" target="_parent" rel="noopener">Handle CSV</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($sessionDetails['gen_qr_privillages']) && $sessionDetails['gen_qr_privillages'] > 20 && $sessionDetails['gen_qr_privillages'] <= 30): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'generate_pdf.php' ? ' active' : ''; ?>"
                                href="generate_pdf.php" target="_parent" rel="noopener">Generate QR Codes</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($sessionDetails['qr_details_privillages']) && $sessionDetails['qr_details_privillages'] > 20 && $sessionDetails['qr_details_privillages'] <= 30): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'plantation_management.php' ? ' active' : ''; ?>"
                                href="plantation_management.php" target="_parent" rel="noopener">QR Details</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($sessionDetails['estate_privillages']) && $sessionDetails['estate_privillages'] > 20 && $sessionDetails['estate_privillages'] <= 30): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'estate_details_page.php' ? ' active' : ''; ?>"
                                href="estate_details_page.php" target="_parent" rel="noopener">Estate Management</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($sessionDetails['estate_privillages']) && $sessionDetails['estate_privillages'] > 20 && $sessionDetails['estate_privillages'] <= 30): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'estate_details_page.php' ? ' active' : ''; ?>"
                                href="about.php" target="_parent" rel="noopener">About</a>
                        </li>
                    <?php endif; ?>


                    <!-- Super User privillages (10-20) -->
                    <?php if (
                        (isset($sessionDetails['handle_csv_privillages']) && $sessionDetails['handle_csv_privillages'] > 10 && $sessionDetails['handle_csv_privillages'] <= 20) &&
                        !(isset($sessionDetails['handle_csv_privillages']) && $sessionDetails['handle_csv_privillages'] > 20 && $sessionDetails['handle_csv_privillages'] <= 30)
                    ): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? ' active' : ''; ?>"
                                href="index.php" target="_parent" rel="noopener">Handle CSV</a>
                        </li>
                    <?php endif; ?>
                    <?php if (
                        (isset($sessionDetails['gen_qr_privillages']) && $sessionDetails['gen_qr_privillages'] > 10 && $sessionDetails['gen_qr_privillages'] <= 20) &&
                        !(isset($sessionDetails['gen_qr_privillages']) && $sessionDetails['gen_qr_privillages'] > 20 && $sessionDetails['gen_qr_privillages'] <= 30)
                    ): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'generate_pdf.php' ? ' active' : ''; ?>"
                                href="generate_pdf.php" target="_parent" rel="noopener">Generate QR Codes</a>
                        </li>
                    <?php endif; ?>
                    <?php if (
                        (isset($sessionDetails['qr_details_privillages']) && $sessionDetails['qr_details_privillages'] > 10 && $sessionDetails['qr_details_privillages'] <= 20) &&
                        !(isset($sessionDetails['qr_details_privillages']) && $sessionDetails['qr_details_privillages'] > 20 && $sessionDetails['qr_details_privillages'] <= 30)
                    ): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'plantation_management.php' ? ' active' : ''; ?>"
                                href="plantation_management.php" target="_parent" rel="noopener">QR Details</a>
                        </li>
                    <?php endif; ?>
                    <?php if (
                        (isset($sessionDetails['estate_privillages']) && $sessionDetails['estate_privillages'] > 10 && $sessionDetails['estate_privillages'] <= 20) &&
                        !(isset($sessionDetails['estate_privillages']) && $sessionDetails['estate_privillages'] > 20 && $sessionDetails['estate_privillages'] <= 30)
                    ): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'estate_details_page.php' ? ' active' : ''; ?>"
                                href="estate_details_page.php" target="_parent" rel="noopener">Estate Management</a>
                        </li>
                    <?php endif; ?>
                    <?php if (
                        (isset($sessionDetails['qr_details_privillages']) && $sessionDetails['qr_details_privillages'] > 10 && $sessionDetails['qr_details_privillages'] <= 20) &&
                        !(isset($sessionDetails['qr_details_privillages']) && $sessionDetails['qr_details_privillages'] > 20 && $sessionDetails['qr_details_privillages'] <= 30)
                    ): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'plantation_management.php' ? ' active' : ''; ?>"
                                href="about.php" target="_parent" rel="noopener">About</a>
                        </li>
                    <?php endif; ?>

                    <!-- User privillages (0-10) -->
                    <?php if (isset($sessionDetails['handle_csv_privillages']) && $sessionDetails['handle_csv_privillages'] > 0 && $sessionDetails['handle_csv_privillages'] <= 10): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? ' active' : ''; ?>"
                                href="index.php" target="_parent" rel="noopener">Handle CSV</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($sessionDetails['gen_qr_privillages']) && $sessionDetails['gen_qr_privillages'] > 0 && $sessionDetails['gen_qr_privillages'] <= 10): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'generate_pdf.php' ? ' active' : ''; ?>"
                                href="generate_pdf.php" target="_parent" rel="noopener">Generate QR Codes</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($sessionDetails['qr_details_privillages']) && $sessionDetails['qr_details_privillages'] > 0 && $sessionDetails['qr_details_privillages'] <= 10): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'plantation_management.php' ? ' active' : ''; ?>"
                                href="plantation_management.php" target="_parent" rel="noopener">QR Details</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($sessionDetails['estate_privillages']) && $sessionDetails['estate_privillages'] > 0 && $sessionDetails['estate_privillages'] <= 10): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'estate_details_page.php' ? ' active' : ''; ?>"
                                href="estate_details_page.php" target="_parent" rel="noopener">Estate Management</a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($sessionDetails['gen_qr_privillages']) && $sessionDetails['gen_qr_privillages'] > 0 && $sessionDetails['gen_qr_privillages'] <= 10): ?>
                        <li class="nav-item">
                            <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? ' active' : ''; ?>"
                                href="about.php" target="_parent" rel="noopener">About</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <!-- Logout button -->
                <?php if (isset($sessionDetails['email'])): ?>
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