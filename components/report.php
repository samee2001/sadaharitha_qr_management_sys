<div>
    <form action="functions/generate_report.php" method="post" target="_blank" class="p-4 border rounded bg-light" style="max-width: 500px; margin: 0 auto;">
        <h4 class="mb-3 text-success">Generate Issued Estate Report</h4>
        <div class="mb-3">
            <label for="date_from" class="form-label">Date From</label>
            <input type="date" class="form-control" id="date_from" name="date_from"  max="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="mb-3">
            <label for="date_to" class="form-label">Date To</label>
            <input type="date" class="form-control" id="date_to" name="date_to"  max="<?php echo date('Y-m-d'); ?>">
        </div>
        <button type="submit" class="btn btn-success w-100">Get Receipt</button>
    </form>
</div>