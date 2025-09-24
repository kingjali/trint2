# File Upload Functionality Documentation

## Overview
This enhancement adds comprehensive file upload functionality to the import job creation form, allowing users to attach relevant documents to each job.

## Features Added

### 1. File Upload Form Field
- Multiple file selection support
- File type validation (PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF, TXT)
- File size validation (maximum 10MB per file)
- Real-time file preview before submission

### 2. Server-Side Processing
- Secure file handling with validation
- Unique filename generation to prevent conflicts
- Organized file storage in job-specific directories
- Database metadata storage for uploaded files

### 3. File Management
- View all uploaded files for a job
- Download files securely
- Delete files with confirmation
- File information display (size, type, upload date)

## Database Schema

### New Table: `cms_job_files`
```sql
CREATE TABLE cms_job_files (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    job_no VARCHAR(50) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(100),
    upload_date DATETIME NOT NULL,
    uploaded_by VARCHAR(50),
    file_description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## File Structure
```
uploads/
└── jobs/
    └── [job_no]/
        └── [uploaded_files]
```

## Security Features

### File Validation
- **File Type Restriction**: Only allows specific file types
- **File Size Limit**: Maximum 10MB per file
- **Filename Sanitization**: Prevents directory traversal attacks
- **Unique Naming**: Prevents file conflicts and overwrites

### Access Control
- Files are stored outside web root when possible
- Download access through controlled PHP script
- User session validation for file operations

## Usage Instructions

### For Users
1. **Uploading Files**: 
   - Click "Choose Files" in the Document Uploads section
   - Select one or multiple files
   - Preview shows selected files with sizes
   - Submit the form to save job and upload files

2. **Viewing Files**:
   - Access through job details page
   - See all uploaded documents with metadata
   - Download or delete files as needed

### For Administrators
1. **Setup Requirements**:
   - Create `uploads/jobs/` directory with write permissions
   - Run the SQL script to create the `cms_job_files` table
   - Ensure PHP file upload settings are configured appropriately

2. **Configuration**:
   - Adjust `upload_max_filesize` and `post_max_size` in php.ini
   - Set appropriate directory permissions (755 recommended)
   - Configure file type restrictions as needed

## Integration Points

### Form Integration
- Added to the existing import job form
- Maintains all existing functionality
- Files are processed after successful job creation

### Database Integration
- Links to existing `cms_job` table via `job_no`
- Maintains referential integrity with foreign key constraints
- Supports soft deletion with `is_active` flag

## Error Handling
- Client-side validation for immediate feedback
- Server-side validation for security
- Graceful error messages for users
- Logging capabilities for administrators

## Future Enhancements
- File categorization (Invoice, Packing List, etc.)
- Image thumbnail generation
- File versioning support
- Bulk file operations
- Integration with cloud storage services

## Maintenance
- Regular cleanup of orphaned files
- Database optimization for file queries
- Backup procedures for uploaded files
- Monitoring of storage usage