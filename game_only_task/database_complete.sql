-- Script SQL complet pour la base de données nextgen_db
-- Combine toutes les tables existantes avec les nouvelles tables de réclamations
-- Base de données: nextgen_db

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================
-- TABLES EXISTANTES (structure de nextgen_db-2.sql)
-- ============================================

-- Table categorie
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo_profil` varchar(255) DEFAULT NULL,
  `credits` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table jeu
CREATE TABLE IF NOT EXISTS `jeu` (
  `id_jeu` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `src_img` varchar(500) DEFAULT NULL,
  `video_src` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `id_categorie` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_jeu`),
  KEY `id_categorie` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table livraisons
CREATE TABLE IF NOT EXISTS `livraisons` (
  `id_livraison` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_jeu` int(11) NOT NULL,
  `date_commande` datetime DEFAULT current_timestamp(),
  `adresse_complete` text NOT NULL,
  `position_lat` decimal(10,8) NOT NULL,
  `position_lng` decimal(11,8) NOT NULL,
  `mode_paiement` enum('credit_site','espece_livraison') NOT NULL DEFAULT 'credit_site',
  `prix_livraison` decimal(8,3) NOT NULL DEFAULT 8.000,
  `statut` enum('commandee','emballee','en_transit','livree') NOT NULL DEFAULT 'commandee',
  PRIMARY KEY (`id_livraison`),
  KEY `id_user` (`id_user`),
  KEY `id_jeu` (`id_jeu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table trajets
CREATE TABLE IF NOT EXISTS `trajets` (
  `id_trajet` int(11) NOT NULL AUTO_INCREMENT,
  `id_livraison` int(11) NOT NULL,
  `position_lat` decimal(10,8) NOT NULL,
  `position_lng` decimal(11,8) NOT NULL,
  `date_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_trajet`),
  KEY `id_livraison` (`id_livraison`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- NOUVELLES TABLES : RÉCLAMATIONS ET TRAITEMENTS
-- ============================================

-- Table reclamation
CREATE TABLE IF NOT EXISTS `reclamation` (
  `idReclamation` INT(11) NOT NULL AUTO_INCREMENT,
  `id_user` INT(11) DEFAULT NULL,
  `id_jeu` INT(11) DEFAULT NULL,
  `description` TEXT NOT NULL,
  `dateReclamation` DATETIME NOT NULL,
  `statut` VARCHAR(50) DEFAULT 'En attente',
  `type` VARCHAR(100) NOT NULL,
  `produitConcerne` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`idReclamation`),
  KEY `idx_id_user` (`id_user`),
  KEY `idx_id_jeu` (`id_jeu`),
  CONSTRAINT `fk_reclamation_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_reclamation_jeu` FOREIGN KEY (`id_jeu`) REFERENCES `jeu` (`id_jeu`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table traitement
CREATE TABLE IF NOT EXISTS `traitement` (
  `idTraitement` INT(11) NOT NULL AUTO_INCREMENT,
  `idReclamation` INT(11) NOT NULL,
  `id_user` INT(11) DEFAULT NULL,
  `contenu` TEXT NOT NULL,
  `dateReclamation` DATETIME NOT NULL,
  PRIMARY KEY (`idTraitement`),
  KEY `idx_idReclamation` (`idReclamation`),
  KEY `idx_id_user` (`id_user`),
  CONSTRAINT `fk_traitement_reclamation` FOREIGN KEY (`idReclamation`) REFERENCES `reclamation` (`idReclamation`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_traitement_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
