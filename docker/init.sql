-- This file runs automatically the FIRST time the MySQL container starts.
-- It creates all tables so the app works immediately with no manual setup.

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  UNIQUE NOT NULL,
    email      VARCHAR(100) UNIQUE NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notes (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    title   VARCHAR(255),
    file    VARCHAR(255),
    user_id INT DEFAULT NULL
);
