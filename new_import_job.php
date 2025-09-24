<?php
include "inc/module.inc.php";

function previewFiles() {
    var fileInput = document.getElementById('job_documents');
    var fileList = document.getElementById('file-list');
    var files = fileInput.files;
    
    if (files.length > 0) {
        fileList.style.display = 'block';
        var listHTML = '<h4 style="margin: 0 0 10px 0; color: #333;">Selected Files:</h4><ul style="margin: 0; padding-left: 20px;">';
        
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            
            listHTML += '<li style="margin-bottom: 5px;">' + file.name + ' <span style="color: #666;">(' + fileSize + ' MB)</span></li>';
            
            // Validate file size
            if (file.size > 10 * 1024 * 1024) {
                alert('File "' + file.name + '" is too large. Maximum size is 10MB.');
                fileInput.value = '';
                fileList.style.display = 'none';
                return false;
            }
        }
        
        listHTML += '</ul>';
        fileList.innerHTML = listHTML;
    } else {
        fileList.style.display = 'none';
    }
}

// File upload handling
function handleFileUploads($job_no) {
    $uploadDir = "uploads/jobs/" . $job_no . "/";
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $uploadedFiles = array();
    
    // Handle multiple file uploads
    if (isset($_FILES['job_documents']) && !empty($_FILES['job_documents']['name'][0])) {
        $fileCount = count($_FILES['job_documents']['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['job_documents']['error'][$i] == UPLOAD_ERR_OK) {
                $fileName = $_FILES['job_documents']['name'][$i];
                $fileSize = $_FILES['job_documents']['size'][$i];
                $fileTmp = $_FILES['job_documents']['tmp_name'][$i];
                $fileType = $_FILES['job_documents']['type'][$i];
                
                // Validate file size (max 10MB)
                if ($fileSize > 10 * 1024 * 1024) {
                    echo "<script>alert('File " . $fileName . " is too large. Maximum size is 10MB.');</script>";
                    continue;
                }
                
                // Validate file type
                $allowedTypes = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'txt');
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                if (!in_array($fileExtension, $allowedTypes)) {
                    echo "<script>alert('File type not allowed for " . $fileName . ". Allowed types: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF, TXT');</script>";
                    continue;
                }
                
                // Generate unique filename to prevent conflicts
                $uniqueFileName = time() . "_" . $i . "_" . $fileName;
                $uploadPath = $uploadDir . $uniqueFileName;
                
                if (move_uploaded_file($fileTmp, $uploadPath)) {
                    $uploadedFiles[] = array(
                        'original_name' => $fileName,
                        'stored_name' => $uniqueFileName,
                        'file_path' => $uploadPath,
                        'file_size' => $fileSize,
                        'file_type' => $fileType
                    );
                }
            }
        }
    }
    
    return $uploadedFiles;
}

// Save file information to database
function saveFileInfo($job_no, $uploadedFiles) {
    global $db;
    
    foreach ($uploadedFiles as $file) {
        $fileData = array(
            'job_no' => $job_no,
            'original_filename' => $file['original_name'],
            'stored_filename' => $file['stored_name'],
            'file_path' => $file['file_path'],
            'file_size' => $file['file_size'],
            'file_type' => $file['file_type'],
            'upload_date' => date("Y-m-d H:i:s", time() + 28800),
            'uploaded_by' => $_SESSION['user_id']
        );
        
        $db->_insert_data('cms_job_files', $fileData);
    }
}


if($_REQUEST['add'])
{
				$data = array(job_date=>$job_date,job_shipper=>$job_shipper,job_consignee=>$job_consignee,job_awb=>$job_awb,job_hawb=>$job_hawb,job_vf=>$job_vf,job_date_etd=>$job_date_etd,job_airport_arrival=>$job_airport_arrival,job_airport_departure=>$job_airport_departure,job_desc=>$job_desc,job_date_collected=>$job_date_collected,job_quantity=>$job_quantity,job_date_eta=>$job_date_eta,job_term=>$job_term,job_remark1=>$job_remark1,job_no=>$job_no,job_vf2=>$job_vf2,job_quantity2=>$job_quantity2,job_date_arrival=>$job_date_arrival,job_date_transfer=>$job_date_transfer,job_storage=>$job_storage,job_date_clearance=>$job_date_clearance,job_duty=>$job_duty,job_permit=>$job_permit,job_date_delivery=>$job_date_delivery,job_delivery_no=>$job_delivery_no,job_vehicle=>$job_vehicle,job_remark2=>$job_remark2,job_type=>$job_type,job_last_update=>$job_last_update,job_created_by=>$job_created_by);
			
				$db->_insert_data('cms_job',$data);
				
				// Handle file uploads after job is created
				$uploadedFiles = handleFileUploads($job_no);
				if (!empty($uploadedFiles)) {
					saveFileInfo($job_no, $uploadedFiles);
				}
				
				list($jobprevyear3, $jobprevmonth3, $jobno3) = split('[.]',$job_no);