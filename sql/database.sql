-- Base de données pour l'Expression du Besoin
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS expression_besoin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE expression_besoin;

-- Table des besoins
CREATE TABLE IF NOT EXISTS besoins (
    id INT(11) NOT NULL AUTO_INCREMENT,
    titre VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    priorite ENUM('faible', 'moyenne', 'haute', 'critique') NOT NULL DEFAULT 'moyenne',
    statut ENUM('nouveau', 'en_cours', 'termine', 'rejete') NOT NULL DEFAULT 'nouveau',
    categorie VARCHAR(100) NOT NULL,
    demandeur_nom VARCHAR(100) NOT NULL,
    demandeur_email VARCHAR(150) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    cout_estime DECIMAL(10,2) DEFAULT NULL,
    delai_souhaite DATE DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_priorite (priorite),
    INDEX idx_statut (statut),
    INDEX idx_categorie (categorie),
    INDEX idx_date_creation (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de données d'exemple
INSERT INTO besoins (titre, description, priorite, statut, categorie, demandeur_nom, demandeur_email, cout_estime, delai_souhaite) VALUES
('Développement d\'une application mobile', 'Création d\'une application mobile native pour iOS et Android permettant aux clients de consulter leurs commandes en temps réel.', 'haute', 'nouveau', 'Développement', 'Jean Dupont', 'jean.dupont@email.com', 25000.00, '2024-06-15'),
('Mise à jour du site web', 'Refonte graphique du site web existant avec amélioration de l\'expérience utilisateur et optimisation SEO.', 'moyenne', 'en_cours', 'Web Design', 'Marie Martin', 'marie.martin@email.com', 8500.00, '2024-04-20'),
('Système de gestion des stocks', 'Implémentation d\'un système automatisé de gestion des stocks avec alertes de rupture et prévisions.', 'critique', 'nouveau', 'ERP', 'Pierre Durand', 'pierre.durand@email.com', 35000.00, '2024-05-10'),
('Formation équipe technique', 'Organisation de formations pour l\'équipe technique sur les nouvelles technologies cloud et DevOps.', 'faible', 'termine', 'Formation', 'Sophie Bernard', 'sophie.bernard@email.com', 3500.00, '2024-03-30'),
('Migration vers le cloud', 'Migration de l\'infrastructure on-premise vers une solution cloud avec haute disponibilité.', 'haute', 'en_cours', 'Infrastructure', 'Laurent Petit', 'laurent.petit@email.com', 18000.00, '2024-07-01');

-- Table des catégories (optionnelle pour extension future)
CREATE TABLE IF NOT EXISTS categories (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    couleur VARCHAR(7) DEFAULT '#007bff',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des catégories par défaut
INSERT INTO categories (nom, description, couleur) VALUES
('Développement', 'Projets de développement logiciel', '#007bff'),
('Web Design', 'Projets de conception web et UX/UI', '#28a745'),
('ERP', 'Systèmes de gestion d\'entreprise', '#dc3545'),
('Formation', 'Projets de formation et développement des compétences', '#ffc107'),
('Infrastructure', 'Projets d\'infrastructure IT', '#6c757d'),
('Marketing', 'Projets marketing et communication', '#e83e8c'),
('Support', 'Support technique et maintenance', '#fd7e14');