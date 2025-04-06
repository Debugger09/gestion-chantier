-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 06 avr. 2025 à 03:54
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
-- Base de données : `chantier_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `chantiers`
--

CREATE TABLE `chantiers` (
  `id` int(11) NOT NULL,
  `nom_chantier` varchar(100) NOT NULL,
  `localisation` varchar(100) NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `id_chef` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `chantiers`
--

INSERT INTO `chantiers` (`id`, `nom_chantier`, `localisation`, `date_debut`, `date_fin`, `id_chef`, `client_id`) VALUES
(1, 'lulu', 'douala', '2025-03-31', '2025-04-27', 6, 1);

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `email`, `telephone`, `adresse`) VALUES
(1, 'lulu ange', 'kuitoange@gmail.com', '677064991', 'bonamoussadi');

-- --------------------------------------------------------

--
-- Structure de la table `demandes`
--

CREATE TABLE `demandes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `chantier_id` int(11) DEFAULT NULL,
  `materiel_id` int(11) DEFAULT NULL,
  `quantite_demandee` int(11) NOT NULL,
  `statut` enum('en_attente','validee','refusee') DEFAULT 'en_attente',
  `date_demande` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_validation` datetime DEFAULT NULL,
  `validateur_id` int(11) DEFAULT NULL,
  `commentaire_validation` text DEFAULT NULL,
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `demandes`
--

INSERT INTO `demandes` (`id`, `user_id`, `chantier_id`, `materiel_id`, `quantite_demandee`, `statut`, `date_demande`, `date_validation`, `validateur_id`, `commentaire_validation`, `commentaire`) VALUES
(1, 6, 1, 1, 4, 'validee', '2025-04-06 00:12:43', '2025-04-06 02:23:43', 5, NULL, 'jhu'),
(2, 6, 1, 1, 1, 'validee', '2025-04-06 00:15:20', '2025-04-06 02:21:37', 5, NULL, 'jjj'),
(3, 6, 1, 3, 6, 'validee', '2025-04-06 00:26:45', '2025-04-06 02:30:52', 5, NULL, 'hhh'),
(4, 6, 1, 3, 3, 'validee', '2025-04-06 00:27:17', '2025-04-06 02:27:36', 5, NULL, 'hh'),
(5, 6, 1, 3, 1, 'refusee', '2025-04-06 00:32:33', '2025-04-06 02:55:31', 5, NULL, 'jj'),
(13, 6, 1, 1, 1, 'refusee', '2025-04-06 01:05:55', '2025-04-06 03:08:28', 5, NULL, 'mm');

-- --------------------------------------------------------

--
-- Structure de la table `liste_materiel`
--

CREATE TABLE `liste_materiel` (
  `id` int(11) NOT NULL,
  `chantier_id` int(11) DEFAULT NULL,
  `materiel_id` int(11) DEFAULT NULL,
  `quantite_utilisee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `liste_materiel`
--

INSERT INTO `liste_materiel` (`id`, `chantier_id`, `materiel_id`, `quantite_utilisee`) VALUES
(1, 1, 1, 1),
(2, 1, 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `materiels`
--

CREATE TABLE `materiels` (
  `id` int(11) NOT NULL,
  `nom_materiel` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `quantite_disponible` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `materiels`
--

INSERT INTO `materiels` (`id`, `nom_materiel`, `description`, `quantite_disponible`) VALUES
(1, 'laptop', 'new', 4),
(3, 'iii', 'iii', 36);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','chef_chantier') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `created_at`) VALUES
(5, 'Kuito ange', 'lulu@gmail.com', '$2b$12$G2fqoLegC6GtChTORP2E.u1r0xMu2NQ5UIkXi7pLMS0RbNz1YVyJe', 'admin', '2025-04-05 18:40:30'),
(6, 'Ouandji lugresse', 'miss@gmail.com', '$2b$12$G2fqoLegC6GtChTORP2E.u1r0xMu2NQ5UIkXi7pLMS0RbNz1YVyJe', 'chef_chantier', '2025-04-05 18:41:58'),
(8, 'chef', 'chef@gmail.com', '$2y$10$R7I46sSzyHI15cAd/bk5weEzc/YGqtcDOKGzKoR9QB0c6Hwh20RFm', 'chef_chantier', '2025-04-06 01:22:48');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `chantiers`
--
ALTER TABLE `chantiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_chef` (`id_chef`),
  ADD KEY `client_id` (`client_id`);

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demandes`
--
ALTER TABLE `demandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chantier_id` (`chantier_id`),
  ADD KEY `materiel_id` (`materiel_id`),
  ADD KEY `validateur_id` (`validateur_id`);

--
-- Index pour la table `liste_materiel`
--
ALTER TABLE `liste_materiel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chantier_id` (`chantier_id`),
  ADD KEY `materiel_id` (`materiel_id`);

--
-- Index pour la table `materiels`
--
ALTER TABLE `materiels`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `chantiers`
--
ALTER TABLE `chantiers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `demandes`
--
ALTER TABLE `demandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `liste_materiel`
--
ALTER TABLE `liste_materiel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `materiels`
--
ALTER TABLE `materiels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `chantiers`
--
ALTER TABLE `chantiers`
  ADD CONSTRAINT `chantiers_ibfk_1` FOREIGN KEY (`id_chef`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chantiers_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `demandes`
--
ALTER TABLE `demandes`
  ADD CONSTRAINT `demandes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_ibfk_2` FOREIGN KEY (`chantier_id`) REFERENCES `chantiers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_ibfk_3` FOREIGN KEY (`materiel_id`) REFERENCES `materiels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `demandes_ibfk_4` FOREIGN KEY (`validateur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `liste_materiel`
--
ALTER TABLE `liste_materiel`
  ADD CONSTRAINT `liste_materiel_ibfk_1` FOREIGN KEY (`chantier_id`) REFERENCES `chantiers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `liste_materiel_ibfk_2` FOREIGN KEY (`materiel_id`) REFERENCES `materiels` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
