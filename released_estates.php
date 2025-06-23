<?php
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
$sql = "SELECT * FROM `issued_estate` ORDER BY id DESC LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

// Fetch total number of records to calculate total pages
$total_records_query = "SELECT COUNT(*) as total_records FROM `issued_estate`";
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
    <title>Issued Estates</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>
<div class="container py-3 p-4" style="background-color: rgb(231, 231, 231); border: 1px solid rgb(114, 234, 126); border-radius: 10px; opacity: 0.80; max-height: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="badge bg-info text-dark fs-6">Total Batches: <?php echo $total_records; ?></span>
    </div>
    <div class="table-responsive" style="max-height: 500px;">
        <div class="card-header bg-success text-white text-center">
            <i class="bi bi-folder2-open"></i>
            <h5 class="mb-0 mt-1">Already Issued QR Ranges to Estates</h5>
        </div>
        <table class="table table-striped table-hover table-bordered align-middle mb-0" style="font-size: 0.95rem;">
            <thead class="table-success sticky-top" style="font-size: 1rem;">
                <tr class="text-center">
                    <th scope="col">ID</th>
                    <th scope="col">Range Start</th>
                    <th scope="col">Range End</th>
                    <th scope="col">Issued at</th>
                    <th scope="col">Estate ID</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='text-center'>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        //echo "<td>" . htmlspecialchars($_SESSION['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['range_start']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['range_end']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['issued_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['estate_id']) . "</td>";
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

</html>