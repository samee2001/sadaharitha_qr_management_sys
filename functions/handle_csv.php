<?php
// handle_csv.php

function processCSVUpload($mysqli, $csvFile, $tableName )
{
    $result = ['success' => false, 'error' => ''];

    try {
        // Validate inputs
        if (!$mysqli || !($mysqli instanceof mysqli)) {
            throw new Exception("Invalid database connection");
        }

        if (!isset($csvFile['error']) || $csvFile['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error");
        }

        /*if (empty($tableName) || !preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            throw new Exception("Invalid table name");
        }

        $checkTable = $mysqli->query("SHOW TABLES LIKE '$tableName'");
        if ($checkTable && $checkTable->num_rows > 0) {

            throw new Exception("A table with the name '$tableName' already exists. Please use a unique table name.");
        }*/

        // Process CSV
        $handle = fopen($csvFile['tmp_name'], 'r');
        if ($handle === false) {
            throw new Exception("Failed to open CSV file");
        }

        // Get headers
        $headers = fgetcsv($handle);
        if ($headers === false || count($headers) === 0) {
            throw new Exception("Invalid CSV format or empty file");
        }

        // Create table with validation
        $createTableQuery = "CREATE TABLE IF NOT EXISTS `$tableName` (
            id INT AUTO_INCREMENT PRIMARY KEY, ";

        foreach ($headers as $index => $header) {
            $cleanHeader = trim($header);
            if (empty($cleanHeader)) {
                throw new Exception("Invalid column name in CSV header");
            }
            $createTableQuery .= "`$cleanHeader` TEXT, ";
        }

        $createTableQuery .= "UNIQUE (`" . trim($headers[0]) . "`))";

        if (!$mysqli->query($createTableQuery)) {
            throw new Exception("Table creation failed: " . $mysqli->error);
        }

        // Prepare insert statement
        $columns = implode(", ", array_map(function ($col) {
            return "`" . trim($col) . "`";
        }, $headers));

        $placeholders = str_repeat('?, ', count($headers) - 1) . '?';
        $updateClause = implode(', ', array_map(function ($col) {
            return "`" . trim($col) . "` = VALUES(`" . trim($col) . "`)";
        }, $headers));

        $stmt = $mysqli->prepare("INSERT INTO `$tableName` ($columns) 
                                VALUES ($placeholders)
                                ON DUPLICATE KEY UPDATE $updateClause");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }

        // Process rows
        $rowCount = 0;
        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== count($headers)) {
                continue; // Skip invalid rows
            }

            $data = array_map('trim', $data);
            if (empty(array_filter($data))) {
                continue; // Skip empty rows
            }

            $stmt->bind_param(str_repeat('s', count($data)), ...$data);
            if (!$stmt->execute()) {
                throw new Exception("Insert failed: " . $stmt->error);
            }
            $rowCount++;
        }

        if ($rowCount === 0) {
            throw new Exception("No valid data rows found in CSV");
        }

        $result['success'] = true;
        $result['message'] = "Successfully processed $rowCount rows";

    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
    } finally {
        if (isset($handle) && is_resource($handle)) {
            fclose($handle);
        }
        if (isset($stmt) && $stmt instanceof mysqli_stmt) {
            $stmt->close();
        }
    }

    return $result;
}

// Usage in your index.php:
// include 'handle_csv.php';
// 
// if (isset($_POST['upload'])) {
//     $uploadResult = processCSVUpload(
//         $mysqli,
//         $_FILES['csvfile'],
//         $_POST['tableName']
//     );
//     
//     if ($uploadResult['success']) {
//         // Show success message
//     } else {
//         // Show error message
//     }
// }
?>