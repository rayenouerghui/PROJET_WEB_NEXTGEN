USE nextgen_db;

CREATE TABLE IF NOT EXISTS AttenteMatch (
    id_attente INT PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INT NOT NULL,
    id_jeu INT NOT NULL,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    matched BOOLEAN DEFAULT FALSE,
    INDEX idx_jeu_matched (id_jeu, matched),
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_date_ajout (date_ajout),
    INDEX idx_utilisateur_jeu_matched (id_utilisateur, id_jeu, matched)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS SessionMatch (
    id_session INT PRIMARY KEY AUTO_INCREMENT,
    id_jeu INT NOT NULL,
    lien_session VARCHAR(255) NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    participants TEXT NOT NULL,
    statut ENUM('active', 'terminee', 'annulee', 'expiree') DEFAULT 'active',
    INDEX idx_jeu (id_jeu),
    INDEX idx_statut (statut),
    INDEX idx_date_creation (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
