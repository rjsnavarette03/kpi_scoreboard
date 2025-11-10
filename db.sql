-- Database schema for KPI Scoreboard
CREATE DATABASE IF NOT EXISTS `u150718207_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `u150718207_db`;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','employee') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kpi_scores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  productivity FLOAT DEFAULT 0,
  productivity_desc TEXT,
  efficiency FLOAT DEFAULT 0,
  efficiency_desc TEXT,
  quality FLOAT DEFAULT 0,
  quality_desc TEXT,
  attendance FLOAT DEFAULT 0,
  attendance_desc TEXT,
  tardiness FLOAT DEFAULT 0,
  tardiness_desc TEXT,
  undertime FLOAT DEFAULT 0,
  undertime_desc TEXT,
  schedule_adherence FLOAT DEFAULT 0,
  total_score FLOAT DEFAULT 0,
  grade VARCHAR(5),
  month DATE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  -- prevent duplicate KPI per user+month
  UNIQUE KEY uq_user_month (user_id, month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample users (passwords not set). Use generate_hash.php to create password hashes and INSERT into users table.
-- Example:
-- INSERT INTO users (username, password, role) VALUES ('admin', '<hash_here>', 'admin');
-- INSERT INTO users (username, password, role) VALUES ('employee', '<hash_here>', 'employee');
