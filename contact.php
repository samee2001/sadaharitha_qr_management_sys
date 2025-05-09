<?php
// contact.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Us | Sadaharitha Plantations Limited</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS and Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #eaf6ea 0%, #f1f4f8 100%);
        }

        .contact-hero {
            background: linear-gradient(120deg, rgba(76, 175, 80, 0.92), rgba(56, 142, 60, 0.88)), url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
            color: #fff;
            padding: 90px 0 60px 0;
            text-align: center;
            position: relative;
        }

        .contact-hero .company-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 4px 16px 0 rgba(76, 175, 80, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            animation: floatLogo 2.5s ease-in-out infinite alternate;
        }

        @keyframes floatLogo {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-12px);
            }
        }

        .contact-hero .company-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .contact-hero .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .contact-card {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(24, 77, 39, 0.10);
            padding: 2.5rem 2rem;
            margin-top: -60px;
            margin-bottom: 2rem;
            max-width: 720px;
            width: 100%;
        }

        .contact-info-list i {
            color: #256d3b;
            font-size: 1.25rem;
            margin-right: 0.7rem;
        }

        .form-control,
        .form-select {
            border-radius: 1rem;
            box-shadow: 0 1px 4px rgba(24, 77, 39, 0.04);
            border: 1.5px solid #e0e0e0;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #256d3b;
            box-shadow: 0 0 0 3px #4b8e4b33;
            outline: none;
        }

        .btn-success {
            background: linear-gradient(90deg, #256d3b 60%, #4b8e4b 100%);
            border: none;
            font-weight: 600;
            border-radius: 2rem;
            padding: 0.7rem 2.2rem;
            font-size: 1.1rem;
            letter-spacing: 1px;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px 0 rgba(24, 77, 39, 0.12);
        }

        .btn-success:hover,
        .btn-success:focus {
            background: linear-gradient(90deg, #4b8e4b 60%, #256d3b 100%);
            color: #ffe082;
            box-shadow: 0 4px 16px 0 rgba(24, 77, 39, 0.18);
        }

        .footer {
            background: linear-gradient(90deg, #256d3b 70%, #4b8e4b 100%);
            color: #eaf6ea;
            padding: 2rem 0 1rem 0;
            border-top-left-radius: 2rem;
            border-top-right-radius: 2rem;
            box-shadow: 0 -4px 16px 0 rgba(24, 77, 39, 0.10);
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

        .social-icons a {
            color: #256d3b;
            margin-right: 1rem;
            font-size: 1.4rem;
            transition: color 0.2s;
        }

        .social-icons a:hover {
            color: #4b8e4b;
        }

        .aspect-ratio-16-9 {
            aspect-ratio: 16 / 9;
            width: 100%;
            border-radius: 0.8rem;
            overflow: hidden;
        }

        .aspect-ratio-1-1 {
            aspect-ratio: 1 / 1;
        }

        .form-label {
            font-weight: 500;
            color: #256d3b;
        }

        .form-text {
            color: #4b8e4b;
        }

        .required-star {
            color: #be5103;
            font-size: 1.1em;
        }

        @media (max-width: 600px) {
            .contact-card {
                padding: 1.2rem 0.5rem;
            }

            .contact-hero .hero-title {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'components/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="company-logo">
            <img src="sadaharitha_logo_black.png" alt="Sadaharitha Logo">
        </div>
        <div class="hero-title mb-3">Contact Sadaharitha Plantations Limited</div>
        <p class="lead mb-0">We're here to answer your questions and help you grow with us.<br>
            Reach out by form, email, phone, or social media-whatever works for you!</p>
    </section>

    <!-- Contact Section -->
    <br>
    <br>
    <br>
    <br>

    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-success">Get in Touch</h2>
        </div>
    </div>
    <?php include 'components/contact_card.php'; ?>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>