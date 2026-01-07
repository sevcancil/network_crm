CREATE DATABASE sirket_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sirket_db;

-- Kullanıcılar (Admin ve Personel)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'personel') NOT NULL DEFAULT 'personel',
    department ENUM('Mice', 'Turizm', 'Muhasebe', 'Genel', 'Yönetici') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Şirketler
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    logo VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    tax_no VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Şahıslar (Müşteriler)
CREATE TABLE persons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT,
    name VARCHAR(50) NOT NULL,
    surname VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    country VARCHAR(50),
    city VARCHAR(50),
    district VARCHAR(50),
    full_address TEXT,
    department ENUM('Mice', 'Turizm', 'Muhasebe', 'Genel', 'Yönetici') NOT NULL,
    created_by_user_id INT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (created_by_user_id) REFERENCES users(id)
);

-- İlk Admin Kullanıcısını Oluştur (Şifre: 123456)
-- Not: Gerçek sistemde bu hash'i kodla oluşturmalısın.
INSERT INTO users (username, password, role, department) VALUES 
('admin', '$2y$10$wS1.0/I7z9/Z.X.Y.U.K.u.Z.H.A.S.H.C.O.D.E', 'admin', 'Genel');