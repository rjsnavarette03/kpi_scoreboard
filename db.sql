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
  efficiency FLOAT DEFAULT 0,
  quality FLOAT DEFAULT 0,
  schedule_adherence FLOAT DEFAULT 0,
  total_score FLOAT DEFAULT 0,
  grade VARCHAR(5),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample users (passwords not set). Use generate_hash.php to create password hashes and INSERT into users table.
-- Example:
-- INSERT INTO users (username, password, role) VALUES ('admin', '<hash_here>', 'admin');
-- INSERT INTO users (username, password, role) VALUES ('employee', '<hash_here>', 'employee');
