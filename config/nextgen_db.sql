-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 15 nov. 2025 à 21:17
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `nextgen_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `attentematch`
--

CREATE TABLE `attentematch` (
  `id_attente` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_jeu` int(11) NOT NULL,
  `date_ajout` datetime NOT NULL DEFAULT current_timestamp(),
  `matched` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `historique`
--

CREATE TABLE `historique` (
  `id_historique` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `type_action` enum('connexion','deconnexion','achat','partie_lancee','partie_terminee') NOT NULL,
  `description` text DEFAULT NULL,
  `donnees_action` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`donnees_action`)),
  `date_action` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jeu`
--

CREATE TABLE `jeu` (
  `id_jeu` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `src_img` varchar(500) DEFAULT NULL,
  `est_gratuit` tinyint(1) DEFAULT 0,
  `lien_externe` varchar(500) DEFAULT NULL,
  `id_categorie` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jeu_achete`
--

CREATE TABLE `jeu_achete` (
  `id_achat` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jeu_id` int(11) NOT NULL,
  `date_achat` datetime DEFAULT current_timestamp(),
  `label` varchar(50) DEFAULT NULL,
  `heures_jouees` decimal(8,2) DEFAULT 0.00,
  `note` tinyint(4) DEFAULT NULL CHECK (`note` between 1 and 5),
  `derniere_connexion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessionmatch`
--

CREATE TABLE `sessionmatch` (
  `id_session` int(11) NOT NULL,
  `id_jeu` int(11) NOT NULL,
  `lien_session` varchar(255) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `participants` text NOT NULL,
  `statut` enum('active','terminee','annulee','expiree') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_user` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `statut` enum('actif','inactif','banni') DEFAULT 'actif',
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `attentematch`
--
ALTER TABLE `attentematch`
  ADD PRIMARY KEY (`id_attente`),
  ADD KEY `idx_jeu_matched` (`id_jeu`,`matched`),
  ADD KEY `idx_utilisateur` (`id_user`),
  ADD KEY `idx_date_ajout` (`date_ajout`),
  ADD KEY `idx_utilisateur_jeu_matched` (`id_user`,`id_jeu`,`matched`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id_historique`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `jeu`
--
ALTER TABLE `jeu`
  ADD PRIMARY KEY (`id_jeu`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `jeu_achete`
--
ALTER TABLE `jeu_achete`
  ADD PRIMARY KEY (`id_achat`),
  ADD UNIQUE KEY `unique_user_jeu` (`user_id`,`jeu_id`),
  ADD KEY `jeu_id` (`jeu_id`);

--
-- Index pour la table `sessionmatch`
--
ALTER TABLE `sessionmatch`
  ADD PRIMARY KEY (`id_session`),
  ADD KEY `idx_jeu` (`id_jeu`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_date_creation` (`date_creation`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `attentematch`
--
ALTER TABLE `attentematch`
  MODIFY `id_attente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `id_historique` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jeu`
--
ALTER TABLE `jeu`
  MODIFY `id_jeu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jeu_achete`
--
ALTER TABLE `jeu_achete`
  MODIFY `id_achat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sessionmatch`
--
ALTER TABLE `sessionmatch`
  MODIFY `id_session` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `attentematch`
--
ALTER TABLE `attentematch`
  ADD CONSTRAINT `fk_attentematch_jeu` FOREIGN KEY (`id_jeu`) REFERENCES `jeu` (`id_jeu`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attentematch_user` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `historique`
--
ALTER TABLE `historique`
  ADD CONSTRAINT `historique_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `jeu`
--
ALTER TABLE `jeu`
  ADD CONSTRAINT `jeu_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE SET NULL;

--
-- Contraintes pour la table `jeu_achete`
--
ALTER TABLE `jeu_achete`
  ADD CONSTRAINT `jeu_achete_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `jeu_achete_ibfk_2` FOREIGN KEY (`jeu_id`) REFERENCES `jeu` (`id_jeu`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sessionmatch`
--
ALTER TABLE `sessionmatch`
  ADD CONSTRAINT `fk_sessionmatch_jeu` FOREIGN KEY (`id_jeu`) REFERENCES `jeu` (`id_jeu`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------
-- DONNÉES DE TEST POUR LE MATCHMAKING
-- Ces inserts servent uniquement à tester rapidement la fonctionnalité.
-- Vous pouvez les supprimer ou les adapter selon vos besoins.

-- Utilisateurs de test
INSERT INTO `utilisateur` (`id_user`, `email`, `mot_de_passe`, `nom`, `prenom`, `statut`, `role`)
VALUES
  (1, 'test1@test.com', 'password1', 'Test', 'User1', 'actif', 'user'),
  (2, 'test2@test.com', 'password2', 'Test', 'User2', 'actif', 'user')
ON DUPLICATE KEY UPDATE
  email = VALUES(`email`);

-- Jeu de test
INSERT INTO `jeu` (`id_jeu`, `titre`, `prix`, `src_img`, `est_gratuit`, `lien_externe`, `id_categorie`)
VALUES
  (1, 'Test Game', 19.99, NULL, 0, NULL, NULL)
ON DUPLICATE KEY UPDATE
  titre = VALUES(`titre`);

-- Achats de test : les deux utilisateurs possèdent le jeu 1
INSERT INTO `jeu_achete` (`id_achat`, `user_id`, `jeu_id`, `label`, `heures_jouees`)
VALUES
  (1, 1, 1, 'achat_test', 0.00),
  (2, 2, 1, 'achat_test', 0.00)
ON DUPLICATE KEY UPDATE
  label = VALUES(`label`);
