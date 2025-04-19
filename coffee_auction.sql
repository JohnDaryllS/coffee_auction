-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2025 at 11:07 AM
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
-- Database: `coffee_auction`
--

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_anonymous` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bids`
--

INSERT INTO `bids` (`id`, `user_id`, `item_id`, `bid_amount`, `timestamp`, `is_anonymous`) VALUES
(15, 3, 7, 18.00, '2025-04-18 10:00:03', 0),
(16, 3, 9, 22.50, '2025-04-18 10:00:11', 0),
(17, 3, 8, 18.50, '2025-04-18 10:00:13', 0),
(18, 4, 9, 23.00, '2025-04-18 10:01:09', 0),
(19, 3, 7, 18.50, '2025-04-18 10:38:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `starting_price` decimal(10,2) NOT NULL,
  `bid_end_date` datetime DEFAULT NULL,
  `is_limited` tinyint(1) DEFAULT 0,
  `quantity` int(11) DEFAULT 1,
  `items_sold` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `image`, `description`, `starting_price`, `bid_end_date`, `is_limited`, `quantity`, `items_sold`) VALUES
(7, 'Colombian Supremo Dark Roast', '68020345b665d.jpg', 'Rich and bold dark roast made from 100% Colombian Supremo beans. Features a smooth, full-bodied flavor with notes of dark chocolate and toasted nuts—perfect for strong coffee lovers.', 15.00, '2025-04-18 17:46:00', 1, 3, 2),
(8, 'Guatemala Antigua Reserve', '68020ca3a4e5d.jpg', 'A smooth, medium roast coffee grown in the Antigua region of Guatemala. Offers a balanced cup with notes of cocoa, spice, and a clean, sweet finish. Sustainably sourced and artisan roasted.', 18.00, '2025-04-18 18:25:00', 1, 1, 1),
(9, 'Sumatra Mandheling Organic', '680215d81167d.jpg', 'Earthy and low-acidity coffee sourced from the lush mountains of Sumatra. This organic roast delivers deep, syrupy notes of dark chocolate, cedar, and spice—perfect for a bold cup.', 22.00, '2025-04-19 19:01:00', 0, 0, 0),
(10, 'Brazil Santos Light Roast', '68022de9c34d3.jpg', 'A light and nutty roast with smooth body and mild acidity. Grown in the Santos region of Brazil, this coffee offers delicate flavors of caramel, almond, and a touch of citrus—ideal for mellow mornings.', 13.00, '2025-04-19 10:50:00', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('pending','approved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'Admin One', 'admincoffeeauction1@coffeeauction.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', '2025-04-17 10:31:47'),
(2, 'Admin Two', 'admincoffeeauction2@coffeeauction.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', '2025-04-17 10:31:47'),
(3, 'John Daryll Sampilingan', 'johndaryllramos8@gmail.com', '09999422017', '$2y$10$dd4eg9NzvOcgVvxihgkf5.MZ4RnELHMPcN.FjH50IsF8/NzxFq2/S', 'user', 'approved', '2025-04-17 10:33:09'),
(4, 'Felsone Caragao', 'felsonecaragao@gmail.com', '09262891370', '$2y$10$sFUhO0qY.WLrgvM5XV7UZ.Hq9CGWhQJvLXeIUrTjFiPt5R6278uO2', 'user', 'approved', '2025-04-17 14:20:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
