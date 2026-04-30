CREATE DATABASE IF NOT EXISTS ecall_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecall_db;

-- Table principale users
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('admin','agent','client') NOT NULL DEFAULT 'client',
    telephone   VARCHAR(20)   DEFAULT NULL,
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table tickets
CREATE TABLE IF NOT EXISTS tickets (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    client_id   INT NOT NULL,
    agent_id    INT DEFAULT NULL,
    sujet       VARCHAR(255) NOT NULL,
    statut      ENUM('ouvert','en_cours','ferme') DEFAULT 'ouvert',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (agent_id)  REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Insertion de l'admin (mot de passe: azerty)
INSERT INTO users (nom, email, password, role) VALUES 
('Administrateur', 'admin@gmail.com', '$2y$10$YourHashHere', 'admin');