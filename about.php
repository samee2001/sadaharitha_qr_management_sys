<?php
// about.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us | Sadaharitha Plantations Limited</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #eaf6ea 0%, #f1f4f8 100%);
        }
        .hero-section {
            background: linear-gradient(120deg, rgba(76,175,80,0.90), rgba(56,142,60,0.88)), url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
            color: #fff;
            padding: 110px 0 80px 0;
            position: relative;
            overflow: hidden;
        }
        .hero-section .company-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 4px 16px 0 rgba(76,175,80,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .hero-section .company-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        .hero-section .btn {
            font-size: 1.25rem;
            padding: 0.75rem 2.5rem;
            border-radius: 2rem;
            font-weight: 600;
            background: linear-gradient(90deg, #ffe082 60%, #a3d9a5 100%);
            color: #256d3b;
            border: none;
            box-shadow: 0 2px 8px 0 rgba(76,175,80,0.09);
            transition: background 0.2s, color 0.2s;
        }
        .hero-section .btn:hover {
            background: linear-gradient(90deg, #a3d9a5 60%, #ffe082 100%);
            color: #184d27;
        }
        .about-section {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(24, 77, 39, 0.10);
            padding: 3rem 2rem;
            margin-top: -60px;
            margin-bottom: 2rem;
        }
        .about-img {
            border-radius: 1rem;
            box-shadow: 0 4px 16px 0 rgba(76,175,80,0.12);
        }
        .about-section h2 {
            color: #256d3b;
        }
        .about-section .btn-success {
            background: linear-gradient(90deg, #256d3b 60%, #4b8e4b 100%);
            border: none;
            font-weight: 600;
            border-radius: 2rem;
            padding: 0.6rem 2.2rem;
            font-size: 1.1rem;
            letter-spacing: 1px;
            transition: background 0.2s, color 0.2s;
        }
        .about-section .btn-success:hover {
            background: linear-gradient(90deg, #4b8e4b 60%, #256d3b 100%);
            color: #ffe082;
        }
        .footer {
            background: linear-gradient(90deg, #256d3b 70%, #4b8e4b 100%);
            color: #eaf6ea;
            padding: 2rem 0 1rem 0;
            border-top-left-radius: 2rem;
            border-top-right-radius: 2rem;
            box-shadow: 0 -4px 16px 0 rgba(24,77,39,0.10);
        }
        .footer a {
            color: #ffe082;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer a:hover {
            color: #fff;
            text-decoration: underline;
        }
        .footer .footer-logo {
            width: 38px;
            height: 38px;
            object-fit: contain;
            margin-right: 0.5rem;
            vertical-align: middle;
        }
    </style>
</head>
<body>
<!-- Navigation Bar -->
<?php include 'components/navbar.php'; ?>
<br>

<!-- Hero Section -->
<section class="hero-section text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="company-logo mx-auto mx-lg-0">
                    <img src="sadaharitha_logo_black.png" alt="Sadaharitha Logo">
                </div>
                <h1 class="display-4 fw-bold mb-3">About <span style="color:#ffe082;">Sadaharitha Plantations Limited</span></h1>
                <p class="lead mb-5">We are passionate about sustainability, innovation, and growth. Our mission is to empower communities and the environment through responsible plantation management and world-class service.</p>
                <a href="#about" class="btn shadow">Learn More</a>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=500&q=80" alt="Team" class="img-fluid about-img">
            </div>
        </div>
    </div>
</section>

<!-- About Section (Middle) -->
<section id="about" class="about-section container my-5">
    <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0">
            <img src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=600&q=80" alt="About Us" class="img-fluid about-img">
        </div>
        <div class="col-md-6">
            <h2 class="fw-bold mb-3">Who We Are</h2>
            <p class="mb-4">Founded in 2002, <strong>Sadaharitha Plantations Limited</strong> has grown from a visionary idea into Sri Lanka’s leader in sustainable forestry and plantation management. We believe in the power of collaboration, integrity, and continuous learning. Our diverse team brings together expertise from various fields, ensuring innovative solutions for every challenge.</p>
            <ul class="list-unstyled mb-4">
                <li class="mb-2"><i class="bi bi-tree-fill text-success me-2"></i> Sustainable and eco-friendly operations</li>
                <li class="mb-2"><i class="bi bi-people-fill text-success me-2"></i> Experienced & passionate team</li>
                <li class="mb-2"><i class="bi bi-award-fill text-success me-2"></i> Commitment to excellence</li>
                <li><i class="bi bi-chat-dots-fill text-success me-2"></i> Transparent communication</li>
            </ul>
            <a href="contact.php" class="btn btn-success rounded-pill px-4">Contact Us</a>
        </div>
    </div>
</section>

<!-- Footer -->
<?php include 'components/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
