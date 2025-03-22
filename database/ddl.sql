CREATE DATABASE IF NOT EXISTS iblog;
USE iblog;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,   
    firstname VARCHAR(50) NOT NULL,     
    lastname VARCHAR(50) NOT NULL,    
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL,      
    gender ENUM('male', 'female', 'other') NOT NULL,  
    age INT NOT NULL CHECK (age BETWEEN 18 AND 150),  
    profile_img LONGBLOB DEFAULT NULL,        
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (firstname, lastname, username, email, password, gender, age, profile_img) VALUES
('Alice', 'Johnson', 'alice123', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'female', 25, NULL),
('Bob', 'Smith', 'bob123', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'male', 30, NULL),
('Charlie', 'Brown', 'charlie123', 'charlie@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 22, NULL),
('Diana', 'Miller', 'diana123','diana@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'female', 28, NULL),
('Eve', 'Davis', 'eve123','eve@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'female', 26, NULL);

CREATE TABLE IF NOT EXISTS blog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS postImages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,  
    image_data LONGBLOB NOT NULL,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog(id) ON DELETE CASCADE
);
