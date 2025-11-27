-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 26 nov. 2025 à 10:13
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `expression_besoin`
--
CREATE DATABASE IF NOT EXISTS `expression_besoin` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `expression_besoin`;
-- --------------------------------------------------------

--
-- Structure de la table `besoins`
--

CREATE TABLE `besoins` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `priorite` enum('faible','moyenne','haute','critique') NOT NULL DEFAULT 'moyenne',
  `statut` enum('nouveau','en_cours','termine','rejete') NOT NULL DEFAULT 'nouveau',
  `categorie` varchar(100) NOT NULL,
  `demandeur_nom` varchar(100) NOT NULL,
  `demandeur_email` varchar(150) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cout_estime` decimal(10,2) DEFAULT NULL,
  `delai_souhaite` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `besoins`
--

INSERT INTO `besoins` (`id`, `titre`, `description`, `priorite`, `statut`, `categorie`, `demandeur_nom`, `demandeur_email`, `date_creation`, `date_modification`, `cout_estime`, `delai_souhaite`) VALUES
(1, 'Développement d\'une application mobile', 'Création d\'une application mobile native pour iOS et Android permettant aux clients de consulter leurs commandes en temps réel.', 'haute', 'nouveau', 'Développement', 'Jean Dupont', 'jean.dupont@email.com', '2025-11-22 14:03:49', '2025-11-22 14:03:49', 25000.00, '2024-06-15'),
(2, 'Mise à jour du site web', 'Refonte graphique du site web existant avec amélioration de l\'expérience utilisateur et optimisation SEO.', 'moyenne', 'en_cours', 'Web Design', 'Marie Martin', 'marie.martin@email.com', '2025-11-22 14:03:49', '2025-11-22 14:03:49', 8500.00, '2024-04-20'),
(3, 'Système de gestion des stocks', 'Implémentation d\'un système automatisé de gestion des stocks avec alertes de rupture et prévisions.', 'critique', 'nouveau', 'ERP', 'Pierre Durand', 'pierre.durand@email.com', '2025-11-22 14:03:49', '2025-11-22 14:03:49', 35000.00, '2024-05-10'),
(4, 'Formation équipe technique', 'Organisation de formations pour l\'équipe technique sur les nouvelles technologies cloud et DevOps.', 'faible', 'termine', 'Formation', 'Sophie Bernard', 'sophie.bernard@email.com', '2025-11-22 14:03:49', '2025-11-22 14:03:49', 3500.00, '2024-03-30'),
(5, 'Migration vers le cloud', 'Migration de l\'infrastructure on-premise vers une solution cloud avec haute disponibilité.', 'haute', 'en_cours', 'Infrastructure', 'Laurent Petit', 'laurent.petit@email.com', '2025-11-22 14:03:49', '2025-11-22 14:03:49', 18000.00, '2024-07-01');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `couleur` varchar(7) DEFAULT '#007bff',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `couleur`, `date_creation`) VALUES
(1, 'Développement', 'Projets de développement logiciel', '#007bff', '2025-11-22 14:03:49'),
(2, 'Web Design', 'Projets de conception web et UX/UI', '#28a745', '2025-11-22 14:03:49'),
(3, 'ERP', 'Systèmes de gestion d\'entreprise', '#dc3545', '2025-11-22 14:03:49'),
(4, 'Formation', 'Projets de formation et développement des compétences', '#ffc107', '2025-11-22 14:03:49'),
(5, 'Infrastructure', 'Projets d\'infrastructure IT', '#6c757d', '2025-11-22 14:03:49'),
(6, 'Marketing', 'Projets marketing et communication', '#e83e8c', '2025-11-22 14:03:49'),
(7, 'Support', 'Support technique et maintenance', '#fd7e14', '2025-11-22 14:03:49');

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--

CREATE TABLE `demandes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type_besoin_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `urgence` enum('Faible','Moyenne','Urgente') NOT NULL DEFAULT 'Faible',
  `statut` enum('En attente','En cours de validation','Validée','Rejetée','Traitée') NOT NULL DEFAULT 'En attente',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `demandes`
--

INSERT INTO `demandes` (`id`, `user_id`, `type_besoin_id`, `description`, `urgence`, `statut`, `date_creation`) VALUES
(1, 2, 3, 'Remplacement de la chaise de bureau cassée.', 'Moyenne', 'Validée', '2024-03-14 20:54:44'),
(2, 2, 1, 'Achat de fournitures de bureau (papier, stylos).', 'Moyenne', 'Validée', '2025-06-30 14:41:54'),
(3, 2, 2, 'Accès au serveur de production.', 'Moyenne', 'Validée', '2024-06-22 13:09:00'),
(4, 2, 4, 'Formation en sécurité informatique pour l\'équipe.', 'Faible', 'Rejetée', '2024-03-09 09:00:50'),
(5, 2, 4, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Moyenne', 'En attente', '2025-02-26 01:54:49'),
(6, 3, 2, 'Renouvellement de la licence Adobe Creative Cloud.', 'Faible', 'En cours de validation', '2023-01-20 09:28:18'),
(7, 3, 3, 'Remplacement de la chaise de bureau cassée.', 'Urgente', 'En attente', '2024-11-16 03:36:45'),
(8, 4, 4, 'Renouvellement de la licence Adobe Creative Cloud.', 'Faible', 'En attente', '2025-02-11 18:49:57'),
(9, 4, 4, 'Remplacement de la chaise de bureau cassée.', 'Faible', 'Validée', '2023-02-21 10:49:22'),
(10, 5, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Faible', 'Validée', '2024-10-12 11:12:24'),
(11, 6, 1, 'Accès au serveur de production.', 'Faible', 'Rejetée', '2025-01-30 18:03:03'),
(12, 6, 4, 'Installation de la fibre optique.', 'Faible', 'Traitée', '2024-03-31 08:00:13'),
(13, 7, 1, 'Renouvellement de la licence Adobe Creative Cloud.', 'Faible', 'En attente', '2023-04-22 03:26:29'),
(14, 7, 1, 'Remplacement de la chaise de bureau cassée.', 'Urgente', 'Validée', '2023-11-14 23:20:37'),
(15, 7, 2, 'Formation en sécurité informatique pour l\'équipe.', 'Faible', 'Rejetée', '2023-07-07 21:39:02'),
(16, 8, 3, 'Réparation de l\'imprimante du 2ème étage.', 'Faible', 'Validée', '2023-05-08 03:58:22'),
(17, 8, 4, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', 'Traitée', '2024-01-15 05:51:27'),
(18, 9, 4, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Moyenne', 'Traitée', '2025-01-12 13:07:47'),
(19, 9, 2, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Faible', 'En attente', '2024-03-18 16:02:46'),
(20, 9, 2, 'Remplacement de la chaise de bureau cassée.', 'Moyenne', 'En cours de validation', '2024-09-13 18:39:41'),
(21, 10, 2, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Urgente', 'Validée', '2025-06-14 03:31:16'),
(22, 11, 4, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', 'Validée', '2023-04-23 07:14:39'),
(23, 11, 2, 'Logiciel de gestion de projet (Jira).', 'Moyenne', 'Validée', '2024-06-09 12:48:06'),
(24, 11, 2, 'Formation en sécurité informatique pour l\'équipe.', 'Urgente', 'Validée', '2024-01-06 16:45:25'),
(25, 11, 2, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', 'Validée', '2025-04-28 17:29:46'),
(26, 12, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Moyenne', 'Traitée', '2024-08-22 10:07:12'),
(27, 12, 1, 'Remplacement de la chaise de bureau cassée.', 'Faible', 'En attente', '2024-09-03 17:26:28'),
(28, 12, 2, 'Formation en sécurité informatique pour l\'équipe.', 'Urgente', 'Traitée', '2024-03-16 12:39:03'),
(29, 12, 4, 'Logiciel de gestion de projet (Jira).', 'Faible', 'En cours de validation', '2025-01-14 19:31:28'),
(30, 13, 4, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Moyenne', 'En attente', '2023-09-30 06:45:22'),
(31, 13, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Urgente', 'En attente', '2025-05-10 19:26:10'),
(32, 13, 4, 'Installation de la fibre optique.', 'Urgente', 'En cours de validation', '2024-03-21 11:47:07'),
(33, 14, 4, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Faible', 'En attente', '2024-10-28 00:01:45'),
(34, 14, 2, 'Remplacement de la chaise de bureau cassée.', 'Faible', 'Traitée', '2025-07-29 18:06:14'),
(35, 14, 2, 'Achat de fournitures de bureau (papier, stylos).', 'Moyenne', 'En attente', '2023-11-18 09:31:50'),
(36, 14, 3, 'Formation en sécurité informatique pour l\'équipe.', 'Faible', 'En attente', '2023-06-05 08:32:25'),
(37, 14, 2, 'Achat de fournitures de bureau (papier, stylos).', 'Urgente', 'En cours de validation', '2024-04-09 12:56:30'),
(38, 15, 3, 'Logiciel de gestion de projet (Jira).', 'Faible', 'Validée', '2023-06-20 10:09:51'),
(39, 15, 1, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', 'En attente', '2024-12-31 14:13:51'),
(40, 16, 2, 'Logiciel de gestion de projet (Jira).', 'Moyenne', 'En cours de validation', '2025-01-15 03:01:29'),
(41, 16, 1, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Urgente', 'Rejetée', '2023-03-29 08:09:49'),
(42, 16, 1, 'Formation en sécurité informatique pour l\'équipe.', 'Moyenne', 'Validée', '2023-12-13 23:56:23'),
(43, 17, 4, 'Installation de la fibre optique.', 'Faible', 'Rejetée', '2024-04-01 22:51:50'),
(44, 18, 4, 'Réparation de l\'imprimante du 2ème étage.', 'Urgente', 'Traitée', '2024-07-16 20:52:04'),
(45, 19, 2, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Faible', 'Traitée', '2024-01-05 02:14:49'),
(46, 19, 4, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Moyenne', 'Rejetée', '2024-03-30 22:27:58'),
(47, 19, 1, 'Achat de fournitures de bureau (papier, stylos).', 'Moyenne', 'Validée', '2025-02-16 14:55:11'),
(48, 19, 3, 'Accès au serveur de production.', 'Moyenne', 'Traitée', '2024-07-06 20:09:21'),
(49, 19, 4, 'Logiciel de gestion de projet (Jira).', 'Urgente', 'En attente', '2025-03-16 13:37:38'),
(50, 20, 4, 'Logiciel de gestion de projet (Jira).', 'Faible', 'En cours de validation', '2023-08-23 05:15:25'),
(51, 20, 3, 'Formation en sécurité informatique pour l\'équipe.', 'Moyenne', 'En attente', '2024-11-01 19:46:16'),
(52, 20, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Moyenne', 'En attente', '2024-02-24 07:53:30'),
(53, 20, 4, 'Formation en sécurité informatique pour l\'équipe.', 'Moyenne', 'Traitée', '2023-02-13 16:19:27');

-- --------------------------------------------------------

--
-- Structure de la table `pieces_jointes`
--

CREATE TABLE `pieces_jointes` (
  `id` int(11) NOT NULL,
  `demande_id` int(11) NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `chemin_fichier` varchar(255) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pieces_jointes`
--

INSERT INTO `pieces_jointes` (`id`, `demande_id`, `nom_fichier`, `chemin_fichier`, `date_ajout`) VALUES
(1, 4, 'devis_4.pdf', '/uploads/2023/devis_4.pdf', '2024-03-09 09:00:50'),
(2, 10, 'devis_10.pdf', '/uploads/2023/devis_10.pdf', '2024-10-12 11:12:24'),
(3, 23, 'devis_23.pdf', '/uploads/2023/devis_23.pdf', '2024-06-09 12:48:06'),
(4, 24, 'devis_24.pdf', '/uploads/2023/devis_24.pdf', '2024-01-06 16:45:25'),
(5, 26, 'devis_26.pdf', '/uploads/2023/devis_26.pdf', '2024-08-22 10:07:12'),
(6, 27, 'devis_27.pdf', '/uploads/2023/devis_27.pdf', '2024-09-03 17:26:28'),
(7, 30, 'devis_30.pdf', '/uploads/2023/devis_30.pdf', '2023-09-30 06:45:22'),
(8, 31, 'devis_31.pdf', '/uploads/2023/devis_31.pdf', '2025-05-10 19:26:10'),
(9, 34, 'devis_34.pdf', '/uploads/2023/devis_34.pdf', '2025-07-29 18:06:14'),
(10, 36, 'devis_36.pdf', '/uploads/2023/devis_36.pdf', '2023-06-05 08:32:25'),
(11, 41, 'devis_41.pdf', '/uploads/2023/devis_41.pdf', '2023-03-29 08:09:49'),
(12, 43, 'devis_43.pdf', '/uploads/2023/devis_43.pdf', '2024-04-01 22:51:50'),
(13, 45, 'devis_45.pdf', '/uploads/2023/devis_45.pdf', '2024-01-05 02:14:49'),
(14, 46, 'devis_46.pdf', '/uploads/2023/devis_46.pdf', '2024-03-30 22:27:58'),
(15, 49, 'devis_49.pdf', '/uploads/2023/devis_49.pdf', '2025-03-16 13:37:38'),
(16, 50, 'devis_50.pdf', '/uploads/2023/devis_50.pdf', '2023-08-23 05:15:25'),
(17, 52, 'devis_52.pdf', '/uploads/2023/devis_52.pdf', '2024-02-24 07:53:30'),
(18, 53, 'devis_53.pdf', '/uploads/2023/devis_53.pdf', '2023-02-13 16:19:27');

-- --------------------------------------------------------

--
-- Structure de la table `types_besoins`
--

CREATE TABLE `types_besoins` (
  `id` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `types_besoins`
--

INSERT INTO `types_besoins` (`id`, `libelle`) VALUES
(1, 'Matériel'),
(2, 'Logiciel'),
(3, 'Service'),
(4, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Demandeur','Validateur','Administrateur') NOT NULL DEFAULT 'Demandeur',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin System', 'admin@company.com', '12345', 'Administrateur', '2025-11-22 10:46:13'),
(2, 'Chef Validateur', 'chef@company.com', '12345', 'Validateur', '2025-11-22 10:46:13'),
(3, 'Frank Idrissi', 'frank.idrissi@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(4, 'Leila Smith', 'leila.smith@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(5, 'Jack Miller', 'jack.miller@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(6, 'Henry Robinson', 'henry.robinson@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(7, 'Omar Brown', 'omar.brown@company.com', '12345', 'Validateur', '2025-11-22 10:46:13'),
(8, 'Bob Thomas', 'bob.thomas@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(9, 'Karim Idrissi', 'karim.idrissi@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(10, 'Karim Cohen', 'karim.cohen@company.com', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(11, 'Nadia Thomas', 'nadia.thomas@company.com', '12345', 'Validateur', '2025-11-22 10:46:13'),
(12, 'Alice Martinez', 'alice.martinez@company.com', '12345', 'Validateur', '2025-11-22 10:46:13'),
(13, 'Leila Cohen', 'leila.cohen@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(14, 'Alice Miller', 'alice.miller@company.com', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(15, 'Alice Benali', 'alice.benali@company.com', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(16, 'Grace Thomas', 'grace.thomas@company.com', '12345', 'Validateur', '2025-11-22 10:46:13'),
(17, 'Charlie Benali', 'charlie.benali@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(18, 'Ivy Martinez', 'ivy.martinez@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13'),
(19, 'Grace Idrissi', 'grace.idrissi@tech.org', '12345', 'Validateur', '2025-11-22 10:46:13'),
(20, 'Emma Benali', 'emma.benali@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13');

-- --------------------------------------------------------

--
-- Structure de la table `validation`
--

CREATE TABLE `validation` (
  `id` int(11) NOT NULL,
  `demande_id` int(11) NOT NULL,
  `validateur_id` int(11) NOT NULL,
  `commentaire` text DEFAULT NULL,
  `statut_validation` enum('Validée','Rejetée') NOT NULL,
  `date_validation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `validation`
--

INSERT INTO `validation` (`id`, `demande_id`, `validateur_id`, `commentaire`, `statut_validation`, `date_validation`) VALUES
(1, 1, 2, 'OK pour moi.', 'Validée', '2025-09-14 16:06:52'),
(2, 2, 2, 'OK pour moi.', 'Validée', '2025-11-05 23:53:49'),
(3, 3, 2, 'Validé, budget approuvé.', 'Validée', '2025-07-18 14:47:51'),
(4, 4, 2, 'Rejeté, budget insuffisant pour le moment.', 'Rejetée', '2025-02-10 04:34:20'),
(5, 9, 2, 'En attente de plus d\'informations.', 'Validée', '2025-07-11 02:22:43'),
(6, 10, 2, 'Rejeté, budget insuffisant pour le moment.', 'Validée', '2025-09-08 06:15:38'),
(7, 11, 2, 'Non prioritaire.', 'Rejetée', '2025-06-11 04:11:06'),
(8, 12, 2, 'Rejeté, budget insuffisant pour le moment.', 'Validée', '2024-05-10 18:12:55'),
(9, 14, 2, 'Validé, urgent.', 'Validée', '2024-05-20 18:48:50'),
(10, 15, 2, 'Rejeté, budget insuffisant pour le moment.', 'Rejetée', '2024-11-02 11:56:55'),
(11, 16, 2, 'Non prioritaire.', 'Validée', '2023-08-22 12:14:46'),
(12, 17, 2, 'OK pour moi.', 'Validée', '2025-10-08 22:20:55'),
(13, 18, 2, 'Validé, urgent.', 'Validée', '2025-07-17 06:07:35'),
(14, 21, 2, 'Non prioritaire.', 'Validée', '2025-10-25 12:46:34'),
(15, 22, 2, 'OK pour moi.', 'Validée', '2025-02-05 19:48:15'),
(16, 23, 2, 'Rejeté, budget insuffisant pour le moment.', 'Validée', '2025-10-06 19:26:33'),
(17, 24, 2, 'Rejeté, budget insuffisant pour le moment.', 'Validée', '2024-08-31 20:31:01'),
(18, 25, 2, 'Validé, budget approuvé.', 'Validée', '2025-10-06 08:34:37'),
(19, 26, 2, 'OK pour moi.', 'Validée', '2025-03-01 17:28:34'),
(20, 28, 2, 'Rejeté, voir avec le service IT avant.', 'Validée', '2025-09-28 06:13:18'),
(21, 34, 2, 'Rejeté, voir avec le service IT avant.', 'Validée', '2025-08-22 02:43:14'),
(22, 38, 2, 'Rejeté, voir avec le service IT avant.', 'Validée', '2023-12-07 21:38:34'),
(23, 41, 2, 'Rejeté, budget insuffisant pour le moment.', 'Rejetée', '2023-08-12 03:00:51'),
(24, 42, 2, 'Rejeté, voir avec le service IT avant.', 'Validée', '2024-03-01 03:09:44'),
(25, 43, 2, 'OK pour moi.', 'Rejetée', '2024-05-11 07:49:02'),
(26, 44, 2, 'Non prioritaire.', 'Validée', '2025-05-03 08:11:08'),
(27, 45, 2, 'Rejeté, budget insuffisant pour le moment.', 'Validée', '2025-02-11 09:36:09'),
(28, 46, 2, 'Validé, budget approuvé.', 'Rejetée', '2024-08-19 20:20:10'),
(29, 47, 2, 'Validé, budget approuvé.', 'Validée', '2025-03-05 21:36:16'),
(30, 48, 2, 'En attente de plus d\'informations.', 'Validée', '2025-07-30 07:36:07'),
(31, 53, 2, 'Rejeté, voir avec le service IT avant.', 'Validée', '2024-09-15 15:45:36');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `besoins`
--
ALTER TABLE `besoins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_priorite` (`priorite`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_categorie` (`categorie`),
  ADD KEY `idx_date_creation` (`date_creation`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `demandes`
--
ALTER TABLE `demandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type_besoin_id` (`type_besoin_id`);

--
-- Index pour la table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `demande_id` (`demande_id`);

--
-- Index pour la table `types_besoins`
--
ALTER TABLE `types_besoins`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `validation`
--
ALTER TABLE `validation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `demande_id` (`demande_id`),
  ADD KEY `validateur_id` (`validateur_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `besoins`
--
ALTER TABLE `besoins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `demandes`
--
ALTER TABLE `demandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `types_besoins`
--
ALTER TABLE `types_besoins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `validation`
--
ALTER TABLE `validation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `demandes`
--
ALTER TABLE `demandes`
  ADD CONSTRAINT `demandes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_ibfk_2` FOREIGN KEY (`type_besoin_id`) REFERENCES `types_besoins` (`id`);

--
-- Contraintes pour la table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD CONSTRAINT `pieces_jointes_ibfk_1` FOREIGN KEY (`demande_id`) REFERENCES `demandes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `validation`
--
ALTER TABLE `validation`
  ADD CONSTRAINT `validation_ibfk_1` FOREIGN KEY (`demande_id`) REFERENCES `demandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `validation_ibfk_2` FOREIGN KEY (`validateur_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
