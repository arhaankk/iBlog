CREATE DATABASE IF NOT EXISTS iblog;
USE iblog;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,   
    first_name VARCHAR(50) NOT NULL,     
    last_name VARCHAR(50) NOT NULL,    
    email VARCHAR(100) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL,      
    gender ENUM('male', 'female', 'other') NOT NULL,  
    age INT NOT NULL CHECK (age BETWEEN 18 AND 150),  
    profile_img LONGBLOB DEFAULT NULL,        
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert 5 dummy users
INSERT INTO users (first_name, last_name, email, password, gender, age, profile_img) VALUES
('Alice', 'Johnson', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'female', 25, NULL),
('Bob', 'Smith', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'male', 30, NULL),
('Charlie', 'Brown', 'charlie@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'other', 22, NULL),
('Diana', 'Miller', 'diana@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'female', 28, NULL),
('Eve', 'Davis', 'eve@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'female', 26, NULL);

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
