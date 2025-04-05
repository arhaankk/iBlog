DROP TABLE IF EXISTS postComments;
DROP TABLE IF EXISTS postImages;
DROP TABLE IF EXISTS blog;
DROP TABLE IF EXISTS users;

CREATE TABLE if not exists users (
	id INT AUTO_INCREMENT PRIMARY KEY,		-- Unique identifier for each user
	firstname VARCHAR(50) NOT NULL,			-- Given name
	lastname VARCHAR(50) NOT NULL,			-- Family name
	username VARCHAR(50) NOT NULL UNIQUE,	-- Username of the user
	password VARCHAR(255) NOT NULL,			-- Hashed password of the user
	email VARCHAR(100) NOT NULL UNIQUE,		-- Email address of the user
    gender ENUM('male', 'female', 'other') NOT NULL,
    age INT NOT NULL CHECK (age BETWEEN 18 AND 150),
    profile_img LONGBLOB DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    admin BIT(1) DEFAULT 0,
    disabled BIT(1) DEFAULT 0
);

-- Insert 5 dummy users
INSERT INTO users (firstname, lastname, username, password, email, gender, age, admin) VALUES
	-- alice, alice123
	('Alice', 'Johnson', 'alice', '$2y$12$G/2JzuqkVdIgTkzlGGLuGuO6fZ3d6SMlDfDel71YMSfeaiR5XB2Wm', 'alice@example.com', 'female', 25, 1),
	-- bob, bob123
	('Bob', 'Smith', 'bob', '$2y$12$AbRAKIGd1ZLrt863qgzyHOKUZtqOHh3J3uaIVOpPTqThr7C7NHbTO', 'bob@example.com', 'male', 30, 0),
	-- charlie, charlie123
	('Charlie', 'Brown', 'charlie', '$2y$12$9b5fQJHQgO4KSL7vPUUig.koLEJmQLzVXGKXQjoukxNxfpoHMmthy', 'charlie@example.com', 'male',  22, 0),
	-- diana, diana123
	('Diana', 'Miller', 'diana', '$2y$12$uMpKtu91mGmIhZc7a.Xl4OXutZWpHKzmrLg3sLszRW4Uc/1tQN0K.', 'diana@example.com', 'female', 28, 0),
	-- eve, eve123
	('Eve', 'Davis', 'eve', '$2y$12$syRex.ljPVgq7s0dGIFB6uL2VEc1UfvgnzTHup7Rh02QDyQE4ycM6', 'eve@example.com', 'female', 26, 0);

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

CREATE TABLE IF NOT EXISTS postComments (
	id INT AUTO_INCREMENT PRIMARY KEY,
	content VARCHAR(512) NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	postId INT NOT NULL,
	userId INT NOT NULL,
	FOREIGN KEY (postId) REFERENCES blog(id) ON DELETE CASCADE,
	FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE
);
