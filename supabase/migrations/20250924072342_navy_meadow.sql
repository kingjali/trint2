-- Create table for storing job file information
-- Compatible with MySQL 5.0+ databases
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add indexes for better performance
CREATE INDEX idx_job_no ON cms_job_files(job_no);
CREATE INDEX idx_upload_date ON cms_job_files(upload_date);
CREATE INDEX idx_uploaded_by ON cms_job_files(uploaded_by);

-- Note: Foreign key constraint is optional - uncomment if your cms_job table exists
-- ALTER TABLE cms_job_files ADD CONSTRAINT fk_job_no 
-- FOREIGN KEY (job_no) REFERENCES cms_job(job_no) ON DELETE CASCADE;