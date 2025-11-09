-- KataWP Database Structure
-- This file is automatically imported on plugin activation

CREATE TABLE IF NOT EXISTS wp_daily_readings (
  id INT NOT NULL AUTO_INCREMENT,
  reading_date DATE NOT NULL,
  title VARCHAR(255),
  content LONGTEXT,
  epistle_text TEXT,
  gospel_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY reading_date (reading_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wp_synaxarium (
  id INT NOT NULL AUTO_INCREMENT,
  synax_date DATE,
  saint_name VARCHAR(255),
  description TEXT,
  feast_type VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY synax_date (synax_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wp_saints (
  id INT NOT NULL AUTO_INCREMENT,
  saint_name VARCHAR(255) NOT NULL,
  saint_name_ar VARCHAR(255),
  biography TEXT,
  feast_date VARCHAR(50),
  icon_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY saint_name (saint_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wp_epistle (
  id INT NOT NULL AUTO_INCREMENT,
  epistle_date DATE,
  title VARCHAR(255),
  text LONGTEXT,
  book VARCHAR(100),
  chapter_verse VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY epistle_date (epistle_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wp_gospel (
  id INT NOT NULL AUTO_INCREMENT,
  gospel_date DATE,
  title VARCHAR(255),
  text LONGTEXT,
  book VARCHAR(100),
  chapter_verse VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY gospel_date (gospel_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wp_liturgy (
  id INT NOT NULL AUTO_INCREMENT,
  liturgy_date DATE,
  title VARCHAR(255),
  content LONGTEXT,
  liturgy_type VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY liturgy_date (liturgy_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO wp_daily_readings (reading_date, title, content) VALUES
('2025-11-09', 'Today\'s Reading', 'Welcome to KataWP! This is a sample daily reading.');

INSERT INTO wp_saints (saint_name, saint_name_ar, biography) VALUES
('Saint George', 'القديس جورج', 'One of the most celebrated saints in the Christian tradition.');
