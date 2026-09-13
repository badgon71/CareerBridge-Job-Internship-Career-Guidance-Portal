CREATE DATABASE IF NOT EXISTS careerbridge
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE careerbridge;

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('student','employer','mentor','admin') NOT NULL,
  status ENUM('active','blocked') NOT NULL DEFAULT 'active',
  cv_file VARCHAR(255) NULL,
  company_name VARCHAR(150) NULL,
  industry VARCHAR(100) NULL,
  expertise VARCHAR(150) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT NULL,
  created_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_category_admin
    FOREIGN KEY (created_by) REFERENCES users(user_id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jobs (
  job_id INT AUTO_INCREMENT PRIMARY KEY,
  employer_id INT NOT NULL,
  category_id INT NOT NULL,
  job_title VARCHAR(150) NOT NULL,
  job_type VARCHAR(50) NOT NULL,
  location VARCHAR(100) NOT NULL,
  salary DECIMAL(10,2) NOT NULL DEFAULT 0,
  deadline DATE NOT NULL,
  description TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_job_employer
    FOREIGN KEY (employer_id) REFERENCES users(user_id)
    ON DELETE CASCADE,
  CONSTRAINT fk_job_category
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS applications (
  application_id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT NOT NULL,
  student_id INT NOT NULL,
  expected_salary DECIMAL(10,2) NULL,
  cover_note TEXT NOT NULL,
  status ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_student_job (student_id, job_id),
  CONSTRAINT fk_application_job
    FOREIGN KEY (job_id) REFERENCES jobs(job_id)
    ON DELETE CASCADE,
  CONSTRAINT fk_application_student
    FOREIGN KEY (student_id) REFERENCES users(user_id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS career_tips (
  tip_id INT AUTO_INCREMENT PRIMARY KEY,
  mentor_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  category VARCHAR(100) NOT NULL,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_tip_mentor
    FOREIGN KEY (mentor_id) REFERENCES users(user_id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- Demo accounts for local testing.
-- Passwords:
-- student@example.com  -> Student@123
-- employer@example.com -> Employer@123
-- mentor@example.com   -> Mentor@123
-- admin@example.com    -> Admin@123

INSERT IGNORE INTO users
(name,email,phone,password,role,status,company_name,industry,expertise)
VALUES
('Demo Student','student@example.com','01710000001',
'$2y$12$8WbCYoNVr8oAXckqK8VzWuxj5WPKDmzAlJtDMRD9jjaFIpryRJky.',
'student','active',NULL,NULL,NULL),

('Demo Employer','employer@example.com','01710000002',
'$2y$12$.Ok.EQ./zQxDgc5YTVMIj.gmLm5K18ASV2pby8CSn3GsHg2kPdy.u',
'employer','active','Orbit Soft','Software',NULL),

('Demo Mentor','mentor@example.com','01710000003',
'$2y$12$kygZflRee.eGawvnzeZ16uhBeG1RBGgTHtCPhdUVEg/Wtw4qAF2aq',
'mentor','active',NULL,NULL,'CV and Interview Preparation'),

('Demo Admin','admin@example.com','01710000004',
'$2y$12$D0pgAovo.OnF4JtqKIA.S.ovei9OWEhjHaqaIupYcQqcAJSCx4/2.',
'admin','active',NULL,NULL,NULL);

INSERT IGNORE INTO categories (category_id, category_name, description, created_by)
VALUES
(1,'Web Development','Web development jobs and internships',
 (SELECT user_id FROM users WHERE email='admin@example.com' LIMIT 1)),
(2,'Software Engineering','Software engineering roles',
 (SELECT user_id FROM users WHERE email='admin@example.com' LIMIT 1)),
(3,'Data Science','Data and machine learning roles',
 (SELECT user_id FROM users WHERE email='admin@example.com' LIMIT 1)),
(4,'UI/UX Design','Design roles',
 (SELECT user_id FROM users WHERE email='admin@example.com' LIMIT 1));
