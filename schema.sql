-- Database Schema for Portfolio Website

-- Main settings table for general info like name, tagline, etc.
CREATE TABLE settings (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  site_title VARCHAR(255) NOT NULL,
  tagline VARCHAR(255),
  owner_name VARCHAR(255),
  owner_photo VARCHAR(255)
);

-- About Me section
CREATE TABLE about (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  bio TEXT,
  philosophy TEXT,
  video_url VARCHAR(255)
);

-- Education section
CREATE TABLE education (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  year VARCHAR(50) NOT NULL,
  degree VARCHAR(255) NOT NULL,
  institution VARCHAR(255) NOT NULL,
  description TEXT
);

-- Experience section
CREATE TABLE experience (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  year_range VARCHAR(50) NOT NULL,
  position VARCHAR(255) NOT NULL,
  institution VARCHAR(255) NOT NULL,
  description TEXT
);

-- Skills section (categorized)
CREATE TABLE skills (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  category ENUM('soft', 'hard') NOT NULL,
  level INT -- Optional: for progress bars (e.g., 1-100)
);

-- Projects section
CREATE TABLE projects (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  image_album TEXT, -- Comma-separated list of image filenames
  external_link VARCHAR(255)
);

-- Testimonials section
CREATE TABLE testimonials (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  quote TEXT NOT NULL,
  author VARCHAR(255) NOT NULL,
  author_role VARCHAR(255)
);

-- Downloads section
CREATE TABLE downloads (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  filename VARCHAR(255) NOT NULL,
  display_name VARCHAR(255) NOT NULL,
  password VARCHAR(255), -- Hashed password
  download_count INT DEFAULT 0
);

-- Contact messages
CREATE TABLE messages (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin user
CREATE TABLE admin (
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL -- Hashed password
);