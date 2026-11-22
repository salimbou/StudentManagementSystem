CREATE DATABASE IF NOT EXISTS school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school;

CREATE TABLE IF NOT EXISTS students (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 age TINYINT UNSIGNED NULL,
 speciality VARCHAR(150) NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','user') NOT NULL DEFAULT 'user',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS attendance (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 student_id INT UNSIGNED NOT NULL,
 attendance_date DATE NOT NULL,
 status ENUM('present','absent','late','excused') NOT NULL,
 note VARCHAR(500) NULL,
 created_by INT UNSIGNED NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_student_date (student_id, attendance_date),
 CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
 CONSTRAINT fk_attendance_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_attendance_date (attendance_date)
) ENGINE=InnoDB;

INSERT IGNORE INTO students (name,email,age,speciality) VALUES
('Ahmed Benali','ahmed@example.com',22,'Informatique'),
('Sara Ali','sara@example.com',21,'Mathematiques'),
('Yacine Karim','yacine@example.com',23,'Gestion');
