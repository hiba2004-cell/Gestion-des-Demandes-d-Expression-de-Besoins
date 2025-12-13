-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 12 déc. 2025 à 16:40
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

-- --------------------------------------------------------

--
-- Structure de la table `available_material`
--

CREATE TABLE `available_material` (
  `id` int(11) NOT NULL,
  `type_besoin_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `quantite_disponible` int(11) NOT NULL DEFAULT 1,
  `image_url` varchar(500) DEFAULT NULL,
  `date_ajout` datetime DEFAULT current_timestamp(),
  `statut` enum('Disponible','Indisponible','Réservé') NOT NULL DEFAULT 'Disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `available_material`
--

INSERT INTO `available_material` (`id`, `type_besoin_id`, `titre`, `description`, `quantite_disponible`, `image_url`, `date_ajout`, `statut`) VALUES
(1, 1, 'MacBook Pro 16\" M3', 'Ordinateur portable Apple MacBook Pro 16 pouces avec puce M3, 32Go RAM, 512Go SSD. Parfait pour le développement et le design.', 2, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400', '2025-12-01 14:56:47', 'Disponible'),
(2, 1, 'Dell UltraSharp 27\" 4K', 'Écran Dell UltraSharp 27 pouces 4K UHD avec USB-C, calibration des couleurs professionnelle.', 5, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400', '2025-12-01 14:56:47', 'Disponible'),
(3, 1, 'Clavier Mécanique Logitech MX', 'Clavier mécanique sans fil Logitech MX Mechanical avec rétroéclairage RGB et switches tactiles.', 10, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400', '2025-12-01 14:56:47', 'Disponible'),
(4, 1, 'Souris Ergonomique MX Master 3', 'Souris sans fil ergonomique Logitech MX Master 3S avec défilement ultra-rapide.', 4, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400', '2025-12-01 14:56:47', 'Disponible'),
(5, 2, 'Licence Adobe Creative Cloud', 'Abonnement annuel Adobe Creative Cloud incluant Photoshop, Illustrator, Premiere Pro et plus.', 15, 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=400', '2025-12-01 14:56:47', 'Disponible'),
(6, 2, 'Microsoft 365 Business', 'Suite Microsoft 365 Business Premium avec Teams, OneDrive 1To et applications Office.', 20, 'https://images.unsplash.com/photo-1633419461186-7d40a38105ec?w=400', '2025-12-01 14:56:47', 'Disponible'),
(7, 2, 'JetBrains All Products Pack', 'Licence annuelle pour tous les IDE JetBrains: IntelliJ, WebStorm, PyCharm, etc.', 10, 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400', '2025-12-01 14:56:47', 'Disponible'),
(8, 3, 'Chaise Ergonomique Herman Miller', 'Chaise de bureau Herman Miller Aeron avec support lombaire ajustable et accoudoirs 4D.', 4, 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=400', '2025-12-01 14:56:47', 'Disponible'),
(9, 3, 'Bureau Assis-Debout Électrique', 'Bureau motorisé réglable en hauteur avec plateau 160x80cm et mémorisation des positions.', 6, 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?w=400', '2025-12-01 14:56:47', 'Disponible'),
(10, 4, 'Webcam 4K Logitech Brio', 'Webcam professionnelle 4K HDR avec cadrage automatique et réduction de bruit.', 10, 'https://images.unsplash.com/photo-1587826080692-f439cd0b70da?w=400', '2025-12-01 14:56:47', 'Disponible'),
(11, 4, 'Casque Audio Sony WH-1000XM5', 'Casque sans fil à réduction de bruit active, autonomie 30h, qualité audio Hi-Res.', 6, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400', '2025-12-01 14:56:47', 'Disponible'),
(12, 1, 'iPad Pro 12.9\" M2', 'Tablette Apple iPad Pro 12.9 pouces avec puce M2, 256Go, WiFi + Cellular.', 2, 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400', '2025-12-01 14:56:47', 'Réservé');

-- --------------------------------------------------------

--
-- Structure de la table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conversations`
--

INSERT INTO `conversations` (`id`, `sender_id`, `receiver_id`, `message`, `sent_time`, `is_read`) VALUES
(1, 1, 2, 'Pourquoi cette demande 33 est envoyer pour moi pour que je la valide?', '2025-12-09 14:59:52', 1);

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
  `statut` enum('En attente','En cours de validation','Traitée') NOT NULL DEFAULT 'En attente',
  `date_creation` datetime DEFAULT current_timestamp(),
  `prix` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `demandes`
--

INSERT INTO `demandes` (`id`, `user_id`, `type_besoin_id`, `description`, `urgence`, `statut`, `date_creation`, `prix`) VALUES
(1, 2, 3, 'Remplacement de la chaise de bureau cassée.', 'Moyenne', 'En attente', '2024-03-14 20:54:44', 200.00),
(2, 2, 1, 'Achat de fournitures de bureau (papier, stylos).', 'Moyenne', 'En attente', '2025-06-30 14:41:54', 150.00),
(3, 2, 2, 'Accès au serveur de production.', 'Moyenne', '', '2024-06-22 13:09:00', NULL),
(4, 2, 4, 'Formation en sécurité informatique pour l\'équipe.', 'Faible', '', '2024-03-09 09:00:50', 2000.00),
(5, 2, 4, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Moyenne', 'Traitée', '2025-02-26 01:54:49', 400.00),
(6, 3, 2, 'Renouvellement de la licence Adobe Creative Cloud.', 'Faible', 'En cours de validation', '2023-01-20 09:28:18', 100.00),
(7, 3, 3, 'Remplacement de la chaise de bureau cassée.', 'Urgente', 'En attente', '2024-11-16 03:36:45', 200.00),
(8, 4, 4, 'Renouvellement de la licence Adobe Creative Cloud.', 'Faible', 'Traitée', '2025-02-11 18:49:57', 100.00),
(9, 4, 4, 'Remplacement de la chaise de bureau cassée.', 'Faible', '', '2023-02-21 10:49:22', 200.00),
(10, 5, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Faible', '', '2024-10-12 11:12:24', 500.00),
(11, 6, 1, 'Accès au serveur de production.', 'Faible', '', '2025-01-30 18:03:03', NULL),
(12, 6, 4, 'Installation de la fibre optique.', 'Faible', 'Traitée', '2024-03-31 08:00:13', 1000.00),
(13, 7, 1, 'Renouvellement de la licence Adobe Creative Cloud.', 'Faible', 'En attente', '2023-04-22 03:26:29', 300.00),
(14, 7, 1, 'Remplacement de la chaise de bureau cassée.', 'Urgente', '', '2023-11-14 23:20:37', 200.00),
(15, 7, 2, 'Formation en sécurité informatique pour l\'équipe.', 'Faible', '', '2023-07-07 21:39:02', 3000.00),
(16, 8, 3, 'Réparation de l\'imprimante du 2ème étage.', 'Faible', '', '2023-05-08 03:58:22', 150.00),
(17, 8, 4, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', 'Traitée', '2024-01-15 05:51:27', 300.00),
(18, 9, 4, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Moyenne', 'Traitée', '2025-01-12 13:07:47', 400.00),
(19, 9, 2, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Faible', 'En attente', '2024-03-18 16:02:46', 400.00),
(20, 9, 2, 'Remplacement de la chaise de bureau cassée.', 'Moyenne', 'En cours de validation', '2024-09-13 18:39:41', 200.00),
(21, 10, 2, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Urgente', '', '2025-06-14 03:31:16', 500.00),
(22, 11, 4, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', '', '2023-04-23 07:14:39', 400.00),
(23, 11, 2, 'Logiciel de gestion de projet (Jira).', 'Moyenne', '', '2024-06-09 12:48:06', 200.00),
(24, 11, 2, 'Formation en sécurité informatique pour l\'équipe.', 'Urgente', '', '2024-01-06 16:45:25', 2000.00),
(25, 11, 2, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', '', '2025-04-28 17:29:46', 150.00),
(26, 12, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Moyenne', 'Traitée', '2024-08-22 10:07:12', NULL),
(27, 12, 1, 'Remplacement de la chaise de bureau cassée.', 'Faible', 'En attente', '2024-09-03 17:26:28', NULL),
(28, 12, 2, 'Formation en sécurité informatique pour l\'équipe.', 'Urgente', 'Traitée', '2024-03-16 12:39:03', NULL),
(29, 12, 4, 'Logiciel de gestion de projet (Jira).', 'Faible', 'Traitée', '2025-01-14 19:31:28', NULL),
(30, 13, 4, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Moyenne', 'Traitée', '2023-09-30 06:45:22', NULL),
(31, 13, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Urgente', 'En attente', '2025-05-10 19:26:10', NULL),
(32, 13, 4, 'Installation de la fibre optique.', 'Urgente', 'En cours de validation', '2024-03-21 11:47:07', NULL),
(33, 14, 4, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Faible', 'Traitée', '2024-10-28 00:01:45', NULL),
(34, 14, 2, 'Remplacement de la chaise de bureau cassée.', 'Faible', 'Traitée', '2025-07-29 18:06:14', NULL),
(35, 14, 2, 'Achat de fournitures de bureau (papier, stylos).', 'Moyenne', 'En attente', '2023-11-18 09:31:50', NULL),
(36, 14, 3, 'Formation en sécurité informatique pour l\'équipe.', 'Faible', 'En attente', '2023-06-05 08:32:25', NULL),
(37, 14, 2, 'Achat de fournitures de bureau (papier, stylos).', 'Urgente', 'En cours de validation', '2024-04-09 12:56:30', NULL),
(38, 15, 3, 'Logiciel de gestion de projet (Jira).', 'Faible', '', '2023-06-20 10:09:51', NULL),
(39, 15, 1, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', 'En attente', '2024-12-31 14:13:51', NULL),
(40, 16, 2, 'Logiciel de gestion de projet (Jira).', 'Moyenne', 'En cours de validation', '2025-01-15 03:01:29', NULL),
(41, 16, 1, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Urgente', '', '2023-03-29 08:09:49', NULL),
(42, 16, 1, 'Formation en sécurité informatique pour l\'équipe.', 'Moyenne', '', '2023-12-13 23:56:23', NULL),
(43, 17, 4, 'Installation de la fibre optique.', 'Faible', '', '2024-04-01 22:51:50', NULL),
(44, 18, 4, 'Réparation de l\'imprimante du 2ème étage.', 'Urgente', 'Traitée', '2024-07-16 20:52:04', NULL),
(45, 19, 2, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Faible', 'Traitée', '2024-01-05 02:14:49', NULL),
(46, 19, 4, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Moyenne', '', '2024-03-30 22:27:58', NULL),
(47, 19, 1, 'Achat de fournitures de bureau (papier, stylos).', 'Moyenne', '', '2025-02-16 14:55:11', NULL),
(48, 19, 3, 'Accès au serveur de production.', 'Moyenne', 'Traitée', '2024-07-06 20:09:21', NULL),
(49, 19, 4, 'Logiciel de gestion de projet (Jira).', 'Urgente', 'En attente', '2025-03-16 13:37:38', NULL),
(50, 20, 4, 'Logiciel de gestion de projet (Jira).', 'Faible', 'Traitée', '2023-08-23 05:15:25', NULL),
(51, 20, 3, 'Formation en sécurité informatique pour l\'équipe.', 'Moyenne', 'En attente', '2024-11-01 19:46:16', NULL),
(52, 20, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Moyenne', 'En attente', '2024-02-24 07:53:30', NULL),
(53, 20, 4, 'Formation en sécurité informatique pour l\'équipe.', 'Moyenne', 'Traitée', '2023-02-13 16:19:27', NULL),
(57, 1, 1, 'Demande pour: MacBook Pro 16&quot; M3', 'Faible', 'En attente', '2025-12-01 21:08:55', NULL),
(58, 1, 1, 'Demande pour: Souris Ergonomique MX Master 3', 'Faible', 'En attente', '2025-12-01 21:10:31', NULL),
(59, 1, 1, 'Demande pour: Souris Ergonomique MX Master 3', 'Faible', 'En attente', '2025-12-01 21:10:46', NULL),
(60, 1, 1, 'Demande pour: Souris Ergonomique MX Master 3', 'Faible', 'En attente', '2025-12-01 21:12:32', NULL),
(61, 4, 4, 'Demande pour: Webcam 4K Logitech Brio', 'Faible', 'En attente', '2025-12-01 21:16:09', NULL),
(62, 4, 1, 'Demande pour: Souris Ergonomique MX Master 3', 'Faible', 'En attente', '2025-12-01 21:42:12', NULL),
(63, 2, 4, 'Demande pour: Casque Audio Sony WH-1000XM5', 'Urgente', 'Traitée', '2025-12-01 21:44:19', NULL),
(64, 7, 4, 'Demande pour: Webcam 4K Logitech Brio', 'Moyenne', 'En attente', '2025-12-11 19:04:05', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `is_just_for_admin` tinyint(1) NOT NULL DEFAULT 0,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `demande_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `message` varchar(200) DEFAULT NULL,
  `validateur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `service_id`, `is_just_for_admin`, `seen`, `demande_id`, `created_at`, `message`, `validateur_id`) VALUES
(5, 1, 1, 0, 5, '2025-11-29 16:07:52', NULL, NULL),
(6, 1, 1, 0, 29, '2025-11-29 16:08:03', NULL, NULL),
(7, 1, 1, 0, 50, '2025-11-29 16:08:37', NULL, NULL),
(8, 1, 1, 0, 8, '2025-11-29 16:09:56', NULL, NULL),
(9, 1, 0, 0, 57, '2025-12-01 21:08:55', 'Nouvelle demande #57 créée.', NULL),
(10, 1, 0, 0, 58, '2025-12-01 21:10:31', 'Nouvelle demande #58 créée.', NULL),
(11, 1, 0, 0, 59, '2025-12-01 21:10:46', 'Nouvelle demande #59 créée.', NULL),
(12, 1, 0, 0, 60, '2025-12-01 21:12:32', 'Nouvelle demande #60 créée.', NULL),
(13, 4, 0, 0, 61, '2025-12-01 21:16:09', 'Nouvelle demande #61 créée.', NULL),
(14, 1, 0, 0, 62, '2025-12-01 21:42:12', 'Nouvelle demande #62 créée.', NULL),
(15, 4, 0, 1, 63, '2025-12-01 21:44:19', 'Nouvelle demande #63 créée.', NULL),
(16, 1, 1, 0, 63, '2025-12-01 21:45:08', 'validateur  a envoyé la demande #63 à l\'administrateur pour révision.', NULL),
(17, 1, 1, 0, 30, '2025-12-02 15:42:42', 'validateur  a envoyé la demande #30 à l\'administrateur pour révision.', NULL),
(18, 1, 1, 1, 33, '2025-12-02 15:48:33', 'validateur  a envoyé la demande #33 à l\'administrateur pour révision.', 2),
(19, 4, 0, 0, 64, '2025-12-11 19:04:05', 'Nouvelle demande #64 créée.', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `pieces_jointes`
--

CREATE TABLE `pieces_jointes` (
  `id` int(11) NOT NULL,
  `demande_id` int(11) NOT NULL,
  `chemin_fichier` varchar(255) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pieces_jointes`
--

INSERT INTO `pieces_jointes` (`id`, `demande_id`, `chemin_fichier`, `date_ajout`) VALUES
(19, 1, '/besoins/uploads/pieces_jointes/pj_6929b5e28bd6f.pdf', '2025-11-28 15:46:58'),
(20, 4, '/besoins/uploads/pieces_jointes/pj_6929b9baf2cc5.pdf', '2025-11-28 16:03:23'),
(21, 4, '/besoins/uploads/pieces_jointes/pj_6929ba2624d52.pdf', '2025-11-28 16:05:10'),
(22, 4, '/besoins/uploads/pieces_jointes/pj_6929bab27d381.pdf', '2025-11-28 16:07:30');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `service_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `created_at`, `service_id`) VALUES
(1, 'Admin System', 'admin@admin.com', '12345', 'Administrateur', '2025-11-22 10:46:13', 2),
(2, 'Chef Validateur', 'chef@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 4),
(3, 'Frank Idrissi', 'frank.idrissi@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(4, 'Leila Smith', 'leila.smith@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13', 4),
(5, 'Jack Miller', 'jack.miller@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(6, 'Henry Robinson', 'henry.robinson@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(7, 'Omar Brown', 'omar.brown@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(8, 'Bob Thomas', 'bob.thomas@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(9, 'Karim Idrissi', 'karim.idrissi@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(10, 'Karim Cohen', 'karim.cohen@company.com', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(11, 'Nadia Thomas', 'nadia.thomas@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(12, 'Alice Martinez', 'alice.martinez@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(13, 'Leila Cohen', 'leila.cohen@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13', 4),
(14, 'Alice Miller', 'alice.miller@company.com', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(15, 'Alice Benali', 'alice.benali@company.com', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(16, 'Grace Thomas', 'grace.thomas@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(17, 'Charlie Benali', 'charlie.benali@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(18, 'Ivy Martinez', 'ivy.martinez@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(19, 'Grace Idrissi', 'grace.idrissi@tech.org', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(20, 'Emma Benali', 'emma.benali@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(22, 'Jedata', 'admin@jedatad.com', '12345', 'Demandeur', '2025-11-29 11:26:58', 1),
(23, 'Nadiri Hiba', 'nadiri@hiba.com', '$2y$10$XqoxthRVyYeI23dtOarOGOFwRVVB0sARkMu0pnqi7HHD1M6mj.Fp6', 'Administrateur', '2025-11-30 11:06:30', 1),
(24, 'Jedata Rachid', 'jedata@rachid.com', '$2y$10$.z6XBVIH3RIgcZP5tpdTUeQYk2X/t10gDbGAeTeHbrJFL1BKhMG4q', 'Validateur', '2025-11-30 11:07:16', 1);

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
(31, 53, 2, 'Rejeté, voir avec le service IT avant.', 'Validée', '2024-09-15 15:45:36'),
(46, 31, 2, 'i accept from Rachid Jedata', 'Validée', '2025-11-28 12:00:11'),
(47, 49, 2, 'Hekkoo', 'Rejetée', '2025-11-28 12:00:30'),
(50, 62, 2, 'Le Validateur 2 a Array cette demande 62', 'Rejetée', '2025-12-03 13:02:28'),
(51, 60, 2, 'Le Validateur 2 a Array cette demande 60', 'Rejetée', '2025-12-03 13:03:53'),
(52, 59, 2, 'Le Validateur 2 a Array cette demande 59', 'Rejetée', '2025-12-03 13:06:48'),
(53, 32, 2, '', 'Validée', '2025-12-11 18:54:43');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `available_material`
--
ALTER TABLE `available_material`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_type_besoin` (`type_besoin_id`);

--
-- Index pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sender` (`sender_id`),
  ADD KEY `fk_receiver` (`receiver_id`);

--
-- Index pour la table `demandes`
--
ALTER TABLE `demandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type_besoin_id` (`type_besoin_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `demande_id` (`demande_id`),
  ADD KEY `validateur_fk` (`validateur_id`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_service` (`service_id`);

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
-- AUTO_INCREMENT pour la table `available_material`
--
ALTER TABLE `available_material`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `demandes`
--
ALTER TABLE `demandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `types_besoins`
--
ALTER TABLE `types_besoins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `validation`
--
ALTER TABLE `validation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `available_material`
--
ALTER TABLE `available_material`
  ADD CONSTRAINT `fk_available_type_besoin` FOREIGN KEY (`type_besoin_id`) REFERENCES `types_besoins` (`id`);

--
-- Contraintes pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `fk_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `demandes`
--
ALTER TABLE `demandes`
  ADD CONSTRAINT `demandes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_ibfk_2` FOREIGN KEY (`type_besoin_id`) REFERENCES `types_besoins` (`id`);

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `types_besoins` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`demande_id`) REFERENCES `demandes` (`id`),
  ADD CONSTRAINT `validateur_fk` FOREIGN KEY (`validateur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD CONSTRAINT `pieces_jointes_ibfk_1` FOREIGN KEY (`demande_id`) REFERENCES `demandes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_service` FOREIGN KEY (`service_id`) REFERENCES `types_besoins` (`id`);

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
