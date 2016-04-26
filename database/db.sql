-- phpMyAdmin SQL Dump
-- version 4.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Dec 04, 2015 at 01:42 PM
-- Server version: 5.5.42
-- PHP Version: 5.5.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `cgeci`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_init_nouvel_exercice`(IN idExercice tinyint)
BEGIN
DECLARE gauche_parent, droite_parent SMALLINT DEFAULT NULL;

INSERT INTO membre (id_adherent, id_parrain) VALUES (idAdherent, idParrain);

SELECT gauche INTO gauche_parent FROM membre WHERE id = idParrain;
SELECT droite INTO droite_parent FROM membre WHERE id = idParrain;

UPDATE membre SET gauche = gauche + 2
  WHERE gauche > gauche_parent AND id_parrain <> 0
  ORDER BY gauche DESC;

UPDATE membre SET droite = droite + 2
  WHERE droite >= droite_parent OR (droite > gauche_parent + 1 AND droite < droite_parent)
  ORDER BY droite DESC;

UPDATE membre SET gauche = gauche_parent + 1, droite = gauche_parent + 2 WHERE id_adherent = idAdherent;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inscription_evenement`
--

CREATE TABLE `inscription_evenement` (
  `id` smallint(5) unsigned NOT NULL,
  `id_evenement` smallint(5) unsigned NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `evenement` varchar(256) NOT NULL,
  `inscris_le` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inscription_evenement`
--

INSERT INTO `inscription_evenement` (`id`, `id_evenement`, `id_user`, `evenement`, `inscris_le`) VALUES
(1, 2, 1, 'JEN', '2015-11-27 11:41:08');

-- --------------------------------------------------------

--
-- Table structure for table `inscription_formation`
--

CREATE TABLE `inscription_formation` (
  `id` smallint(5) unsigned NOT NULL,
  `id_formation` smallint(5) unsigned NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `formation` varchar(256) NOT NULL,
  `inscris_le` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `inscription_formation`
--

INSERT INTO `inscription_formation` (`id`, `id_formation`, `id_user`, `formation`, `inscris_le`) VALUES
(4, 2, 1, 'EUDONET', '0000-00-00 00:00:00'),
(5, 1, 1, 'FORMATION 1', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_salle`
--

CREATE TABLE `reservation_salle` (
  `id` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL,
  `place` varchar(64) NOT NULL,
  `date_debut` varchar(64) NOT NULL,
  `jour` varchar(64) NOT NULL,
  `heure` varchar(64) NOT NULL,
  `service` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reservation_salle`
--

INSERT INTO `reservation_salle` (`id`, `id_user`, `place`, `date_debut`, `jour`, `heure`, `service`, `created_at`) VALUES
(1, 1, '100', '24 Decembre 2015', '2', '8', 'Restauration, Hotesse', '2015-11-27 13:01:05'),
(2, 1, '4', '16 Decembre 2015', '4', '8', 'Salle climatisÃ©e avec Hotesse de preference mince et grande. ', '2015-11-27 13:14:45'),
(3, 1, '3 ', '30 Novembre 2015', '3', '4', 'sfdfsqdfqsdf sdfsdfsdf sdfsdfdsfsd', '2015-11-27 13:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `salle_formation`
--

CREATE TABLE `salle_formation` (
  `id` smallint(5) unsigned NOT NULL,
  `nom` varchar(64) NOT NULL,
  `description` mediumtext NOT NULL,
  `montant` varchar(64) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `salle_formation`
--

INSERT INTO `salle_formation` (`id`, `nom`, `description`, `montant`) VALUES
(1, 'Salle A', '50 places assises, Climatisation centrale,Video Projecteur', '200 000 FCFA / jour'),
(2, 'Salle B', '90 places assises, Split 10 chevaux, ....', '300 000 FCFA / jour');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` smallint(5) unsigned NOT NULL,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `company_name` varchar(128) NOT NULL,
  `address` varchar(64) NOT NULL,
  `phone_number` varchar(64) NOT NULL,
  `id_company_eudonet` smallint(3) unsigned NOT NULL DEFAULT '0',
  `email` varchar(64) NOT NULL,
  `login` varchar(64) NOT NULL,
  `pwd` varchar(256) NOT NULL,
  `status` tinyint(4) unsigned NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `firstname`, `lastname`, `company_name`, `address`, `phone_number`, `id_company_eudonet`, `email`, `login`, `pwd`, `status`, `created_date`, `updated_date`) VALUES
(1, 'Diallo', 'Ousmane', 'Diallo & Co', '15 BP Abidjan', '09810860', 118, 'dialloousmane2001@yahoo.fr', 'ousti', 'aaaa', 1, '2015-11-23 15:43:58', '2015-11-27 15:13:13'),
(2, 'yapi', 'Serge', 'YAPi & Co', '18 Bp Abidjan', '09 87 65 34', 0, 'eyapi@emineo.com', 'yapi', 'aaaa', 0, '2015-11-23 15:48:24', '0000-00-00 00:00:00'),
(3, '', '', '', '', '', 0, '', '', '', 0, '2015-11-27 15:26:37', '0000-00-00 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inscription_evenement`
--
ALTER TABLE `inscription_evenement`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id_formation` (`id_evenement`,`id_user`);

--
-- Indexes for table `inscription_formation`
--
ALTER TABLE `inscription_formation`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id_formation` (`id_formation`,`id_user`);

--
-- Indexes for table `reservation_salle`
--
ALTER TABLE `reservation_salle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salle_formation`
--
ALTER TABLE `salle_formation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inscription_evenement`
--
ALTER TABLE `inscription_evenement`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `inscription_formation`
--
ALTER TABLE `inscription_formation`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `reservation_salle`
--
ALTER TABLE `reservation_salle`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `salle_formation`
--
ALTER TABLE `salle_formation`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;