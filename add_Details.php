<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: logIn.php');
    exit();
}

include 'connect.php'; // Database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

$email = $_SESSION['email'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Get form data
    $start = (int)($_POST['rangeStart'] ?? 0);
    $step = (int)($_POST['rangeStep'] ?? 0);
    $background_color = isset($_POST['cellColorSelect']) ? trim($_POST['cellColorSelect']) : '';
    $year = trim($_POST['year'] ?? '');
    $botanical_name = trim($_POST['bot'] ?? '');
    $email = $_SESSION['email'];
    //current date and time
    $created_at = date('Y-m-d H:i:s');
    

    // Validate inputs
    if (empty($year)) {
        $error = "Year is required.";
    } elseif (empty($botanical_name)) {
        $error = "Botanical name is required.";
    } elseif ($start < 1) {
        $error = "Start must be a positive number.";
    } elseif ($step < 1 || $step > 5000) {
        $error = "Step must be between 1 and 5000.";
    } elseif (!preg_match('/^\d{1,3},\d{1,3},\d{1,3}$/', $background_color)) {
        $error = "Invalid RGB color format.";
    } else {
        // Validate color exists in colors table
        $color_check = $conn->prepare("SELECT color_code FROM colors WHERE color_code = ?");
        $color_check->bind_param("s", $background_color);
        $color_check->execute();
        $result = $color_check->get_result();
        if ($result->num_rows === 0) {
            $error = "Error: Selected color is invalid.";
        }
        $color_check->close();
    }

    // Check for range overlap
    if (!$error) {
        $end = $start + $step - 1;
        $stmt = $conn->prepare("SELECT plant_number FROM plant_data WHERE plant_number BETWEEN ? AND ?");
        if (!$stmt) {
            $error = "Range check failed: " . $conn->error;
        } else {
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = "Range $start to $end overlaps with existing records.";
            }
            $stmt->close();
        }
    }

    // Insert records
    if (!$error) {
        $conn->begin_transaction();
        $batch_stmt = null;
        $stmt = null;
        try {
            // Insert batch details into qr_batch_details
            $batch_stmt = $conn->prepare("INSERT INTO qr_batch_details (start, step, email, background_color, created_at) VALUES (?, ?, ?, ?,?)");
            if (!$batch_stmt) {
                throw new Exception("Batch prepare failed: " . $conn->error);
            }
            $batch_stmt->bind_param("iisss", $start, $step, $email, $background_color, $created_at);
            if (!$batch_stmt->execute()) {
                throw new Exception("Batch insert failed: " . $batch_stmt->error);
            }

            // Retrieve the last inserted batch_id
            $batch_id = $conn->insert_id;

            // Insert records into data_plant
            $stmt = $conn->prepare("INSERT INTO plant_data (plant_number, year, email, botanical_name, background_color, qr_code_details, batch_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }

            for ($i = 0; $i < $step; $i++) {
                $plant_number = $start + $i;
                $qr_code_details = "$year $botanical_name Plant No. $plant_number";
                $stmt->bind_param("iissssi", $plant_number, $year, $email, $botanical_name, $background_color, $qr_code_details, $batch_id);
                if (!$stmt->execute()) {
                    if ($conn->errno === 1062) {
                        throw new Exception("Duplicate plant number detected: $plant_number");
                    }
                    throw new Exception("Insert failed for plant $plant_number: " . $stmt->error);
                }
            }

            $conn->commit();
            $_SESSION['success'] = "Batch added successfully!";
            header("Location: add_Details.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Insertion failed: " . htmlspecialchars($e->getMessage());
        } finally {
            // Close statements only if they exist and are not closed
            if (isset($batch_stmt) && $batch_stmt instanceof mysqli_stmt) {
                $batch_stmt->close();
            }
            if (isset($stmt) && $stmt instanceof mysqli_stmt) {
                $stmt->close();
            }
        }
    }
}

// Default form values
$rangeStart = $_POST['rangeStart'] ?? 1;
$rangeStep = $_POST['rangeStep'] ?? 100;
$year = $_POST['year'] ?? '';
$botanical_name = $_POST['bot'] ?? '';
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fill QR Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-image: url('sdh_bg_2.png');
            font-family: 'Arial', sans-serif;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .btn-primary {
            background-color: #28a745;
            border-color: #28a745;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .color-preview {
            width: 24px;
            height: 24px;
            border: 1px solid #dee2e6;
            border-radius: 50%;
            display: inline-block;
            vertical-align: middle;
            margin-left: 8px;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .form-text {
            color: #6c757d;
        }
    </style>
</head>

<body>
    <?php include 'components/navbar.php'; ?>
    <br>
    <div class="container my-5">
        <h2 class="text-center fw-bold mb-3" style="color: #40f23d;">Add QR Batch Details</h2>
        <h5 class="text-center fw-semibold mb-4 " style="color: #40f23d;">Enter details to create a new batch</h5>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success:</strong> <?php echo htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <div class="card p-4 mx-auto " style="max-width: 600px; opacity: 0.80;">
            <form method="post" id="qrForm" novalidate>
                <div class="row g-3 mb-1">
                    <div class="col-md-6">
                        <label for="rangeStart" class="form-label">Range Start</label>
                        <input type="number" class="form-control" id="rangeStart" name="rangeStart" value="<?php echo htmlspecialchars($rangeStart); ?>" required min="1" aria-describedby="rangeStartHelp">
                        <div id="rangeStartHelp" class="form-text">Starting plant number</div>
                        <div class="invalid-feedback">Please enter a positive number.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="rangeStep" class="form-label">Range Step</label>
                        <input type="number" class="form-control" id="rangeStep" name="rangeStep" value="<?php echo htmlspecialchars($rangeStep); ?>" aria-describedby="rangeStepHelp">
                        <div id="rangeStepHelp" class="form-text">How many qr codes from starting</div>
                        <div class="invalid-feedback">Maximum step is 5000.</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Year</label>
                    <input type="text" class="form-control" id="year" name="year" value="<?php echo htmlspecialchars($year); ?>" required placeholder="e.g., 24/25" aria-describedby="yearHelp">
                    <div id="yearHelp" class="form-text">Year of the batch</div>
                    <div class="invalid-feedback">Please enter the year.</div>
                </div>
                <div class="mb-3">
                    <label for="bot" class="form-label">Botanical Name</label>
                    <select class="form-select" id="bot" name="bot" required aria-describedby="botHelp">
                        <option value="">Select Scientific Name</option>
                        <option value="Aquilaria Crassna" <?php if ($botanical_name == "Aquilaria Crassna") echo "selected"; ?>>Aquilaria Crassna</option>
                        <option value="Aquilaria Senensis" <?php if ($botanical_name == "Aquilaria Senensis") echo "selected"; ?>>Aquilaria Senensis</option>
                        <option value="Aquilaria Sabintegra" <?php if ($botanical_name == "Aquilaria Sabintegra") echo "selected"; ?>>Aquilaria Sabintegra</option>
                        <option value="Aquilaria Agallocha" <?php if ($botanical_name == "Aquilaria Agallocha") echo "selected"; ?>>Aquilaria Agallocha</option>
                        <!-- Add more hardcoded options as needed -->
                    </select>
                    <div id="botHelp" class="form-text">Scientific name of the plant</div>
                    <div class="invalid-feedback">Please select the botanical name.</div>
                </div>
                <div class="mb-3">
                    <label for="cellColorSelect" class="form-label">Background Color</label>
                    <div class="input-group">
                        <select name="cellColorSelect" id="cellColorSelect" class="form-select" required aria-describedby="colorHelp">
                            <option value="">Select a color</option>
                            <?php
                            $result = $conn->query("SELECT color_code, color_name FROM colors");
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $color_code = htmlspecialchars($row['color_code']);
                                    $color_name = htmlspecialchars($row['color_name']);
                                    echo "<option value='$color_code' data-color='$color_code' style='background-color: rgb($color_code); color: #000;'>$color_name</option>";
                                }
                                $result->free();
                            } else {
                                echo "<option value=''>No colors available</option>";
                            }
                            ?>
                        </select>
                        <span id="colorPreview" class="color-preview" style="background-color: #fff;"></span>
                    </div>
                    <div id="colorHelp" class="form-text">Background color for the QR code</div>
                    <div class="invalid-feedback">Please select a color.</div>
                </div>
                <div class="text-center">
                    <button type="submit" name="submit" class="btn btn-primary w-100">Add Batch</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Js/validation.js">

    </script>
    <?php include 'components/footer.php'; ?>
</body>

</html>