-- Création de la base de données
CREATE DATABASE IF NOT EXISTS resultats_exam CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE resultats_exam;

-- Table des administrateurs
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT NOW()
);

-- Table des publications de résultats
CREATE TABLE IF NOT EXISTS publications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    fichier_nom VARCHAR(255) NOT NULL,
    fichier_type VARCHAR(50) NOT NULL,
    date_publication DATETIME DEFAULT NOW()
);

-- Admin par défaut : admin / admin123
-- Remplacez ce mot de passe après la première connexion
INSERT INTO admins (username, password_hash)
SELECT 'admin', '$2y$10$rFBJ0PMN7wn9P0P3feFlBuaRrG2nuznHLLcX8HnTL3xYEfMJJeLXS'
WHERE NOT EXISTS (SELECT 1 FROM admins WHERE username = 'admin');
