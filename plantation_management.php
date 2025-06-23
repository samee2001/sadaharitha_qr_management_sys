<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: logIn.php');
    exit();
}
include 'connect.php'; // Include your database connection file
// Set the number of records per page
$records_per_page = 20;

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Calculate the starting record for the current page
$start_from = ($page - 1) * $records_per_page;


// Fetch data for the current page
$sql = "SELECT * FROM `qr_batch_details` ORDER BY created_at DESC LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

// Fetch total number of records to calculate total pages
$total_records_query = "SELECT COUNT(*) as total_records FROM `qr_batch_details`";
$total_records_result = $conn->query($total_records_query);
$total_records_row = $total_records_result->fetch_assoc();
$total_records = $total_records_row['total_records'];

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>

<body style="background-image: url('sdh_bg_2.png'); background-size: cover;">
    <?php include 'components/navbar.php'; ?>
    <br><br>
    
    <div class="container my-5 p-4" style="background-color: rgb(231, 231, 231); border: 1px solid rgb(114, 234, 126); border-radius: 10px; opacity: 0.92; max-height: 700px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-success">
                <a href="add_details.php" class="text-light text-decoration-none">Add Batch Details</a>
            </button>
            <span class="badge bg-info text-dark fs-6">Total Batches: <?php echo $total_records; ?></span>
        </div>
        <div class="table-responsive" style="max-height: 500px;">
            <table class="table table-striped table-hover table-bordered align-middle mb-0" style="font-size: 0.95rem;">
                <thead class="table-success sticky-top" style="font-size: 1rem;">
                    <tr class="text-center">
                        <th scope="col">ID</th>
                        <th scope="col">Created By</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Start</th>
                        <th scope="col">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='text-center'>";
                            echo "<td>" . htmlspecialchars($row['batch_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($_SESSION['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['start']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['step']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation example" class="mt-3">
            <ul class="pagination justify-content-center">
                <?php
                if ($page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '">Previous</a></li>';
                }
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                }
                if ($page < $total_pages) {
                    echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '">Next</a></li>';
                }
                ?>
            </ul>
        </nav>
    </div>
    <?php include 'components/footer.php'; ?>
</body>

</html>