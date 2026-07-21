-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2026 at 04:49 PM
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
-- Database: `golden_crust`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `fullname`, `phone`) VALUES
(1, 'admin', '123', 'admin123@gmail.com', 'Super Admin', '9876543210'),
(6, 'demo', '123456789', '', NULL, NULL),
(7, 'bansi', '185207', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cakes`
--

CREATE TABLE `cakes` (
  `id` int(11) NOT NULL,
  `itemname` varchar(255) NOT NULL,
  `categories` enum('Chocolate','Fruit','Bundt','Velvet','Celebration','Ice Cream','Cupcake','Roll','Pastry') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cakes`
--

INSERT INTO `cakes` (`id`, `itemname`, `categories`, `price`, `img`, `description`, `stock`, `created_at`) VALUES
(15, 'Royal Chocolate Floral  Cake', 'Chocolate', 8000.00, '1784296495_0.jpg', 'A luxurious 4-tier chocolate cake decorated with handcrafted chocolate swirls and elegant white sugar flowers. Perfect for weddings, receptions, and premium celebrations.', 5, '2026-07-17 13:54:55'),
(16, 'Classic Chocolate Truffle Cake', 'Chocolate', 899.00, '1784296552_0.jpg', 'Soft chocolate sponge layered with rich chocolate truffle cream and topped with premium dark chocolate shards for an irresistible taste.', 20, '2026-07-17 13:55:52'),
(17, 'Chocolate Heart Bliss Cake', 'Chocolate', 600.00, '1784296610_0.jpg', 'Heart-shaped rich chocolate cake with glossy mirror glaze, smooth chocolate ganache, and cherry decoration. Perfect for anniversaries and romantic occasions.', 15, '2026-07-17 13:56:50'),
(18, 'Divine Almond Chocolate Truffle Cake', 'Chocolate', 1299.00, '1784296676_0.webp', 'Premium chocolate truffle cake coated with crunchy roasted almonds, finished with rich chocolate drizzle and handcrafted chocolate decorations.', 12, '2026-07-17 13:57:56'),
(19, 'Premium Chocolate Mirror Cake', 'Chocolate', 1499.00, '1784296724_0.jpg', 'Rich chocolate sponge covered with smooth mirror glaze, topped with chocolate hearts, nuts, and delicious chocolate ganache.', 10, '2026-07-17 13:58:44'),
(20, 'Chocolate Rose Elegance Cake', 'Chocolate', 1599.00, '1784296763_0.webp', 'Premium chocolate cake decorated with handcrafted chocolate roses, glossy chocolate glaze, and creamy chocolate frosting for a luxurious experience.', 8, '2026-07-17 13:59:24'),
(22, 'Heart Fruit Delight Cake', 'Fruit', 1399.00, '1784297939.jpg', 'Soft vanilla sponge layered with fresh whipped cream and decorated with strawberries, blueberries, raspberries, cherries, macarons, and seasonal fruits. Perfect for birthdays and anniversaries.', 12, '2026-07-17 14:18:59'),
(23, 'Royal Fruit Chocolate Drip Cake', 'Fruit', 1899.00, '1784297977.webp', 'Premium square fruit cake topped with fresh strawberries, grapes, blueberries, raspberries, and finished with rich chocolate drip. A luxurious cake for special celebrations.', 8, '2026-07-17 14:19:37'),
(24, 'Premium Fresh Fruit Fantasy Cake', 'Fruit', 1499.00, '1784298066.jpg', 'Delicious vanilla cream cake decorated with kiwi, apples, oranges, strawberries, cherries, and handcrafted chocolate pieces. Fresh, colorful, and full of fruity flavor.', 10, '2026-07-17 14:21:06'),
(25, 'Premium Fresh Fruit Fantasy Cake', 'Fruit', 1499.00, '1784298080.jpg', 'Delicious vanilla cream cake decorated with kiwi, apples, oranges, strawberries, cherries, and handcrafted chocolate pieces. Fresh, colorful, and full of fruity flavor.', 10, '2026-07-17 14:21:20'),
(26, 'Chocolate Berry Fruit Cake', 'Fruit', 1699.00, '1784298111.jpg', 'Moist chocolate cake covered with silky chocolate ganache and topped with fresh strawberries, blueberries, raspberries, kiwi, and premium chocolate drizzle.', 9, '2026-07-17 14:21:51'),
(27, 'Classic Mixed Fruit Cake', 'Fruit', 999.00, '1784298145.jpg', 'Soft vanilla sponge with whipped cream frosting, topped with apples, kiwi, grapes, oranges, cherries, chocolate sticks, and colorful sprinkles. A timeless bakery favorite.', 10, '2026-07-17 14:22:25'),
(28, 'Fresh Berry Chocolate Drip Cake', 'Fruit', 1599.00, '1784298174.jpg', 'Smooth vanilla cream cake finished with chocolate drip and loaded with fresh strawberries, blueberries, raspberries, kiwi, and cherries. Perfect for birthdays and family celebrations.', 11, '2026-07-17 14:22:54');

-- --------------------------------------------------------

--
-- Table structure for table `cake_images`
--

CREATE TABLE `cake_images` (
  `id` int(11) NOT NULL,
  `cake_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cake_images`
--

INSERT INTO `cake_images` (`id`, `cake_id`, `image`) VALUES
(111, 142, '1782908428_0.jpg'),
(112, 142, '1782908428_1.jpg'),
(113, 142, '1782908428_2.jpg'),
(114, 142, '1782908428_3.webp'),
(115, 142, '1782908428_4.jpg'),
(116, 142, '1782908428_5.webp'),
(117, 143, '1782911173_0.webp'),
(118, 143, '1782911173_1.jpg'),
(119, 143, '1782911173_2.jpg'),
(120, 143, '1782911173_3.jpg'),
(121, 143, '1782911173_4.jpg'),
(122, 144, '1783261367_0.webp'),
(123, 144, '1783261367_1.jpg'),
(124, 144, '1783261367_2.jpg'),
(125, 144, '1783261367_3.jpg'),
(126, 144, '1783261367_4.jpg'),
(127, 144, '1783261367_5.jpg'),
(148, 6, '1783267843_0.webp'),
(149, 6, '1783267843_1.jpg'),
(150, 6, '1783267843_2.jpg'),
(151, 6, '1783267843_3.jpg'),
(152, 6, '1783267843_4.jpg'),
(153, 7, '1783268195_0.webp'),
(154, 7, '1783268195_1.jpg'),
(155, 7, '1783268195_2.jpg'),
(156, 7, '1783268195_3.jpg'),
(157, 7, '1783268195_4.jpg'),
(158, 7, '1783268195_5.jpg'),
(159, 7, '1783268195_6.webp'),
(160, 7, '1783268195_7.webp'),
(180, 1, 'chocolate_sub1.jpg'),
(181, 1, 'chocolate_sub2.jpg'),
(182, 1, 'chocolate_sub3.jpg'),
(183, 2, 'strawberry_sub1.jpg'),
(184, 2, 'strawberry_sub2.jpg'),
(185, 3, 'vanilla_sub1.jpg'),
(186, 3, 'vanilla_sub2.jpg'),
(195, 15, '1784296495_0.jpg'),
(196, 16, '1784296552_0.jpg'),
(197, 17, '1784296610_0.jpg'),
(198, 18, '1784296676_0.webp'),
(199, 19, '1784296724_0.jpg'),
(200, 20, '1784296763_0.webp');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `itemname` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total` decimal(10,2) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `itemname`, `price`, `quantity`, `total`, `img`, `username`, `created_at`) VALUES
(18, 13, 28, 'Fresh Berry Chocolate Drip Cake', 1599.00, 1, 1599.00, '1784298174.jpg', 'demo', '2026-07-17 14:26:24'),
(19, 13, 18, 'Divine Almond Chocolate Truffle Cake', 1299.00, 1, 1299.00, '1784296676_0.webp', 'demo', '2026-07-17 14:26:30');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `mno` varchar(15) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `order_notes` text DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `username`, `total_amount`, `address`, `phone`, `payment_method`, `order_notes`, `order_date`, `status`) VALUES
(1, 13, 'demo', 1500.00, 'junagadh', '9854765201', 'COD', '', '2026-07-10 19:57:02', 'Pending'),
(2, 13, 'demo', 600.00, 'junagadh', '1458754269', 'Online', '', '2026-07-10 19:57:36', 'Pending'),
(3, 13, 'demo', 2600.00, 'junagadh', '9854765201', 'COD', '', '2026-07-10 20:11:32', 'Pending'),
(4, 13, 'demo', 1000.00, 'junagadh', '9854765201', 'COD', '', '2026-07-11 11:24:25', 'Pending'),
(5, 10, 'user', 2298.00, 'junagadh', '1245876952', 'COD', '', '2026-07-17 16:27:38', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `itemname` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `itemname`, `price`, `quantity`, `total`, `img`) VALUES
(1, 1, 13, 'red velvet', 600.00, 1, 600.00, '1783688051_0.jpg'),
(2, 1, 11, 'fruit cake', 400.00, 1, 400.00, '1783687699_main.webp'),
(3, 1, 10, 'fruit cake', 500.00, 1, 500.00, '1783625319_main.jpg'),
(4, 2, 14, 'red velvet', 600.00, 1, 600.00, '1783688789_0.jpg'),
(5, 3, 14, 'red velvet', 600.00, 2, 1200.00, '1783688789_0.jpg'),
(6, 3, 10, 'fruit cake', 500.00, 2, 1000.00, '1783625319_main.jpg'),
(7, 3, 11, 'fruit cake', 400.00, 1, 400.00, '1783687699_main.webp'),
(8, 4, 11, 'fruit cake', 400.00, 1, 400.00, '1783687699_main.webp'),
(9, 4, 13, 'red velvet', 600.00, 1, 600.00, '1783688051_0.jpg'),
(10, 5, 18, 'Divine Almond Chocolate Truffle Cake', 1299.00, 1, 1299.00, '1784296676_0.webp'),
(11, 5, 27, 'Classic Mixed Fruit Cake', 999.00, 1, 999.00, '1784298145.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `mobileno` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `address`, `city`, `pincode`, `state`, `country`, `username`, `email`, `dob`, `password`, `gender`, `mobileno`, `created_at`, `phone`) VALUES
(10, 'user', 'junagadh', 'junagadh', '362015', 'gujrat', 'indian', 'user', 'user123@gmail.com', '2024-12-04', 'user123', 'Female', '9854765201', '2026-07-10 12:50:58', NULL),
(13, 'demo', 'junagadh', 'junagadh', '362015', 'gujrat', 'India', 'demo', 'demo12@gmail.com', '2025-12-31', '$2y$10$bxb4rQnrSFyHzWIfMFwftOSaH5Mjd3zqfYKW.dhGnXVLYx03VU8U.', 'Female', '09854765201', '2026-07-10 14:20:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `cakes`
--
ALTER TABLE `cakes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cake_images`
--
ALTER TABLE `cake_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cakes`
--
ALTER TABLE `cakes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `cake_images`
--
ALTER TABLE `cake_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `cakes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `cakes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
