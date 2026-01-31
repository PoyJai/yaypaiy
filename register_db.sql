-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2026 at 07:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `register_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'poyjai', 'tae7165933za@gmail.com', '$2y$10$AsPytULKm4w1RKsgxXANZ.UezyFsX8Z9xUGcWkWO2eG4Jqop/qoi6', '2025-12-14 03:55:05'),
(2, 'p_jai_p', 'rovgamerpr@gmail.com', '$2y$10$RFB4JIAUph6itJ3CwhBe/.aX2DCg703p9K8ZrW.6.PDNZtSHMXCBy', '2025-12-14 04:01:44'),
(3, 'mongdupi', 'rovgamerpro12@gmail.com', '$2y$10$7UVLxfRmVjbK4gmvBRwkVuEXBeukbsFuy27L20Dp87Bm2rCCBIsc6', '2025-12-14 16:59:38'),
(4, 'hee', 'howardwelchir@gmail.com', '$2y$10$4Oq9ri1bkRDZMR5o2qNZL.zEgLZBWSCDic5WdLsHkp4UGGodbIsPe', '2026-01-22 11:51:48'),
(5, 'iansb_u', 'sirapatsorn290845@gmail.com', '$2y$10$NaP6aAQs9IDbOJfFJusBRuXVfIzgiLocLjlBcGG4LTkt5G4vc/awa', '2026-01-31 02:31:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
