-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 18 juin 2026 à 00:19
-- Version du serveur : 5.7.40
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `entreprise`
--

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id_client` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `mail` varchar(150) NOT NULL,
  `telephone` varchar(30) NOT NULL,
  `adresse` varchar(80) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_client`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom`, `prenom`, `mail`, `telephone`, `adresse`, `id_user`) VALUES
(1, 'sagesse', 'andreche', 'sagesse@gmail.com', '+212635766873', 'Meknès', 3);

-- --------------------------------------------------------

--
-- Structure de la table `employe`
--

DROP TABLE IF EXISTS `employe`;
CREATE TABLE IF NOT EXISTS `employe` (
  `id_emp` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `poste` varchar(100) NOT NULL,
  PRIMARY KEY (`id_emp`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `employe`
--

INSERT INTO `employe` (`id_emp`, `id_user`, `poste`) VALUES
(1, 4, 'caissier'),
(2, 5, 'portier');

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

DROP TABLE IF EXISTS `factures`;
CREATE TABLE IF NOT EXISTS `factures` (
  `id_fact` int(11) NOT NULL AUTO_INCREMENT,
  `numero_fact` int(11) NOT NULL,
  `date_fact` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `mode_paiement` varchar(200) NOT NULL,
  `id_vente` int(11) NOT NULL,
  PRIMARY KEY (`id_fact`),
  UNIQUE KEY `id_vente_2` (`id_vente`),
  KEY `id_vente` (`id_vente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ligne_vente`
--

DROP TABLE IF EXISTS `ligne_vente`;
CREATE TABLE IF NOT EXISTS `ligne_vente` (
  `id_ligne` int(11) NOT NULL AUTO_INCREMENT,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `sous_total` decimal(10,2) NOT NULL,
  `id_prod` int(11) NOT NULL,
  `id_vente` int(11) NOT NULL,
  PRIMARY KEY (`id_ligne`),
  KEY `id_prod` (`id_prod`,`id_vente`),
  KEY `fk_ligne_vente_vente` (`id_vente`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ligne_vente`
--

INSERT INTO `ligne_vente` (`id_ligne`, `quantite`, `prix_unitaire`, `sous_total`, `id_prod`, `id_vente`) VALUES
(1, 1, '299.99', '299.99', 1, 1),
(2, 2, '9.00', '18.00', 2, 2),
(3, 2, '9.00', '18.00', 2, 3);

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

DROP TABLE IF EXISTS `produits`;
CREATE TABLE IF NOT EXISTS `produits` (
  `id_prod` int(11) NOT NULL AUTO_INCREMENT,
  `nom_produit` varchar(100) NOT NULL,
  `description` varchar(300) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  PRIMARY KEY (`id_prod`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_prod`, `nom_produit`, `description`, `prix`, `stock`) VALUES
(1, 'Clavier', 'Clavier mécanique RGB', '299.99', 9),
(2, 'huile', 'huile vegetal de bonne qualité', '9.00', 96);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `mail` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `role` enum('admin','client','employe') NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_user`, `nom`, `prenom`, `mail`, `password`, `adresse`, `telephone`, `role`) VALUES
(5, 'sagesse', 'Andrèche', 'andreche1bouetoussa@gmail.com', '$2y$10$YBnDLVaDX9tzJrrHdNlqaeje7H5t5qU5dHVKmMo7VcoPbeQSepJ1u', 'Meknès', '+212780703023', 'employe'),
(4, 'BOUETOUSSA', 'Andrèche', 'andrechebouetoussa@gmail.com', '$2y$10$qND6hi1e.hdq8ha2hmE.yODCbJG7oQdKX/tP8LZWfignbT6.N7pJK', 'Meknès', '+212635766873', 'employe');

-- --------------------------------------------------------

--
-- Structure de la table `vente`
--

DROP TABLE IF EXISTS `vente`;
CREATE TABLE IF NOT EXISTS `vente` (
  `id_vente` int(11) NOT NULL AUTO_INCREMENT,
  `date_vente` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_emp` int(11) NOT NULL,
  PRIMARY KEY (`id_vente`),
  KEY `id_client` (`id_client`,`id_emp`),
  KEY `fk_vente_employe` (`id_emp`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `vente`
--

INSERT INTO `vente` (`id_vente`, `date_vente`, `total`, `id_client`, `id_emp`) VALUES
(1, '2026-06-17 22:30:41', '299.99', 1, 3),
(2, '2026-06-17 23:31:14', '18.00', 1, 3),
(3, '2026-06-17 23:32:48', '18.00', 1, 3);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
