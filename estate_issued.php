<?php
// Start session for user authentication
session_start();
include 'connect.php';
if (!isset($_SESSION['email'])) {
    header('Location: logIn.php');
    exit();
}

// Initialize variables
$errors = [];
$successMessage = '';

// Process form submission
if (isset($_POST['issue'])) {
    // Sanitize and validate inputs
    $rangeStart = filter_input(INPUT_POST, 'rangeStart', FILTER_VALIDATE_INT);
    $rangeEnd = filter_input(INPUT_POST, 'rangeEnd', FILTER_VALIDATE_INT);
    $estateId = filter_input(INPUT_POST, 'estateSelect', FILTER_VALIDATE_INT);

    // Validate inputs
    if ($rangeStart === false || $rangeStart <= 0) {
        $errors[] = "Range Start must be a positive integer.";
    }
    if ($rangeEnd === false || $rangeEnd <= 0) {
        $errors[] = "Range End must be a positive integer.";
    }
    if ($rangeStart !== false && $rangeEnd !== false && $rangeEnd < $rangeStart) {
        $errors[] = "Range End must be greater than or equal to Range Start.";
    }
    if ($estateId === false || $estateId <= 0) {
        $errors[] = "Please select a valid estate.";
    }

    // If no validation errors, check for overlapping ranges across all estates
    if (empty($errors)) {
        try {
            // Query to check for overlapping ranges in issued_estate
            $stmt = $conn->prepare("
                SELECT estate_id, range_start, range_end
                FROM issued_estate
                WHERE
                    (range_start <= ? AND range_end >= ?)
                    OR
                    (range_start <= ? AND range_end >= ?)
                    OR
                    (? <= range_end AND ? >= range_start)
            ");
            $stmt->bind_param("iiiiii", $rangeEnd, $rangeStart, $rangeEnd, $rangeStart, $rangeStart, $rangeEnd);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $existingEstateId = $row['estate_id'];
                $existingRangeStart = $row['range_start'];
                $existingRangeEnd = $row['range_end'];
                // Fetch estate name for user-friendly error message
                $estateStmt = $conn->prepare("SELECT estate_name FROM estate WHERE id = ?");
                $estateStmt->bind_param("i", $existingEstateId);
                $estateStmt->execute();
                $estateResult = $estateStmt->get_result();
                $estateName = $estateResult->num_rows > 0 ? $estateResult->fetch_assoc()['estate_name'] : "unknown estate";
                $estateStmt->close();
                $errors[] = "The range $rangeStart to $rangeEnd overlaps with an existing range ($existingRangeStart to $existingRangeEnd) allocated to '$estateName'.";
            }
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = "Database error while checking range: " . htmlspecialchars($e->getMessage());
            error_log("Range check error: " . $e->getMessage());
        }
    }

    // Check if the range exists in plant_data
    if (empty($errors)) {
        try {
            // Query to count how many plant numbers in the range exist in plant_data
            $stmt = $conn->prepare("
                SELECT COUNT(*) as total
                FROM plant_data
                WHERE plant_number BETWEEN ? AND ?
            ");
            $stmt->bind_param("ii", $rangeStart, $rangeEnd);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $totalPlants = $row['total'];
            $expectedPlants = $rangeEnd - $rangeStart + 1;

            if ($totalPlants != $expectedPlants) {
                $errors[] = "The range $rangeStart to $rangeEnd is invalid. Not all plant numbers in this range exist in the plant_data table.";
            }
            $stmt->close();
        } catch (Exception $e) {
            $errors[] = "Database error while checking plant numbers: " . htmlspecialchars($e->getMessage());
            error_log("Plant range check error: " . $e->getMessage());
        }
    }

    // If no errors, insert into issued_estate and update plant_data
    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            // Insert into issued_estate
            date_default_timezone_set('Asia/Colombo');
            $issuedAt = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("
                INSERT INTO issued_estate (estate_id, range_start, range_end, issued_at, issued_by)
                VALUES (?, ?, ?, ?, ?)
            ");
            $issuedBy = $_SESSION['email'] ?? 'system'; // Use session email or default
            $stmt->bind_param("iiiss", $estateId, $rangeStart, $rangeEnd, $issuedAt, $issuedBy);
            if (!$stmt->execute()) {
                throw new Exception("Failed to save issuance details: " . $conn->error);
            }
            // Retrieve the last inserted id
            $issueId = $conn->insert_id;
            $stmt->close();

            // Update plant_data with issue_id for the range
            $updateStmt = $conn->prepare("
                UPDATE plant_data
                SET issued_id = ?
                WHERE plant_number BETWEEN ? AND ?
            ");
            $updateStmt->bind_param("iii", $issueId, $rangeStart, $rangeEnd);
            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update plant_data with issue_id: " . $conn->error);
            }
            $updateStmt->close();

            $conn->commit();
            $successMessage = "QR codes issued successfully for range $rangeStart to $rangeEnd.";
            echo "<script>document.getElementById('qrForm').reset();</script>";
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Database error: " . htmlspecialchars($e->getMessage());
            error_log("Transaction error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue QR Codes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body style="background-image: url('sdh_bg_2.png');">
    <?php include 'components/navbar.php'; ?>
    <br><br>
    <div class="container py-5">
        <br>
        <div class="row justify-content-center g-4">
            <!-- Issue QR Codes Form -->
            <div class="col-12 col-lg-5">
                <div class="card shadow-sm border-0 opacity-75">
                    <div class="card-header bg-success text-white text-center">
                        <i class="bi bi-qr-code-scan fs-3"></i>
                        <h4 class="mb-0 mt-2">Issue QR Codes</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($successMessage): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($successMessage) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form method="post" id="qrForm" novalidate autocomplete="off">
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label for="rangeStart" class="form-label fw-semibold">Issue From</label>
                                    <input type="number" class="form-control form-control-sm" id="rangeStart" name="rangeStart"
                                        value="<?= !empty($errors) && isset($_POST['rangeStart']) ? htmlspecialchars($_POST['rangeStart']) : '' ?>"
                                        placeholder="e.g., 1" required aria-describedby="startHelp" min="1">
                                    <div id="startHelp" class="form-text small">Starting plant number</div>
                                </div>
                                <div class="col-6">
                                    <label for="rangeEnd" class="form-label fw-semibold">To</label>
                                    <input type="number" class="form-control form-control-sm" id="rangeEnd" name="rangeEnd"
                                        value="<?= !empty($errors) && isset($_POST['rangeEnd']) ? htmlspecialchars($_POST['rangeEnd']) : '' ?>"
                                        placeholder="e.g., 10" required aria-describedby="endHelp" min="1">
                                    <div id="endHelp" class="form-text small">Ending plant number</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="estateSelect" class="form-label fw-semibold">Estate</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                    <select name="estateSelect" id="estateSelect" class="form-select" required>
                                        <option value="">Select Estate</option>
                                        <?php
                                        // Use the same $conn for this query
                                        $result = $conn->query("SELECT id, estate_name FROM estate ORDER BY estate_name");
                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $id = htmlspecialchars($row['id']);
                                                $name = htmlspecialchars($row['estate_name']);
                                                $selected = (!empty($errors) && isset($_POST['estateSelect']) && $_POST['estateSelect'] == $id) ? 'selected' : '';
                                                echo "<option value=\"$id\" $selected>$name</option>";
                                            }
                                            $result->free();
                                        } else {
                                            echo "<option value=''>No estates available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id="estateHelp" class="form-text small">Choose the estate for QR issuance.</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="issue" class="btn btn-success btn-sm">
                                    <i class="bi bi-qr-code me-1"></i> Issue QR Codes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Available QR Ranges -->
            <div class="col-12 col-lg-7">
                <div class="card shadow-sm border-0 h-100 opacity-75">
                    <div class="card-header bg-success text-white text-center">
                        <i class="bi bi-list-ol fs-4"></i>
                        <h5 class="mb-0 mt-1">Available QR Ranges Can be Issued to Estates</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-2">
                            <table class="table table-bordered table-hover table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Starting Plant Number</th>
                                        <th>Ending Plant Number</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Find available ranges in plant_data
                                // This query requires MySQL 8.0+ for ROW_NUMBER()
                                $sql = "
                                    SELECT
                                      MIN(plant_number) AS range_start,
                                      MAX(plant_number) AS range_end,
                                      'Available' AS status
                                    FROM (
                                      SELECT
                                        plant_number,
                                        issued_id,
                                        plant_number - ROW_NUMBER() OVER (ORDER BY plant_number) AS grp
                                      FROM
                                        plant_data
                                      WHERE
                                        issued_id = 0
                                    ) AS subquery
                                    GROUP BY grp
                                    ORDER BY range_start
                                ";
                                $result = $conn->query($sql);
                                $total = 0;
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $total++;
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($row['range_start']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['range_end']) . '</td>';
                                        echo '<td><span class="badge bg-success">' . htmlspecialchars($row['status']) . '</span></td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="3">No available ranges</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-info text-dark">
                                <?php echo isset($total) ? $total : 0; ?> range(s) available
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <?php include 'released_estates.php'; ?>
            </div>                  
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Basic form validation
        (function() {
            'use strict'
            const form = document.getElementById('qrForm');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
    </script>
    <?php include 'components/footer.php'; ?>
</body>
</html>