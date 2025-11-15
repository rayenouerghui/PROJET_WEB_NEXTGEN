-- ============================================================================
-- MODULE MATCHMAKING - Version de TEST LOCAL
-- Responsable: Rayen Ouerghui
-- 
-- ATTENTION: Ce fichier est UNIQUEMENT pour tester localement quand les 
-- tables des autres modules n'existent pas encore.
-- 
-- NE PAS partager ce fichier avec l'équipe !
-- Utiliser database.sql pour la version partagée.
-- ============================================================================

USE nextgen_db;

-- ============================================================================
-- CRÉER LES TABLES DES AUTRES MODULES (TEMPORAIRE - POUR TEST UNIQUEMENT)
-- Ces tables seront recréées par vos collègues avec leur structure complète
-- ============================================================================

-- Table utilisateurs (version minimale pour test)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('utilisateur', 'admin') DEFAULT 'utilisateur',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table jeux (version minimale pour test)
CREATE TABLE IF NOT EXISTS jeux (
    id_jeu INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    categorie VARCHAR(100),
    stock INT DEFAULT 0,
    image_url VARCHAR(255),
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table commandes (version minimale pour test)
CREATE TABLE IF NOT EXISTS commandes (
    id_commande INT PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INT NOT NULL,
    id_jeu INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'confirmee', 'livree', 'annulee') DEFAULT 'confirmee',
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_jeu) REFERENCES jeux(id_jeu) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- VOS TABLES DU MODULE MATCHMAKING
-- ============================================================================

-- Table: AttenteMatch
CREATE TABLE IF NOT EXISTS AttenteMatch (
    id_attente INT PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INT NOT NULL,
    id_jeu INT NOT NULL,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    matched BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_jeu) REFERENCES jeux(id_jeu) ON DELETE CASCADE,
    INDEX idx_jeu_matched (id_jeu, matched),
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_date_ajout (date_ajout),
    INDEX idx_utilisateur_jeu_matched (id_utilisateur, id_jeu, matched)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: SessionMatch
CREATE TABLE IF NOT EXISTS SessionMatch (
    id_session INT PRIMARY KEY AUTO_INCREMENT,
    id_jeu INT NOT NULL,
    lien_session VARCHAR(255) NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    participants TEXT NOT NULL COMMENT 'JSON array des IDs utilisateurs',
    statut ENUM('active', 'terminee', 'expiree') DEFAULT 'active',
    FOREIGN KEY (id_jeu) REFERENCES jeux(id_jeu) ON DELETE CASCADE,
    INDEX idx_jeu (id_jeu),
    INDEX idx_statut (statut),
    INDEX idx_date_creation (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DONNÉES DE TEST (LOCAL SEULEMENT)
-- ============================================================================

-- Utilisateurs de test
INSERT IGNORE INTO utilisateurs (id_utilisateur, email, nom, prenom, mot_de_passe, role) VALUES
(1, 'user1@test.com', 'Dupont', 'Jean', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'utilisateur'),
(2, 'user2@test.com', 'Martin', 'Marie', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'utilisateur'),
(3, 'admin@nextgen.com', 'Admin', 'NextGen', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Jeux de test
INSERT IGNORE INTO jeux (id_jeu, nom, description, prix, categorie, stock, image_url) VALUES
(1, 'Jungle Quest', 'Aventure dans la jungle', 29.99, 'Aventure', 100, 'https://via.placeholder.com/300x200?text=Jungle+Quest'),
(2, 'Space Warriors', 'Combat spatial épique', 39.99, 'Action', 100, 'https://via.placeholder.com/300x200?text=Space+Warriors'),
(3, 'Kingdom Builder', 'Construisez votre royaume', 34.99, 'Stratégie', 100, 'https://via.placeholder.com/300x200?text=Kingdom+Builder'),
(4, 'Epic RPG', 'RPG épique avec des combats intenses', 49.99, 'RPG', 100, 'https://via.placeholder.com/300x200?text=Epic+RPG');

-- Commandes de test (jeux achetés)
INSERT IGNORE INTO commandes (id_utilisateur, id_jeu, date_commande, statut) VALUES
(1, 1, NOW(), 'confirmee'),  -- User 1 a acheté Jungle Quest
(1, 2, NOW(), 'confirmee'),  -- User 1 a acheté Space Warriors
(1, 3, NOW(), 'confirmee'),  -- User 1 a acheté Kingdom Builder
(1, 4, NOW(), 'confirmee'),  -- User 1 a acheté Epic RPG
(2, 1, NOW(), 'confirmee'),  -- User 2 a acheté Jungle Quest
(2, 2, NOW(), 'confirmee');  -- User 2 a acheté Space Warriors

-- Note: Le mot de passe de test est "password" (hashé avec bcrypt)

