-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2025 at 12:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expression_besoin`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `couleur` varchar(7) DEFAULT '#007bff',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
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
-- Table structure for table `demandes`
--

CREATE TABLE `demandes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type_besoin_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `urgence` enum('Faible','Moyenne','Urgente') NOT NULL DEFAULT 'Faible',
  `statut` enum('En attente','En cours de validation','Traitée') NOT NULL DEFAULT 'En attente',
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `demandes`
--

INSERT INTO `demandes` (`id`, `user_id`, `type_besoin_id`, `description`, `urgence`, `statut`, `date_creation`) VALUES
(1, 2, 3, 'Remplacement de la chaise de bureau cassée.', 'Moyenne', 'En attente', '2024-03-14 20:54:44'),
(2, 2, 1, 'Achat de fournitures de bureau (papier, stylos).', 'Moyenne', 'En attente', '2025-06-30 14:41:54'),
(3, 2, 2, 'Accès au serveur de production.', 'Moyenne', '', '2024-06-22 13:09:00'),
(4, 2, 4, 'Formation en sécurité informatique pour l\'équipe.', 'Faible', '', '2024-03-09 09:00:50'),
(5, 2, 4, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Moyenne', 'En attente', '2025-02-26 01:54:49'),
(6, 3, 2, 'Renouvellement de la licence Adobe Creative Cloud.', 'Faible', 'En cours de validation', '2023-01-20 09:28:18'),
(7, 3, 3, 'Remplacement de la chaise de bureau cassée.', 'Urgente', 'En attente', '2024-11-16 03:36:45'),
(8, 4, 4, 'Renouvellement de la licence Adobe Creative Cloud.', 'Faible', 'En attente', '2025-02-11 18:49:57'),
(9, 4, 4, 'Remplacement de la chaise de bureau cassée.', 'Faible', '', '2023-02-21 10:49:22'),
(10, 5, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Faible', '', '2024-10-12 11:12:24'),
(11, 6, 1, 'Accès au serveur de production.', 'Faible', '', '2025-01-30 18:03:03'),
(12, 6, 4, 'Installation de la fibre optique.', 'Faible', 'Traitée', '2024-03-31 08:00:13'),
(13, 7, 1, 'Renouvellement de la licence Adobe Creative Cloud.', 'Faible', 'En attente', '2023-04-22 03:26:29'),
(14, 7, 1, 'Remplacement de la chaise de bureau cassée.', 'Urgente', '', '2023-11-14 23:20:37'),
(15, 7, 2, 'Formation en sécurité informatique pour l\'équipe.', 'Faible', '', '2023-07-07 21:39:02'),
(16, 8, 3, 'Réparation de l\'imprimante du 2ème étage.', 'Faible', '', '2023-05-08 03:58:22'),
(17, 8, 4, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', 'Traitée', '2024-01-15 05:51:27'),
(18, 9, 4, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Moyenne', 'Traitée', '2025-01-12 13:07:47'),
(19, 9, 2, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Faible', 'En attente', '2024-03-18 16:02:46'),
(20, 9, 2, 'Remplacement de la chaise de bureau cassée.', 'Moyenne', 'En cours de validation', '2024-09-13 18:39:41'),
(21, 10, 2, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Urgente', '', '2025-06-14 03:31:16'),
(22, 11, 4, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', '', '2023-04-23 07:14:39'),
(23, 11, 2, 'Logiciel de gestion de projet (Jira).', 'Moyenne', '', '2024-06-09 12:48:06'),
(24, 11, 2, 'Formation en sécurité informatique pour l\'équipe.', 'Urgente', '', '2024-01-06 16:45:25'),
(25, 11, 2, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', '', '2025-04-28 17:29:46'),
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
(38, 15, 3, 'Logiciel de gestion de projet (Jira).', 'Faible', '', '2023-06-20 10:09:51'),
(39, 15, 1, 'Achat de fournitures de bureau (papier, stylos).', 'Faible', 'En attente', '2024-12-31 14:13:51'),
(40, 16, 2, 'Logiciel de gestion de projet (Jira).', 'Moyenne', 'En cours de validation', '2025-01-15 03:01:29'),
(41, 16, 1, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Urgente', '', '2023-03-29 08:09:49'),
(42, 16, 1, 'Formation en sécurité informatique pour l\'équipe.', 'Moyenne', '', '2023-12-13 23:56:23'),
(43, 17, 4, 'Installation de la fibre optique.', 'Faible', '', '2024-04-01 22:51:50'),
(44, 18, 4, 'Réparation de l\'imprimante du 2ème étage.', 'Urgente', 'Traitée', '2024-07-16 20:52:04'),
(45, 19, 2, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Faible', 'Traitée', '2024-01-05 02:14:49'),
(46, 19, 4, 'Besoin d\'un nouvel ordinateur portable pour le développement.', 'Moyenne', '', '2024-03-30 22:27:58'),
(47, 19, 1, 'Achat de fournitures de bureau (papier, stylos).', 'Moyenne', '', '2025-02-16 14:55:11'),
(48, 19, 3, 'Accès au serveur de production.', 'Moyenne', 'Traitée', '2024-07-06 20:09:21'),
(49, 19, 4, 'Logiciel de gestion de projet (Jira).', 'Urgente', 'En attente', '2025-03-16 13:37:38'),
(50, 20, 4, 'Logiciel de gestion de projet (Jira).', 'Faible', 'En cours de validation', '2023-08-23 05:15:25'),
(51, 20, 3, 'Formation en sécurité informatique pour l\'équipe.', 'Moyenne', 'En attente', '2024-11-01 19:46:16'),
(52, 20, 3, 'Besoin d\'un écran supplémentaire 27 pouces.', 'Moyenne', 'En attente', '2024-02-24 07:53:30'),
(53, 20, 4, 'Formation en sécurité informatique pour l\'équipe.', 'Moyenne', 'Traitée', '2023-02-13 16:19:27');

-- --------------------------------------------------------

--
-- Table structure for table `pieces_jointes`
--

CREATE TABLE `pieces_jointes` (
  `id` int(11) NOT NULL,
  `demande_id` int(11) NOT NULL,
  `chemin_fichier` varchar(255) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pieces_jointes`
--

INSERT INTO `pieces_jointes` (`id`, `demande_id`, `chemin_fichier`, `date_ajout`) VALUES
(19, 1, '/besoins/uploads/pieces_jointes/pj_6929b5e28bd6f.pdf', '2025-11-28 15:46:58'),
(20, 4, '/besoins/uploads/pieces_jointes/pj_6929b9baf2cc5.pdf', '2025-11-28 16:03:23'),
(21, 4, '/besoins/uploads/pieces_jointes/pj_6929ba2624d52.pdf', '2025-11-28 16:05:10'),
(22, 4, '/besoins/uploads/pieces_jointes/pj_6929bab27d381.pdf', '2025-11-28 16:07:30');

-- --------------------------------------------------------

--
-- Table structure for table `types_besoins`
--

CREATE TABLE `types_besoins` (
  `id` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `types_besoins`
--

INSERT INTO `types_besoins` (`id`, `libelle`) VALUES
(1, 'Matériel'),
(2, 'Logiciel'),
(3, 'Service'),
(4, 'Autre');

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `created_at`, `service_id`) VALUES
(1, 'Admin System', 'admin@admin.com', '12345', 'Administrateur', '2025-11-22 10:46:13', 1),
(2, 'Chef Validateur', 'chef@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(3, 'Frank Idrissi', 'frank.idrissi@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(4, 'Leila Smith', 'leila.smith@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(5, 'Jack Miller', 'jack.miller@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(6, 'Henry Robinson', 'henry.robinson@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(7, 'Omar Brown', 'omar.brown@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(8, 'Bob Thomas', 'bob.thomas@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(9, 'Karim Idrissi', 'karim.idrissi@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(10, 'Karim Cohen', 'karim.cohen@company.com', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(11, 'Nadia Thomas', 'nadia.thomas@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(12, 'Alice Martinez', 'alice.martinez@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(13, 'Leila Cohen', 'leila.cohen@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(14, 'Alice Miller', 'alice.miller@company.com', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(15, 'Alice Benali', 'alice.benali@company.com', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(16, 'Grace Thomas', 'grace.thomas@company.com', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(17, 'Charlie Benali', 'charlie.benali@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(18, 'Ivy Martinez', 'ivy.martinez@solutions.net', '12345', 'Demandeur', '2025-11-22 10:46:13', 1),
(19, 'Grace Idrissi', 'grace.idrissi@tech.org', '12345', 'Validateur', '2025-11-22 10:46:13', 1),
(20, 'Emma Benali', 'emma.benali@tech.org', '12345', 'Demandeur', '2025-11-22 10:46:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `validation`
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
-- Dumping data for table `validation`
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
(47, 49, 2, 'Hekkoo', 'Rejetée', '2025-11-28 12:00:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `demandes`
--
ALTER TABLE `demandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type_besoin_id` (`type_besoin_id`);

--
-- Indexes for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `demande_id` (`demande_id`);

--
-- Indexes for table `types_besoins`
--
ALTER TABLE `types_besoins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_service` (`service_id`);

--
-- Indexes for table `validation`
--
ALTER TABLE `validation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `demande_id` (`demande_id`),
  ADD KEY `validateur_id` (`validateur_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `demandes`
--
ALTER TABLE `demandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `types_besoins`
--
ALTER TABLE `types_besoins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `validation`
--
ALTER TABLE `validation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `demandes`
--
ALTER TABLE `demandes`
  ADD CONSTRAINT `demandes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_ibfk_2` FOREIGN KEY (`type_besoin_id`) REFERENCES `types_besoins` (`id`);

--
-- Constraints for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD CONSTRAINT `pieces_jointes_ibfk_1` FOREIGN KEY (`demande_id`) REFERENCES `demandes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_service` FOREIGN KEY (`service_id`) REFERENCES `types_besoins` (`id`);

--
-- Constraints for table `validation`
--
ALTER TABLE `validation`
  ADD CONSTRAINT `validation_ibfk_1` FOREIGN KEY (`demande_id`) REFERENCES `demandes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `validation_ibfk_2` FOREIGN KEY (`validateur_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
