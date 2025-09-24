<?php
/**
 * View and manage uploaded files for a specific job
 * This file displays all uploaded documents for a job and allows downloading
 */

include "inc/module.inc.php";

$job_no = $_GET['job_no'] ?? '';

if (empty($job_no)) {
    echo "<script>alert('Job number is required'); history.back();</script>";
    exit;
}

// Handle file deletion
if (isset($_POST['delete_file'])) {
    $file_id = $_POST['file_id'];
    
    // Get file info before deletion
    $sql = "SELECT * FROM cms_job_files WHERE file_id = '$file_id' AND job_no = '$job_no'";
    $result = mysql_query($sql);
    $file = mysql_fetch_object($result);
    
    if ($file) {
        // Delete physical file
        if (file_exists($file->file_path)) {
            unlink($file->file_path);
        }
        
        // Delete database record
        $db->_delete_data('cms_job_files', 'file_id', $file_id);
        
        echo "<script>alert('File deleted successfully'); window.location.reload();</script>";
    }
}

// Get job files
$sql = "SELECT * FROM cms_job_files WHERE job_no = '$job_no' AND is_active = 1 ORDER BY upload_date DESC";
$result = mysql_query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Files - <?php echo $job_no; ?></title>
    <link rel="stylesheet" href="main.css" type="text/css">
    <style>
        .file-container {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .file-info {
            flex-grow: 1;
        }
        .file-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
        }
        .btn-download {
            background: #4CAF50;
            color: white;
        }
        .btn-delete {
            background: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body bgcolor="#FFFFFF">
    <div style="padding: 20px;">
        <h2>Uploaded Files for Job: <?php echo $job_no; ?></h2>
        
        <?php if (mysql_num_rows($result) > 0): ?>
            <div class="file-container">
                <?php while ($file = mysql_fetch_object($result)): ?>
                    <div class="file-item">
                        <div class="file-info">
                            <strong><?php echo htmlspecialchars($file->original_filename); ?></strong><br>
                            <small>
                                Size: <?php echo number_format($file->file_size / 1024, 2); ?> KB | 
                                Uploaded: <?php echo $file->upload_date; ?> | 
                                Type: <?php echo $file->file_type; ?>
                            </small>
                        </div>
                        <div class="file-actions">
                            <a href="download_file.php?file_id=<?php echo $file->file_id; ?>" 
                               class="btn btn-download" target="_blank">Download</a>
                            <form method="post" style="display: inline;" 
                                  onsubmit="return confirm('Are you sure you want to delete this file?');">
                                <input type="hidden" name="file_id" value="<?php echo $file->file_id; ?>">
                                <button type="submit" name="delete_file" class="btn btn-delete">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No files uploaded for this job.</p>
        <?php endif; ?>
        
        <br>
        <a href="javascript:history.back();" class="btn" style="background: #008CBA; color: white; padding: 10px 20px;">Back to Job</a>
    </div>
</body>
</html>