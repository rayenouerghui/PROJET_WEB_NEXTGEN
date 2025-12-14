-- ============================================
-- NextGen Unified Database
-- Complete SQL dump for integrated application
-- ============================================
-- This file combines tables from:
-- - Base module (user, product, reclamation, livraison)
-- - Blog module (article, commentaire, categorie_article)
-- - Event module (evenement, categoriev, reservation)
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nextgen_db`
-- Create database if it doesn't exist
--
CREATE DATABASE IF NOT EXISTS `nextgen_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `nextgen_db`;

-- ============================================
-- BASE MODULE TABLES
-- ============================================

--
-- Table structure for table `users`
--
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
  `last_login` datetime DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_code` char(6) DEFAULT NULL,
  `verification_expires` datetime DEFAULT NULL,
  `statut` enum('actif','suspendu','banni') DEFAULT 'actif',
  `date_inscription` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `categorie` (Game categories)
--
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `jeu`
--
CREATE TABLE IF NOT EXISTS `jeu` (
  `id_jeu` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `src_img` varchar(500) DEFAULT NULL,
  `video_src` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `id_categorie` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_jeu`),
  KEY `id_categorie` (`id_categorie`),
  CONSTRAINT `jeu_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `jeux_owned`
--
CREATE TABLE IF NOT EXISTS `jeux_owned` (
  `owned_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `id_jeu` int(11) NOT NULL,
  `score` int(11) DEFAULT 0,
  `date_achat` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`owned_id`),
  UNIQUE KEY `unique_user_game` (`id`,`id_jeu`),
  KEY `id_jeu` (`id_jeu`),
  CONSTRAINT `jeux_owned_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jeux_owned_ibfk_2` FOREIGN KEY (`id_jeu`) REFERENCES `jeu` (`id_jeu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `livraisons`
--
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
  KEY `id_jeu` (`id_jeu`),
  CONSTRAINT `livraisons_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `livraisons_ibfk_2` FOREIGN KEY (`id_jeu`) REFERENCES `jeu` (`id_jeu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `trajets`
--
CREATE TABLE IF NOT EXISTS `trajets` (
  `id_trajet` int(11) NOT NULL AUTO_INCREMENT,
  `id_livraison` int(11) NOT NULL,
  `position_lat` decimal(10,8) NOT NULL,
  `position_lng` decimal(11,8) NOT NULL,
  `date_update` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `route_json` text DEFAULT NULL,
  `current_index` int(11) DEFAULT 0,
  PRIMARY KEY (`id_trajet`),
  KEY `id_livraison` (`id_livraison`),
  CONSTRAINT `trajets_ibfk_1` FOREIGN KEY (`id_livraison`) REFERENCES `livraisons` (`id_livraison`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `reclamation`
--
CREATE TABLE IF NOT EXISTS `reclamation` (
  `idReclamation` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_jeu` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `dateReclamation` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` varchar(50) NOT NULL DEFAULT 'en attente',
  `type` varchar(100) NOT NULL,
  `produitConcerne` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idReclamation`),
  KEY `id_user` (`id_user`),
  KEY `id_jeu` (`id_jeu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `traitement`
--
CREATE TABLE IF NOT EXISTS `traitement` (
  `idTraitement` int(11) NOT NULL AUTO_INCREMENT,
  `idReclamation` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `contenu` text NOT NULL,
  `dateReclamation` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idTraitement`),
  KEY `idReclamation` (`idReclamation`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `historique`
--
CREATE TABLE IF NOT EXISTS `historique` (
  `id_historique` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `type_action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_action` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_historique`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `historique_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `password_resets`
--
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `code` char(6) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiration` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- BLOG MODULE TABLES
-- ============================================

--
-- Table structure for table `categorie_article` (Blog article categories)
--
CREATE TABLE IF NOT EXISTS `categorie_article` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `article`
--
CREATE TABLE IF NOT EXISTS `article` (
  `id_article` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date_publication` datetime NOT NULL,
  `categorie` int(11) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `id_auteur` int(11) NOT NULL,
  `rating_count` int(11) DEFAULT 0,
  `rating_sum` int(11) DEFAULT 0,
  PRIMARY KEY (`id_article`),
  KEY `categorie` (`categorie`),
  KEY `id_auteur` (`id_auteur`),
  CONSTRAINT `article_ibfk_categorie` FOREIGN KEY (`categorie`) REFERENCES `categorie_article` (`id_categorie`) ON DELETE SET NULL,
  CONSTRAINT `article_ibfk_auteur` FOREIGN KEY (`id_auteur`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `commentaire`
--
CREATE TABLE IF NOT EXISTS `commentaire` (
  `id_commentaire` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) NOT NULL,
  `nom_visiteur` varchar(100) NOT NULL,
  `contenu` text NOT NULL,
  `date_commentaire` datetime NOT NULL DEFAULT current_timestamp(),
  `likes` int(11) DEFAULT 0,
  `id_parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_commentaire`),
  KEY `fk_id_article` (`id_article`),
  KEY `fk_commentaire_parent` (`id_parent`),
  CONSTRAINT `commentaire_ibfk_article` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`) ON DELETE CASCADE,
  CONSTRAINT `commentaire_ibfk_parent` FOREIGN KEY (`id_parent`) REFERENCES `commentaire` (`id_commentaire`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `article_rating`
--
CREATE TABLE IF NOT EXISTS `article_rating` (
  `id_rating` int(11) NOT NULL AUTO_INCREMENT,
  `id_article` int(11) NOT NULL,
  `user_identifier` varchar(255) NOT NULL,
  `rating_value` tinyint(1) NOT NULL CHECK (`rating_value` between 1 and 5),
  `rating_date` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_rating`),
  UNIQUE KEY `unique_rating` (`id_article`,`user_identifier`),
  CONSTRAINT `article_rating_ibfk` FOREIGN KEY (`id_article`) REFERENCES `article` (`id_article`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- EVENT MODULE TABLES
-- ============================================

--
-- Table structure for table `categoriev` (Event categories)
--
CREATE TABLE IF NOT EXISTS `categoriev` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `nom_categorie` varchar(100) NOT NULL,
  `description_categorie` text NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `evenement`
--
CREATE TABLE IF NOT EXISTS `evenement` (
  `id_evenement` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_evenement` date NOT NULL,
  `lieu` varchar(150) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  `places_disponibles` int(11) NOT NULL,
  PRIMARY KEY (`id_evenement`),
  KEY `id_categorie` (`id_categorie`),
  CONSTRAINT `evenement_ibfk_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categoriev` (`id_categorie`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `reservation`
--
CREATE TABLE IF NOT EXISTS `reservation` (
  `id_reservation` int(11) NOT NULL AUTO_INCREMENT,
  `id_evenement` int(11) NOT NULL,
  `nom_complet` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `nombre_places` int(11) NOT NULL,
  `message` text NOT NULL,
  `date_reservation` date NOT NULL,
  PRIMARY KEY (`id_reservation`),
  KEY `id_evenement` (`id_evenement`),
  CONSTRAINT `reservation_ibfk_evenement` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

