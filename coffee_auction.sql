-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2025 at 03:08 PM
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
(16, 3, 9, 22.50, '2025-04-18 10:00:11', 0),
(18, 4, 9, 23.00, '2025-04-18 10:01:09', 0),
(20, 3, 9, 23.50, '2025-04-19 11:10:59', 1),
(21, 3, 14, 22.50, '2025-04-19 12:01:52', 1),
(22, 4, 14, 23.00, '2025-04-19 12:02:36', 1),
(23, 3, 9, 24.00, '2025-04-20 04:24:02', 1),
(24, 3, 11, 25.50, '2025-04-20 05:49:33', 1),
(25, 5, 9, 24.50, '2025-04-20 06:00:54', 1),
(26, 5, 10, 13.50, '2025-04-20 06:19:32', 1),
(27, 5, 10, 14.00, '2025-04-20 06:24:09', 1);

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
  `bid_start_date` datetime DEFAULT NULL,
  `is_limited` tinyint(1) DEFAULT 0,
  `quantity` int(11) DEFAULT 1,
  `items_sold` int(11) DEFAULT 0,
  `notified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `image`, `description`, `starting_price`, `bid_end_date`, `bid_start_date`, `is_limited`, `quantity`, `items_sold`, `notified`) VALUES
(9, 'Sumatra Mandheling Organic', '680215d81167d.jpg', 'Earthy and low-acidity coffee sourced from the lush mountains of Sumatra. This organic roast delivers deep, syrupy notes of dark chocolate, cedar, and spice—perfect for a bold cup.', 23.00, '2025-04-30 03:01:00', '2025-04-28 18:34:00', 0, 0, 0, 1),
(10, 'Brazil Santos Light Roast', '68022de9c34d3.jpg', 'A light and nutty roast with smooth body and mild acidity. Grown in the Santos region of Brazil, this coffee offers delicate flavors of caramel, almond, and a touch of citrus—ideal for mellow mornings.', 13.00, '2025-04-22 02:50:00', NULL, 0, 0, 0, 1),
(11, 'Kenya AA Plus Peaberry', '680371b3eea5a.jpg', 'A rare peaberry coffee from Kenya, known for its vibrant acidity and wine-like undertones. This medium roast delivers juicy berry notes with a smooth, bright finish—perfect for pour-overs or cold brew.', 25.00, '2025-04-23 04:48:00', NULL, 1, 2, 1, 1),
(12, 'Honduras Marcala Honey Process', '6803720360a9c.jpg', 'A specialty coffee with a sweet and creamy profile, thanks to the honey processing method. Grown in the highlands of Marcala, it features smooth notes of honey, red apple, and vanilla with a silky body.', 17.00, '2025-04-30 16:50:00', '2025-04-28 19:17:00', 0, 0, 0, 0),
(13, 'Ethiopia Yirgacheffe Floral Roast', '680372505bfe5.jpg', 'A bright, aromatic coffee from the Yirgacheffe region of Ethiopia. This light roast boasts floral notes of jasmine and lavender, with a tea-like body and hints of lemon zest—an elegant and refreshing cup.', 20.00, '2025-05-02 14:51:00', '2025-04-30 00:00:00', 0, 0, 0, 0),
(14, 'Sumatra Mandheling Earthy Roast', '680372a7cd5ae.jpg', 'An intense, low-acidity coffee from the Indonesian highlands. This medium-dark roast features bold earthy tones, dark chocolate, and a touch of spice—perfect for espresso or a strong morning brew.', 22.00, '2025-04-24 17:53:00', NULL, 0, 0, 0, 1),
(15, 'Panama Geisha Elite Bloom', '68049579547f9.png', 'World-renowned for its floral aroma and complex flavors, this Geisha variety offers a silky body with jasmine, bergamot, and apricot tasting notes. A luxurious cup for true connoisseurs.', 45.00, '2025-05-30 20:00:00', '2025-05-17 14:00:00', 0, 0, 0, 0),
(16, 'Brazil Cerrado Nutty Roast', '680495ebc0565.jpg', 'A smooth and balanced roast with notes of roasted hazelnut, caramel, and milk chocolate. Perfect for drip or French press, this Brazilian coffee delivers a consistent, comforting brew.', 16.00, '2025-04-30 06:35:00', '2025-04-28 17:18:00', 0, 0, 0, 0),
(17, 'Costa Rica Tarrazú Citrus Wave', '68049683879cb.png', 'Clean, crisp, and zesty. This light-medium roast bursts with bright citrus notes, red currant, and a hint of honey. Grown in high altitudes for a vibrant cup.Clean, crisp, and zesty. This light-medium roast bursts with bright citrus notes, red currant, and a hint of honey. Grown in high altitudes for a vibrant cup.', 19.00, '2025-11-20 08:37:00', '2025-06-21 14:00:00', 0, 0, 0, 0),
(18, 'Rwanda Bourbon Red', '680496db2c0e6.jpg', 'Grown on the rich volcanic soils of Rwanda, this bourbon varietal shines with cranberry, pomegranate, and floral hints. Great for adventurous palates.', 20.97, '2025-04-30 20:40:00', '2025-04-28 17:19:00', 0, 0, 0, 0),
(19, 'Java Estate Midnight Roast', '68049741d7500.jpg', 'A bold Indonesian dark roast with a velvety body, smoky aroma, and flavors of molasses, tobacco, and cedar. Strong and smooth, ideal for night owls.', 20.00, '2025-05-14 22:41:00', '2025-04-28 19:13:00', 0, 0, 0, 0),
(20, 'Guatemala Huehuetenango Sweet Harmony', '680497c13be12.jpg', 'Delicate and sweet with notes of brown sugar, red apple, and floral undertones. This medium roast from Huehuetenango offers balance and clarity in every sip.', 18.00, '2025-04-27 19:46:00', NULL, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 'Welcome to Coffee Auction! Your account has been approved.', 1, '2025-04-20 00:00:00'),
(2, 3, 'You placed a bid of ₱22.50 on Sumatra Mandheling Organic', 1, '2025-04-20 00:05:00'),
(3, 4, 'Welcome to Coffee Auction! Your account has been approved.', 1, '2025-04-20 00:00:00'),
(4, 4, 'Someone outbid you on Sumatra Mandheling Organic with ₱23.00', 1, '2025-04-20 00:10:00'),
(5, 3, 'You placed a bid of ₱25.50 on Kenya AA Plus Peaberry', 1, '2025-04-20 05:49:33'),
(6, 5, 'Welcome to Coffee Auction! Your account is pending admin approval.', 1, '2025-04-20 05:53:28'),
(7, 5, 'Your account has been approved! You can now place bids.', 1, '2025-04-20 05:53:44'),
(8, 5, 'You placed a bid of ₱24.50 on Sumatra Mandheling Organic', 1, '2025-04-20 06:00:54'),
(9, 3, 'Someone outbid you on Sumatra Mandheling Organic with ₱24.50', 1, '2025-04-20 06:00:54'),
(10, 5, 'You placed a bid of ₱13.50 on Brazil Santos Light Roast', 1, '2025-04-20 06:19:32'),
(11, 5, 'You placed a bid of ₱14.00 on Brazil Santos Light Roast', 1, '2025-04-20 06:24:09'),
(12, 5, 'Your account has been suspended. Please contact support.', 0, '2025-04-20 09:29:04'),
(13, 5, 'Your account has been reactivated. Welcome back!', 0, '2025-04-20 09:29:09'),
(14, 4, 'Congratulations! You won the auction for Sumatra Mandheling Earthy Roast with a bid of ₱23.00', 0, '2025-04-26 13:39:08'),
(15, 3, 'Congratulations! You won the auction for Kenya AA Plus Peaberry with a bid of ₱25.50', 1, '2025-04-26 13:39:08'),
(16, 5, 'Congratulations! You won the auction for Sumatra Mandheling Organic with a bid of ₱24.50', 0, '2025-04-26 13:39:08'),
(17, 5, 'Congratulations! You won the auction for Brazil Santos Light Roast with a bid of ₱14.00', 0, '2025-04-26 13:39:08'),
(18, 5, 'Your account has been suspended. Please contact support.', 0, '2025-04-28 08:29:04'),
(19, 5, 'Your account has been reactivated. Welcome back!', 0, '2025-04-28 08:29:12'),
(20, 5, 'Your account has been suspended. Please contact support.', 0, '2025-04-28 10:50:58'),
(21, 5, 'Your account has been reactivated. Welcome back!', 0, '2025-04-28 10:51:01');

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
  `status` enum('pending','approved','suspended') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'Admin One', 'admincoffeeauction1@coffeeauction.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', '2025-04-17 10:31:47'),
(2, 'Admin Two', 'admincoffeeauction2@coffeeauction.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved', '2025-04-17 10:31:47'),
(3, 'John Daryll Sampilingan', 'johndaryllramos8@gmail.com', '09999422017', '$2y$10$dd4eg9NzvOcgVvxihgkf5.MZ4RnELHMPcN.FjH50IsF8/NzxFq2/S', 'user', 'approved', '2025-04-17 10:33:09'),
(4, 'Felsone Caragao', 'felsonecaragao@gmail.com', '09262891370', '$2y$10$6w61p./ZJaRrZhUsBsi5jeUeSnoz2tvUGXqQhypY0foAy5qow3VCW', 'user', 'approved', '2025-04-17 14:20:10'),
(5, 'Johnny Gayo', 'johnnygayo@gmail.com', '09066043962', '$2y$10$eqTlESjMEtdbr8l8bqeWZuIzbqbCr7zhSJ/J5asEO3p2eoWawvcC2', 'user', 'approved', '2025-04-20 05:53:28');

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bids`
--
ALTER TABLE `bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
