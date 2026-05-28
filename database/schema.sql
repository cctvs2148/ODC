CREATE DATABASE IF NOT EXISTS odc_catering DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE odc_catering;

CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS managers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS placement_heads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS hoteliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS hotels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  address TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vacancies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hotel_id INT NOT NULL,
  duty_date DATE NOT NULL,
  shift_type ENUM('FN','AN') NOT NULL,
  total_vacancies INT NOT NULL DEFAULT 0,
  available_vacancies INT NOT NULL DEFAULT 0,
  reporting_time VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS applications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  vacancy_id INT NOT NULL,
  apply_date DATETIME NOT NULL,
  shift_type ENUM('FN','AN') NOT NULL,
  manager_status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  hotel_status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  final_status ENUM('pending','confirmed','rejected') NOT NULL DEFAULT 'pending',
  manager_remarks TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (vacancy_id) REFERENCES vacancies(id) ON DELETE CASCADE
);

INSERT INTO admin (name, email, password) VALUES
('ODC Admin', 'admin@odc.local', '$2y$10$CVo1jYnMwHbEbxcg2kdMU.dschjmqS66hqRIynuDzO1GVkyOBSc/y');

INSERT INTO students (name, email, password) VALUES
('Alice Kumar', 'alice@odc.local', '$2y$10$tTtTCwilfpT.amHWOg8sGOOYPyU0xaF0T77YzueIdXTw/Qnbaqyam'),
('Bhavya Singh', 'bhavya@odc.local', '$2y$10$tTtTCwilfpT.amHWOg8sGOOYPyU0xaF0T77YzueIdXTw/Qnbaqyam');

INSERT INTO managers (name, email, password) VALUES
('Manager One', 'manager@odc.local', '$2y$10$lbrmUxuESVcpexOCNAm1/uEv4aaaLDHEnF4pM.3c2cFsGH9Wd2YX2');

INSERT INTO placement_heads (name, email, password) VALUES
('Placement Head', 'placement@odc.local', '$2y$10$H6PBubErkV28f5D/SQFbHOP3MRf27zIArYgQRRsBP7WZZpftiszUe');

INSERT INTO hoteliers (name, email, password) VALUES
('Hotel Supervisor', 'hotelier@odc.local', '$2y$10$4WTiImn8XP9KclziM2EAQux5EQZay2nTh7/KBprsTBSAVLaiewO3y');

INSERT INTO hotels (name, address) VALUES
('Ocean View Hotel', 'Near Main Campus Road, ODC City'),
('Sunrise Guest House', 'Central Campus Area, ODC City');

INSERT INTO vacancies (hotel_id, duty_date, shift_type, total_vacancies, available_vacancies, reporting_time) VALUES
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'FN', 5, 5, '08:00 AM'),
(1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'AN', 4, 4, '02:00 PM'),
(2, DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'FN', 6, 6, '08:30 AM');

INSERT INTO applications (student_id, vacancy_id, apply_date, shift_type, manager_status, hotel_status, final_status, manager_remarks) VALUES
(1, 1, NOW(), 'FN', 'pending', 'pending', 'pending', NULL);
