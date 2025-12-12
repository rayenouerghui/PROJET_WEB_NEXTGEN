-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 20 nov. 2025 à 17:55
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
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(100) NOT NULL,
  `description_categorie` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `id_evenement` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_evenement` date NOT NULL,
  `lieu` varchar(150) NOT NULL,
  `id_categorie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
-- Structure de la table `jeu_achete`
--

CREATE TABLE `jeu_achete` (
  `id_achat` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `jeu_id` int(11) NOT NULL,
  `numero_commande` varchar(30) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` enum('payée','expédiée','livrée','annulée') NOT NULL DEFAULT 'payée',
  `date_achat` datetime NOT NULL DEFAULT current_timestamp(),
  `label` varchar(50) DEFAULT NULL,
  `demiere_connexion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jeu_categorie`
--

CREATE TABLE `jeu_categorie` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jeu_jeu`
--

CREATE TABLE `jeu_jeu` (
  `id_jeu` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `src_img` varchar(500) DEFAULT NULL,
  `id_categorie` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `livraison`
--

CREATE TABLE `livraison` (
  `id_livraison` int(10) UNSIGNED NOT NULL,
  `id_commande` int(10) UNSIGNED NOT NULL,
  `adresse_complete` varchar(255) NOT NULL,
  `ville` varchar(120) NOT NULL,
  `code_postal` varchar(20) NOT NULL,
  `date_livraison` date NOT NULL,
  `mode_livraison` enum('standard','express','super_fast') NOT NULL,
  `prix_livraison` decimal(10,2) NOT NULL,
  `transport_type` enum('camion','fourgon','moto','velo','drone') DEFAULT NULL,
  `statut` enum('preparée','en_route','livrée','annulée') DEFAULT 'preparée',
  `notes_client` text DEFAULT NULL,
  `position_lat` decimal(10,7) DEFAULT NULL,
  `position_lng` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id_reservation` int(11) NOT NULL,
  `id_evenement` int(11) NOT NULL,
  `nom_complet` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `nombre_places` int(11) NOT NULL,
  `message` text NOT NULL,
  `date_reservation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `trajet`
--

CREATE TABLE `trajet` (
  `id_trajet` int(10) UNSIGNED NOT NULL,
  `id_livraison` int(10) UNSIGNED NOT NULL,
  `fournisseur_api` varchar(80) NOT NULL,
  `identifiant_suivi` varchar(120) NOT NULL,
  `statut_realtime` varchar(80) NOT NULL,
  `position_lat` decimal(10,7) DEFAULT NULL,
  `position_lng` decimal(10,7) DEFAULT NULL,
  `derniere_mise_a_jour` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `role` enum('user','admin') DEFAULT 'user',
  `photo_profile` varchar(255) DEFAULT NULL,
  `credit` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id_evenement`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id_historique`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `jeu_achete`
--
ALTER TABLE `jeu_achete`
  ADD PRIMARY KEY (`id_achat`),
  ADD KEY `jeu_id` (`jeu_id`),
  ADD KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `numero_commande` (`numero_commande`);

--
-- Index pour la table `jeu_categorie`
--
ALTER TABLE `jeu_categorie`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `jeu_jeu`
--
ALTER TABLE `jeu_jeu`
  ADD PRIMARY KEY (`id_jeu`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `livraison`
--
ALTER TABLE `livraison`
  ADD PRIMARY KEY (`id_livraison`),
  ADD KEY `idx_id_commande` (`id_commande`),
  ADD KEY `idx_date_livraison` (`date_livraison`),
  ADD KEY `idx_statut` (`statut`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id_reservation`),
  ADD KEY `id_evenement` (`id_evenement`);

--
-- Index pour la table `trajet`
--
ALTER TABLE `trajet`
  ADD PRIMARY KEY (`id_trajet`),
  ADD KEY `id_livraison` (`id_livraison`),
  ADD KEY `idx_identifiant_suivi` (`identifiant_suivi`),
  ADD KEY `idx_derniere_mise_a_jour` (`derniere_mise_a_jour`);

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
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `id_historique` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jeu_achete`
--
ALTER TABLE `jeu_achete`
  MODIFY `id_achat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jeu_categorie`
--
ALTER TABLE `jeu_categorie`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jeu_jeu`
--
ALTER TABLE `jeu_jeu`
  MODIFY `id_jeu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `livraison`
--
ALTER TABLE `livraison`
  MODIFY `id_livraison` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `trajet`
--
ALTER TABLE `trajet`
  MODIFY `id_trajet` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`);

--
-- Contraintes pour la table `historique`
--
ALTER TABLE `historique`
  ADD CONSTRAINT `historique_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `jeu_achete`
--
ALTER TABLE `jeu_achete`
  ADD CONSTRAINT `jeu_achete_ibfk_1` FOREIGN KEY (`jeu_id`) REFERENCES `jeu_jeu` (`id_jeu`);

--
-- Contraintes pour la table `jeu_jeu`
--
ALTER TABLE `jeu_jeu`
  ADD CONSTRAINT `jeu_jeu_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `jeu_categorie` (`id_categorie`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`);

--
-- Contraintes pour la table `trajet`
--
ALTER TABLE `trajet`
  ADD CONSTRAINT `trajet_ibfk_1` FOREIGN KEY (`id_livraison`) REFERENCES `livraison` (`id_livraison`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
