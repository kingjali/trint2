<?php
/**
 * File download handler
 * Securely serves uploaded files for download
 */

include "inc/module.inc.php";

$file_id = $_GET['file_id'] ?? '';

if (empty($file_id)) {
    echo "<script>alert('File ID is required'); history.back();</script>";
    exit;
}

// Get file information
$sql = "SELECT * FROM cms_job_files WHERE file_id = '$file_id' AND is_active = 1";
$result = mysql_query($sql);
$file = mysql_fetch_object($result);

if (!$file) {
    echo "<script>alert('File not found'); history.back();</script>";
    exit;
}

// Check if file exists
if (!file_exists($file->file_path)) {
    echo "<script>alert('Physical file not found'); history.back();</script>";
    exit;
}

// Set headers for file download
header('Content-Type: ' . $file->file_type);
header('Content-Disposition: attachment; filename="' . $file->original_filename . '"');
header('Content-Length: ' . $file->file_size);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Output file content
readfile($file->file_path);
exit;
?>