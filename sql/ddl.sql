

CREATE TABLE if not exists users (
                       id INT AUTO_INCREMENT PRIMARY KEY,       -- Unique identifier for each user
                       username VARCHAR(50) NOT NULL UNIQUE,   -- Username of the user
                       password VARCHAR(255) NOT NULL,         -- Hashed password of the user
                       email VARCHAR(100) NOT NULL UNIQUE      -- Email address of the use
);
-- Insert 5 dummy users
INSERT INTO users (username, password, email) VALUES
                                                  ('alice', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'alice@example.com'),
                                                  ('bob', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'bob@example.com'),
                                                  ('charlie', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'charlie@example.com'),
                                                  ('diana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'diana@example.com'),
                                                  ('eve', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eve@example.com');

CREATE TABLE IF NOT EXISTS blog (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    title VARCHAR(255) NOT NULL,
                                    content TEXT NOT NULL,
                                    userId INT NOT NULL,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                    FOREIGN KEY (userId) REFERENCES users(id)
);


CREATE TABLE IF NOT EXISTS postImages (
                                      id INT AUTO_INCREMENT PRIMARY KEY,
                                      postId INT NOT NULL, -- Foreign key to link images to a blog post
                                      imageData LONGBLOB NOT NULL, -- Store the image as a BLOB
                                      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                      FOREIGN KEY (postId) REFERENCES blog(id) ON DELETE CASCADE
);
