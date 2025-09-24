-- Create table for storing job file information
-- This table will store metadata about uploaded files for each job

CREATE TABLE IF NOT EXISTS cms_job_files (
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_job_no (job_no),
    INDEX idx_upload_date (upload_date),
    INDEX idx_uploaded_by (uploaded_by),
    
    FOREIGN KEY (job_no) REFERENCES cms_job(job_no) ON DELETE CASCADE
);

-- Create uploads directory structure (you'll need to create these directories manually)
-- uploads/
-- └── jobs/
--     └── [job_no]/
--         └── [uploaded_files]