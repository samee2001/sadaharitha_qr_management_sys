<?php
// handle_csv.php

function processCSVUpload($mysqli, $csvFile, $tableName)
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
            // Use INT for Plant Number (first column), TEXT for others
            $type = ($index === 0) ? 'INT' : 'TEXT';
            $createTableQuery .= "`$cleanHeader` $type, ";
        }

        $createTableQuery .= "UNIQUE (`" . trim($headers[0]) . "`))";

        if (!$mysqli->query($createTableQuery)) {
            throw new Exception("Table creation failed: " . $mysqli->error);
        }

        // Ensure index on Plant Number (first column)
        $indexQuery = "CREATE INDEX IF NOT EXISTS idx_plant_number ON `$tableName` (`" . trim($headers[0]) . "`)";
        if (!$mysqli->query($indexQuery)) {
            throw new Exception("Index creation failed: " . $mysqli->error);
        }

        // Start transaction
        $mysqli->begin_transaction();

        // Prepare insert statement
        $columns = implode(", ", array_map(function ($col) {
            return "`" . trim($col) . "`";
        }, $headers));

        $placeholders = str_repeat('?, ', count($headers) - 1) . '?';
        $stmt = $mysqli->prepare("INSERT IGNORE INTO `$tableName` ($columns) VALUES ($placeholders)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $mysqli->error);
        }

        // Batch processing: Collect Plant Numbers to check duplicates
        $plantNumbers = [];
        $rows = [];
        $batchSize = 1000; // Process in batches of 1000 rows
        $rowCount = 0;
        $skippedCount = 0;

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== count($headers)) {
                $skippedCount++;
                continue; // Skip invalid rows
            }

            $data = array_map('trim', $data);
            if (empty(array_filter($data))) {
                $skippedCount++;
                continue; // Skip empty rows
            }

            // Transform Plant Number (first column) to extract only the integer
            if (isset($data[0]) && strpos($data[0], 'Plant No. ') === 0) {
                $data[0] = (int)str_replace('Plant No. ', '', $data[0]);
            }

            $plantNumber = $data[0]; // First column is Plant Number
            $plantNumbers[] = $plantNumber;
            $rows[] = $data;

            // Process batch when it reaches the batch size
            if (count($rows) >= $batchSize) {
                // Check duplicates in batch
                $placeholders = str_repeat('?,', count($plantNumbers) - 1) . '?';
                $checkStmt = $mysqli->prepare("SELECT `" . trim($headers[0]) . "` FROM `$tableName` WHERE `" . trim($headers[0]) . "` IN ($placeholders)");
                if (!$checkStmt) {
                    throw new Exception("Prepare for duplicate check failed: " . $mysqli->error);
                }

                $types = str_repeat('i', count($plantNumbers));
                $checkStmt->bind_param($types, ...$plantNumbers);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();

                $existingPlantNumbers = [];
                while ($row = $checkResult->fetch_assoc()) {
                    $existingPlantNumbers[] = $row[trim($headers[0])];
                }
                $checkStmt->close();

                // Process each row in the batch
                foreach ($rows as $data) {
                    $plantNumber = $data[0];
                    if (in_array($plantNumber, $existingPlantNumbers)) {
                        $skippedCount++;
                        continue; // Skip duplicate
                    }

                    // Bind parameters with proper types (INT for Plant Number, strings for others)
                    $types = 'i' . str_repeat('s', count($data) - 1);
                    $stmt->bind_param($types, ...$data);
                    if (!$stmt->execute()) {
                        throw new Exception("Insert failed: " . $stmt->error);
                    }
                    $rowCount++;
                }

                // Clear batch
                $plantNumbers = [];
                $rows = [];
            }
        }

        // Process remaining rows (last batch)
        if (!empty($rows)) {
            // Check duplicates in batch
            $placeholders = str_repeat('?,', count($plantNumbers) - 1) . '?';
            $checkStmt = $mysqli->prepare("SELECT `" . trim($headers[0]) . "` FROM `$tableName` WHERE `" . trim($headers[0]) . "` IN ($placeholders)");
            if (!$checkStmt) {
                throw new Exception("Prepare for duplicate check failed: " . $mysqli->error);
            }

            $types = str_repeat('i', count($plantNumbers));
            $checkStmt->bind_param($types, ...$plantNumbers);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            $existingPlantNumbers = [];
            while ($row = $checkResult->fetch_assoc()) {
                $existingPlantNumbers[] = $row[trim($headers[0])];
            }
            $checkStmt->close();

            // Process each row in the batch
            foreach ($rows as $data) {
                $plantNumber = $data[0];
                if (in_array($plantNumber, $existingPlantNumbers)) {
                    $skippedCount++;
                    continue; // Skip duplicate
                }

                $types = 'i' . str_repeat('s', count($data) - 1);
                $stmt->bind_param($types, ...$data);
                if (!$stmt->execute()) {
                    throw new Exception("Insert failed: " . $stmt->error);
                }
                $rowCount++;
            }
        }

        if ($rowCount === 0 && $skippedCount === 0) {
            throw new Exception("No valid data rows found in CSV");
        }

        // Commit transaction
        $mysqli->commit();

        $result['success'] = true;
        $result['message'] = "Successfully processed $rowCount rows. Skipped $skippedCount rows (due to invalid data or duplicates).";
    } catch (Exception $e) {
        $mysqli->rollback();
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