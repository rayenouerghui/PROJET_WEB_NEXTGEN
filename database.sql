-- Script de création de la base de données pour NextGen Events
-- Base de données: playforchange

-- Table des catégories
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(255) NOT NULL,
  `description_categorie` text,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des événements
CREATE TABLE IF NOT EXISTS `evenement` (
  `id_evenement` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text,
  `date_evenement` date NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  `places_disponibles` int(11) DEFAULT 0,
  PRIMARY KEY (`id_evenement`),
  KEY `id_categorie` (`id_categorie`),
  CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des réservations
CREATE TABLE IF NOT EXISTS `reservation` (
  `id_reservation` int(11) NOT NULL AUTO_INCREMENT,
  `id_evenement` int(11) NOT NULL,
  `nom_complet` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `nombre_places` int(11) NOT NULL,
  `points_generes` int(11) NOT NULL DEFAULT 0,
  `message` text,
  `date_reservation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reservation`),
  KEY `id_evenement` (`id_evenement`),
  CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion de données de test (optionnel)
INSERT INTO `categorie` (`nom_categorie`, `description_categorie`) VALUES
('Technologie', 'Événements liés à la technologie et l\'innovation'),
('Art & Culture', 'Événements artistiques et culturels'),
('Sport', 'Événements sportifs et activités physiques'),
('Business', 'Événements professionnels et entrepreneuriat');

-- If you are updating an existing database, run the following to add the new column:
-- ALTER TABLE reservation ADD COLUMN points_generes INT(11) NOT NULL DEFAULT 0;

