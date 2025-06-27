<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow-sm mb-4 px-3 text-black" style="opacity: 0.9;">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php" target="_parent" rel="noopener">
            <img src="sadaharitha_logo_black.png" alt="Sadaharitha Logo" class="me-2" style="height: 40px; width: auto;">
            Sadaharitha
        </a>
        <!-- Toggler for mobile view -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navbar content -->
        <div class="collapse navbar-collapse justify-content-center" style="margin-left: 400px;" id="navbarNav">
            <ul class="navbar-nav ms-lg-5">
                <?php /* if ($_SESSION['handle_csv_privillages'] >= 20): ?>
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
                <?php endif; */?>

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
                            Batch Creation
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['qr_details_privillages'] >= 10): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="plantation_management.php" target="_parent" rel="noopener">Batch Details</a>
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
                        <a class="nav-link" href="estate_details_page.php" target="_parent" rel="noopener">Estate Management</a>
                    </li>
                <?php endif; ?>

                
            </ul>
            <!--profile-->
            <div class="ms-auto ms-1 me-3" id="profileTrigger">
                <div class="bg-warning rounded-circle d-flex justify-content-center align-items-center" style="width: 50px; height: 50px; cursor: pointer;">
                    <a href="javascript:void(0);" class="btn p-0">
                        <i class="bi bi-person-fill"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Side Drawer -->
<div id="sideDrawer" class="side-drawer" style="opacity: 0.75;">
    <div class="drawer-content">
        <ul class="drawer-menu">
            <li><a href="#" class="drawer-link me-3"><i class="bi bi-person-fill"></i><?php echo $_SESSION['email']; ?></a></li>
        </ul>
        <div class="drawer-footer">
            <a href="logout.php" class="btn btn-outline-success w-100">Logout</a>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
    .side-drawer {
        position: fixed;
        top: 0;
        right: -300px;
        width: 300px;
        height: 100%;
        background-color: #fff;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
        transition: right 0.3s ease-in-out;
        z-index: 1000;
        padding: 20px;
    }

    .side-drawer.active {
        right: 0;
    }

    .drawer-content {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .drawer-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .drawer-link {
        margin-top: 50px;
        display: block;
        padding: 10px 15px;
        color: #000;
        text-decoration: none;
        transition: background-color 0.2s;
    }

    .drawer-link:hover {
        background-color: #f8f9fa;
        color: #000;
    }

    .drawer-footer {
        margin-top: auto;
        margin-bottom: 50px;
    }
</style>

<!-- JavaScript -->
<script>
    document.getElementById('profileTrigger').addEventListener('click', function() {
        const drawer = document.getElementById('sideDrawer');
        drawer.classList.toggle('active');
    });
</script>