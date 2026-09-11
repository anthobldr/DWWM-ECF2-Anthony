-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : ven. 11 sep. 2026 à 14:37
-- Version du serveur : 8.0.44
-- Version de PHP : 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dwwm-ecf-backend`
--

-- --------------------------------------------------------

--
-- Structure de la table `absence`
--

CREATE TABLE `absence` (
  `id` int NOT NULL,
  `trainee_id` int NOT NULL,
  `date` date NOT NULL,
  `reason` varchar(50) NOT NULL,
  `notes` longtext,
  `justificative_filename` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `absence`
--

INSERT INTO `absence` (`id`, `trainee_id`, `date`, `reason`, `notes`, `justificative_filename`, `created_at`, `updated_at`) VALUES
(12, 6, '2026-09-01', 'sans_motif', '', NULL, '2026-09-11 10:35:23', NULL),
(13, 6, '2026-09-02', 'sans_motif', '', NULL, '2026-09-11 10:35:32', NULL),
(14, 6, '2026-09-03', 'sans_motif', '', NULL, '2026-09-11 10:35:41', NULL),
(15, 6, '2026-09-04', 'sans_motif', '', NULL, '2026-09-11 10:35:49', NULL),
(16, 6, '2026-09-05', 'sans_motif', '', NULL, '2026-09-11 10:35:56', NULL),
(17, 6, '2026-09-06', 'sans_motif', '', NULL, '2026-09-11 10:45:46', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260907171448', '2026-09-07 17:14:54', 9),
('DoctrineMigrations\\Version20260908080514', '2026-09-08 08:24:14', 14),
('DoctrineMigrations\\Version20260908083158', '2026-09-08 08:32:04', 19),
('DoctrineMigrations\\Version20260908084642', '2026-09-08 08:46:47', 38),
('DoctrineMigrations\\Version20260911094624', '2026-09-11 09:46:28', 29);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `trainee`
--

CREATE TABLE `trainee` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `photo_filename` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `trainee`
--

INSERT INTO `trainee` (`id`, `email`, `phone`, `first_name`, `last_name`, `photo_filename`, `date_of_birth`) VALUES
(6, 'hello@adila-k.fr', '0645557195', 'Adila', 'Kehlaoui', 'adila-6aa3c26014cb7.svg', '1990-12-18'),
(7, 'm-benerroua@gmail.com', '0767250170', 'Mohammed', 'Benerroua', 'mohammed-6aa3c2e4b3328.jpg', '1990-04-01'),
(8, 'g-bellia@gmail.com', '0662877894', 'Ghislène', 'Bellia', 'ghislene-6aa3c3266661d.png', '2005-08-25'),
(9, 'contact@campsa.fr', '0668368996', 'Aurèle', 'Camps', 'aurele-6aa3c34e3d4e3.png', '1995-11-26'),
(10, 'nelly.fabre@hotmail.fr', '0627154096', 'Nelly', 'Fabre', 'nelly-6aa3c38f6e67a.jpg', '1983-10-02'),
(11, 'sarah.casabianca@gmail.com', '0683049749', 'Sarah', 'Casabianca', 'sarah-6aa3c3cdd8d8a.png', '1996-06-10'),
(12, 'rjuan3683@gmail.com', '0635902566', 'Juan', 'Rojas Cuicas', 'juan-6aa3c4823079c.jpg', '2000-08-04'),
(13, 'merletlucas2@gmail.com', '0788697051', 'Lucas', 'Merlet', 'lucas-6aa3c4b762d57.png', '2002-09-12'),
(14, 'belmahriafatenn@gmail.com', '0602568388', 'Faten', 'Bannani', 'faten-6aa3c5000d64d.png', '1997-05-04'),
(15, 'nathanael.kenzey@gmail.com', '0745165819', 'Nathanael', 'Kenzey', 'kenzey-6aa3c5326e037.jpg', '1998-10-22'),
(16, 'anthony.lutard33@gmail.com', '0750862760', 'Anthony', 'Lutard', 'anthony-6aa3c568caf9e.jpg', '2003-12-03'),
(17, 'emel.saez@gmail.com', '0760227763', 'Mélanie', 'Saez', 'saez-6aa3c5a39f065.png', '1986-02-16');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`) VALUES
(1, 'ADMINAFPA', '[\"ROLE_ADMIN\"]', '$2y$13$JOOyCpPT1R5LUvNn87qnueAl5TVYqsUmibOLZXEG4FdxtMC72hZtW');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `absence`
--
ALTER TABLE `absence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_765AE0C936C682D0` (`trainee_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `trainee`
--
ALTER TABLE `trainee`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_USERNAME` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `absence`
--
ALTER TABLE `absence`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `trainee`
--
ALTER TABLE `trainee`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `absence`
--
ALTER TABLE `absence`
  ADD CONSTRAINT `FK_765AE0C936C682D0` FOREIGN KEY (`trainee_id`) REFERENCES `trainee` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
