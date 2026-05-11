-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 10, 2025 at 11:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `db_BD` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_BD`;

--
-- Database: `db_BD`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(100) NOT NULL COMMENT 'Adresse email de l’utilisateur',
  `PW` varchar(255) NOT NULL COMMENT 'Mot de passe haché de l’utilisateur',
  `TOTPOINTS` int(11) DEFAULT 0 COMMENT 'Total des points cumulés des quiz',
  `CREATED_AT` timestamp NOT NULL DEFAULT current_timestamp(),
  `PHOTO` varchar(255) DEFAULT NULL COMMENT 'Chemin ou URL de la photo de profil'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `USERNAME`, `PW`, `TOTPOINTS`, `CREATED_AT`, `PHOTO`) VALUES
(1, 'user@univ.com', 'user', 1, '2025-11-10 16:47:28', 'img/user1.png'),
(2, 'admin@gmail.com', 'admin', 0, '2025-11-10 16:47:28', 'img/admin.png'),
(3, 'student6@univ.com', 'pass666', 2, '2025-11-10 17:56:08', 'img/user3.png'),
(4, 'etudiant1@univ.com', 'pass123', 15, '2025-11-10 19:10:52', 'https://i.pravatar.cc/150?img=1'),
(5, 'etudiant2@univ.com', 'pass456', 25, '2025-11-10 19:10:53', 'https://i.pravatar.cc/150?img=2'),
(6, 'etudiant3@univ.com', 'pass789', 5, '2025-11-10 19:10:53', 'https://i.pravatar.cc/150?img=3'),
(7, 'etudiant4@univ.com', 'nouveau', 0, '2025-11-10 19:10:53', 'https://i.pravatar.cc/150?img=4'),
(8, 'etudiant5@univ.com', 'pass555', 42, '2025-11-10 19:10:53', 'https://i.pravatar.cc/150?img=5'),
(9, 'etudiant_issat@univ.com', 'ssss', 42, '2025-11-10 20:59:07', 'https://i.pravatar.cc/150?img=5');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `USERNAME` (`USERNAME`),
  ADD UNIQUE KEY `USERNAME_2` (`USERNAME`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
