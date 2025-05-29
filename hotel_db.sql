-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 29 mai 2025 à 09:05
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `hotel_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `user_id` varchar(20) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` varchar(10) NOT NULL,
  `rooms` int NOT NULL,
  `check_in` varchar(10) NOT NULL,
  `check_out` varchar(10) NOT NULL,
  `adults` int NOT NULL,
  `childs` int NOT NULL,
  `payer` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `bookings`
--

INSERT INTO `bookings` (`user_id`, `booking_id`, `name`, `email`, `number`, `rooms`, `check_in`, `check_out`, `adults`, `childs`, `payer`) VALUES
('9vWvXXxdjWckeDtHtj7p', '7JwUFqaSHC3bowXrYUke', 'abc', 'abc@gmail.com', '666666666', 2, '2024-02-22', '2024-03-31', 2, 2, 1),
('9vWvXXxdjWckeDtHtj7p', 'KqcyBxurBhufc6WMBwQB', 'cc', 'test@example.com', '238492', 2, '2025-02-22', '2025-02-28', 2, 2, 1),
('OcBi8T1w2RfkcRV2nHGo', '5LSw8mdoaE6HABdExcN1', 'bonjour monde', 'islemhajjem11@gmail.com', '06777777', 3, '2025-02-27', '2025-03-06', 2, 3, 1),
('OcBi8T1w2RfkcRV2nHGo', 'wIpMo8Z2MpONjpvr4A2g', 'bonjour Hello ', 'islemhajjem12@gmail.com', '06666666', 2, '2025-02-28', '2025-03-07', 2, 2, 1),
('OcBi8T1w2RfkcRV2nHGo', 'iTm1yxWbOiFKdkyIcYOm', 'Hello Hello ', 'hellohello@gmail.com', '06777776', 2, '2025-03-08', '2025-03-06', 2, 2, 1),
('SszuqrmSkAaAb7mwF1NL', 'aLRFHI53c0U4wgjcURGV', 'Lili bonjour', 'abc@gmail.com', '06222222', 3, '2025-04-24', '2025-05-01', 2, 2, 1),
('683037', 'kTRGduzfbetXeAwPEmNH', 'user user', 'user@gmail.com', '0222222222', 2, '2025-05-24', '2025-05-31', 2, 1, 0),
('683037', 'W0AnpO721IwS83KEkio8', 'user user', 'user@gmail.com', '0222222222', 1, '2025-05-24', '2025-05-31', 3, 1, 1),
('683037', 'EhiVuVeMyS2LnJo8MSht', 'user user', 'user@gmail.com', '0222222222', 2, '2025-05-25', '2025-06-01', 1, 1, 0),
('683244', 'yLKPnExQSn4nTYZipYrp', 'user user1', 'user1@gmail.com', '022222233', 2, '2025-05-30', '2025-06-08', 2, 1, 0),
('2147483657', 'obUm0XJBzgCy3bqvlMac', 'user user2', 'user2@gmail.com', '011111111', 2, '2025-06-03', '2025-06-06', 1, 2, 1),
('2147483659', 'uawMEjoBnjl9oZZuUKuX', 'user user2', 'user2@gmail.com', '011111111', 2, '2025-06-04', '2025-06-07', 2, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id_message`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id_message`, `nom`, `email`, `subject`, `message`) VALUES
(10, 'bonjour bonjour', 'bonjour@gmail.com', 'service', 'hgjgljgljgjl'),
(11, 'user user2', 'user2@gmail.com', 'service', 'hgjhgljghj'),
(8, 'bonjour monde', 'islemhajjem11@gmail.com', 'service', 'FGHFHFKHFHFKHF'),
(12, 'user user2', 'user2@gmail.com', 'product', 'pannnenne'),
(4, 'jgjhg', 'islemhajjem1@gmail.com', 'product', 'jgjhgjhgkhfhfhg'),
(5, 'dfsdfs', 'islemhajjem1@gmail.com', 'service', 'fsfsdf');

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` text NOT NULL,
  `card_number` varchar(16) NOT NULL,
  `card_expiry` varchar(16) NOT NULL,
  `card_cvc` varchar(3) NOT NULL,
  `amount` text NOT NULL,
  `transaction_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`id`, `booking_id`, `card_number`, `card_expiry`, `card_cvc`, `amount`, `transaction_date`) VALUES
(33, 'uawMEjoBnjl9oZZuUKuX', '123344535', '23/26', '123', '842.4', '2025-05-27 20:43:41'),
(32, 'uawMEjoBnjl9oZZuUKuX', '123344535', '23/26', '123', '842.4', '2025-05-27 20:43:41'),
(31, 'obUm0XJBzgCy3bqvlMac', '123234345', '23/27', '123', '841.2', '2025-05-27 20:29:51'),
(30, 'obUm0XJBzgCy3bqvlMac', '123234345', '23/27', '123', '841.2', '2025-05-27 20:29:51'),
(29, 'QiM0ZHp5r1k1UspbiQ4I', '13234556', '23/27', '123', '985.6', '2025-05-27 18:41:08'),
(28, 'QiM0ZHp5r1k1UspbiQ4I', '13234556', '23/27', '123', '985.6', '2025-05-27 18:41:08'),
(20, 'aLRFHI53c0U4wgjcURGV', '2123483458234975', '23/30', '234', '2945.6', '2025-04-24 17:25:17'),
(21, 'W0AnpO721IwS83KEkio8', '1223', '23/25', '123', '988.4', '2025-05-25 09:56:15'),
(22, 'W0AnpO721IwS83KEkio8', '1223', '23/25', '123', '988.4', '2025-05-25 09:56:59'),
(23, 'W0AnpO721IwS83KEkio8', '1223', '23/25', '123', '988.4', '2025-05-25 09:56:59'),
(24, 'W0AnpO721IwS83KEkio8', '1223', '23/25', '123', '988.4', '2025-05-25 09:57:37'),
(25, 'W0AnpO721IwS83KEkio8', '1223', '23/25', '123', '988.4', '2025-05-25 09:57:37'),
(26, 'W0AnpO721IwS83KEkio8', '1223', '23/25', '123', '988.4', '2025-05-25 09:57:57'),
(27, 'W0AnpO721IwS83KEkio8', '1223', '23/25', '123', '988.4', '2025-05-25 09:57:57');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `number` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `salt` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2147483661 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `number`, `name`, `role`, `salt`) VALUES
(20, 'bonjour@gmail.com', '$2y$10$wZgJw94sQHpUuTjpJUqGMOVY1CMk.VCwL/vHwBuKAnDz.y6ZdX7lu', '06666666', 'bonjour bonjour', 'user', ''),
(18, 'abc@gmail.com', '$2y$10$y43La5p31uLIoipQUqWeQuMYDtAvOv14FjK1LxZYBe0KXqbn7LzeG', '06222222', 'coucou coucou ', 'user', ''),
(2147483659, 'user2@gmail.com', '$2y$10$ApOQxASjBGmlrBNwJfe8SelaSt767XUDqi4QnZroFXuy0t/eIlObO', '011111111', 'user user2', 'user', ''),
(2147483660, 'admin2@gmail.com', '$2y$10$4n6OUWC.VpO7O.c0sIAe1O6OWqQ0qPQXaSdcUbwEXi9paZAp0bOAC', '', 'admin2', 'admin', ''),
(2147483655, 'user3@gmail.com', '$2y$10$Hy.rh0vUEqwuBtkRFK0/sePc9Zw5VzJrEnEeeSRvTWjKuVE0GjKQm', '0111111111', 'user user3', 'user', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
