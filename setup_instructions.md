# File Upload Setup Instructions

## Quick Setup Guide

### 1. Database Setup
Run the SQL script to create the file storage table:
```sql
-- Run mysql_file_table.sql in your MySQL database
```

### 2. Directory Setup
Create the upload directory structure:
```bash
mkdir -p uploads/jobs
chmod 755 uploads/jobs
```

### 3. File Replacement
Replace your existing `new_import_job.php` with `enhanced_new_import_job.php`

### 4. File Management
Place the file management files in a `file_manager` directory:
- `view_job_files.php` - View uploaded files for a job
- `download_file.php` - Secure file download handler

## Features Added

✅ **Multiple File Upload** - Select and upload multiple documents at once
✅ **File Validation** - Automatic validation of file types and sizes
✅ **Real-time Preview** - See selected files before submission
✅ **Secure Storage** - Files stored in job-specific directories
✅ **Database Integration** - File metadata linked to job records
✅ **File Management** - View, download, and delete uploaded files

## Security Features

🔒 **File Type Restrictions** - Only allows: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF, TXT
🔒 **File Size Limits** - Maximum 10MB per file
🔒 **Unique Naming** - Prevents file conflicts and overwrites
🔒 **Secure Downloads** - Files served through PHP script with validation

## Usage

1. **Upload Files**: Use the "Document Uploads" section in the job form
2. **View Files**: Access through job details or use `view_job_files.php?job_no=JOB_NUMBER`
3. **Download Files**: Click download button in file list
4. **Delete Files**: Use delete button with confirmation

## File Structure
```
your_project/
├── enhanced_new_import_job.php (replaces new_import_job.php)
├── mysql_file_table.sql
├── file_manager/
│   ├── view_job_files.php
│   └── download_file.php
└── uploads/
    └── jobs/
        └── [job_no]/
            └── [uploaded_files]
```

## PHP Configuration
Ensure your PHP settings allow file uploads:
```ini
file_uploads = On
upload_max_filesize = 10M
post_max_size = 50M
max_file_uploads = 20
```

## Troubleshooting

**Upload not working?**
- Check directory permissions (755 or 777 for uploads/jobs/)
- Verify PHP upload settings
- Check error logs for specific issues

**Files not displaying?**
- Ensure database table was created successfully
- Check file paths in database records
- Verify include paths in file_manager scripts

**Download issues?**
- Check if physical files exist in upload directory
- Verify file permissions
- Ensure proper headers are being sent