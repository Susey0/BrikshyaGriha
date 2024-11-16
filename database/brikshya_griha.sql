-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2024 at 07:12 AM
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
-- Database: `brikshya_griha`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(161, 2, 1, 1, '2024-11-15 01:54:07', '2024-11-15 01:54:07');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'Indoor Plants', '2024-09-20 05:16:06'),
(2, 'Outdoor Plants', '2024-09-20 05:17:07'),
(3, 'Accessories', '2024-09-20 05:17:25'),
(4, 'Fertilizers', '2024-09-20 05:17:33'),
(5, 'Flowers', '2024-09-20 06:26:39'),
(6, 'Fruits', '2024-09-20 06:26:52'),
(7, 'Pots', '2024-09-20 06:51:19');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_reply` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`, `admin_reply`) VALUES
(3, 'Aarya Hazel Sapkota', 'sapkotasushma05@gmail.com', 'For location', 'I want to visit your shop.Can you provide me detailes location?', '2024-10-27 00:19:30', 'You can visit us at Tinthana,Chandragiri-5,Kathmandu');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `full_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `total_amount`, `payment_method`, `status`, `full_name`, `address`, `phone`, `email`) VALUES
(1, 1, '2024-11-13 22:34:44', 400.00, 'cod', 'Pending', 'Aarya Hazel Sapkota', 'Tinthana', '9851005572', 'aarya@gmail.com'),
(2, 1, '2024-11-13 22:35:35', 75.00, 'cod', 'Pending', 'Aarya Hazel Sapkota', 'Tinthana', '9851005572', 'aarya@gmail.com'),
(3, 1, '2024-11-13 22:36:22', 1400.00, 'cod', 'Pending', 'Aarya Hazel Sapkota', 'Tinthana', '9851005572', 'aarya@gmail.com'),
(4, 3, '2024-11-13 22:54:08', 650.00, 'cod', 'Pending', 'Sirjana Sapkota', 'Tinthana', '9811111111', 'sirjana@gmail.com'),
(5, 3, '2024-11-14 04:20:38', 50.00, 'cod', 'Pending', 'Sirjana Sapkota', 'Tinthana', '9811111111', 'sirjana@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 30, 2, 200.00),
(2, 2, 21, 3, 25.00),
(3, 3, 1, 2, 700.00),
(4, 4, 18, 1, 650.00),
(5, 5, 21, 2, 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `category_id`, `image`, `created_at`, `updated_at`, `stock`) VALUES
(1, 'Monstera Broken Heart Indoor Plants', 700.00, 'Monstera Broken Heart Plant, also known as the \'Swiss Cheese Plant\' is a tropical plant from the Araceae family. It is popular houseplants, this easy-growing plant with its heart-shaped leaves that have holes in the leaf blade is a crowd favorite.', 1, 'Screenshot 2024-09-20 112640.png', '2024-09-20 05:52:59', '2024-11-13 16:52:41', 10),
(2, 'Bamboo Palm Indoor Plant', 630.00, 'Bamboo palms prefer indirect, filtered light or shade, and should not be placed in direct sunlight. Sudden changes in lighting can shock the plant and cause damage. Water - Bamboo palms like well-drained soil that\'s consistently moist, but doesn\'t tolerate standing water. Water one to three times a week.', 1, 'Bamboo Palm Indoor Plant.png', '2024-09-20 06:08:09', '2024-11-13 16:52:50', 10),
(4, 'Money Plant Indoor Plant', 550.00, 'The Money Plant, also known as Pothos (Epipremnum aureum) or Devil\'s Ivy, is a popular indoor plant celebrated for its ease of care and attractive appearance. Its reputation for bringing good luck and financial success adds to its charm, making it a common fixture in homes and offices.', 1, 'Money Plant.png', '2024-09-20 06:16:38', '2024-11-13 16:52:58', 10),
(5, 'Philodendron Birkin Indoor Plant', 750.00, 'The Philodendron Birkin thrives in bright, indirect light. That means a few feet away from a bright window, filtered by thin curtains or blinds. Do not expose to direct sun light or the leaves may \"burn\". Water your Philodendron Birkin houseplant once a week.', 1, 'Philodendron Birkin Indoor Plant.png', '2024-09-20 06:18:27', '2024-11-09 07:44:03', 10),
(6, 'Snake Plant Indoor Plant', 750.00, 'The snake plant comes from Africa, where it thrives in extremely dry conditions. It can therefore survive a long period of neglect and needs little watering. It could be described as the perfect house plant – it\'s low maintenance and isn\'t fussy about its location.', 1, 'Snake Plant.png', '2024-09-20 06:20:40', '2024-10-26 04:35:01', 10),
(9, 'Muntala', 1200.00, 'Kumquats are slow-growing evergreen shrubs, short trees with dense branches, and little thorns. The flowers are white, similar to other citrus flowers, and can be borne singly or in clusters within the leaf axils.', 6, 'Muntala.png', '2024-10-20 13:47:01', '2024-11-09 07:44:10', 10),
(10, 'Kiwi', 350.00, 'Kiwifruit or Chinese gooseberry, is the edible berry of several species of woody vines in the genus Actinidia.', 6, 'Kiwi.png', '2024-10-20 13:50:21', '2024-11-13 16:53:07', 10),
(11, 'Junar', 1200.00, 'The Junar, also known as the sweet orange, is a citrus fruit and evergreen tree that is grown in Nepal', 6, 'Junar.png', '2024-10-20 13:53:45', '2024-11-13 16:53:17', 10),
(12, 'Pear (Naspati)', 450.00, 'Nashpati or Pear plant is a fruit plant in the family Rosaceae. The fruit is of green colour initially and yellowish coloured when fully ripe. Its flesh is juicy, sweet and sour.This is grafted plant.', 6, 'Naspati.png', '2024-10-20 13:57:40', '2024-11-13 16:53:55', 10),
(13, 'Orange (Suntala)', 450.00, 'Orange tree is a citrus evergreen tree with a productive lifespan of 50-60 years. Some well-cared orange trees can live up to 100 years or more. Orange plant (Citrus x sinensis) belongs to the Rutaceae family. It is a flowering tree and its height in maturity can range between 16 and 50 ft. (between 5 and 15 m). ', 6, 'orange.png', '2024-10-20 14:02:48', '2024-10-22 03:15:35', 10),
(14, 'Cocopeat 650 gram(Block)', 150.00, 'Cocopeat is a compressed form of coir pith, which is a natural by-product of coconut husks. It\'s primarily used as a growing medium for plants due to its excellent water retention, aeration, and nutrient-holding properties. ', 4, 'Cocopeat.png', '2024-10-20 14:07:03', '2024-11-13 16:54:03', 10),
(15, 'Pina', 90.00, 'Pina is an organic by-product of mustard oil extraction, rich in essential nutrients like nitrogen, phosphorus, and potassium. It enhances soil fertility, promotes plant growth, and also acts as a natural pesticide, protecting crops from pests.', 4, 'Pina.png', '2024-10-20 14:11:20', '2024-11-09 07:00:29', 10),
(16, 'Neem Oil (100 ml)', 260.00, 'Neem oil is a natural pesticide derived from the seeds of the neem tree (Azadirachta indica). It is widely used for plant care due to its ability to control pests like aphids, mites, and whiteflies without harming beneficial insects. Neem oil also acts as a fungicide, preventing and treating fungal infections like powdery mildew, while being safe and eco-friendly for organic gardening.', 4, 'Neem Oil.png', '2024-10-20 14:15:01', '2024-10-22 03:16:14', 10),
(17, 'Saaf', 100.00, 'Saaf is a fungicide used to protect plants from fungal diseases. It contains Carbendazim and Mancozeb, which effectively control a wide range of fungal infections like powdery mildew, rust, and leaf spot. Saaf is commonly used in agriculture to ensure healthy plant growth and prevent crop losses due to fungal infestations.', 4, 'Saaf.png', '2024-10-20 14:21:44', '2024-11-12 12:49:02', 10),
(18, 'Buddha Pot', 650.00, 'Useful for indoor plants.', 7, 'Buddha Pot.png', '2024-10-20 14:59:56', '2024-11-13 17:09:08', 9),
(19, 'Gamala With Plate', 100.00, 'useful for planting flowers', 7, 'Gamala with Plate.png', '2024-10-20 15:09:30', '2024-11-13 16:54:21', 10),
(20, 'Bagmati Orange Rectangle Flower Pot / Gamala With Plate - Small', 280.00, 'Reactangular pot for planting', 7, 'Rectangular pot.png', '2024-10-20 15:11:30', '2024-11-13 16:54:42', 10),
(21, 'Mini Flower Pot', 25.00, 'Useful for small plants', 7, 'Mini Flower Pot.png', '2024-10-20 15:16:49', '2024-11-13 22:35:38', 8),
(23, 'London Dhupi', 1600.00, 'London Dhupi', 2, 'London Dhupi.png', '2024-11-06 01:28:15', '2024-11-06 01:28:15', 10),
(24, 'Rexona Palm', 1800.00, 'Rexona Palm', 2, 'Rexona Palm.png', '2024-11-06 01:31:18', '2024-11-06 01:31:18', 10),
(25, 'Rosemary', 250.00, 'Rosemary is a fragrant, evergreen shrub with needle-like leaves and two-lipped, purplish-blue and white flowers. New growth is soft and flexible but older stems become woody and form trunks with time.', 2, 'Rosemary.png', '2024-11-06 01:36:33', '2024-11-06 01:36:33', 10),
(26, 'Basil', 200.00, 'Basil, also called great basil, is a culinary herb of the family Lamiaceae. It is a tender plant, and is used in cuisines worldwide. In Western cuisine, the generic term \"basil\" refers to the variety also known as Genovese basil or sweet basil. Basil is native to tropical regions from Central Africa to Southeast Asia.', 2, 'Basil.png', '2024-11-06 01:40:12', '2024-11-13 16:54:51', 10),
(27, 'Hibiscus Single', 500.00, 'Known for their large and colorful flowers.They are a great addition to any home or a garden.With brightening up the spaces these blossoms have great medicinal properties,as their flowers and leaves can be made into teas and liquid extracts that can help treta variety of conditions from weight loss to even cancer.  ', 5, 'Hibiscus Single.png', '2024-11-06 01:47:51', '2024-11-13 16:54:29', 10),
(28, 'Rose', 250.00, 'A rose is either a woody perennial flowering plant of the genus Rosa, in the family Rosaceae, or the flower it bears. There are over three hundred species and tens of thousands of cultivars.', 5, 'Rose.png', '2024-11-06 01:52:52', '2024-11-13 16:53:27', 10),
(29, 'Dahlia', 50.00, 'Dahlia is a genus of bushy, tuberous, herbaceous perennial plants native to Mexico and Central America. As a member of the Asteraceae family of dicotyledonous plants, its relatives include the sunflower, daisy, chrysanthemum, and zinnia. ', 5, 'Dahlia.png', '2024-11-06 01:55:12', '2024-11-13 16:53:36', 10),
(30, 'Bougainvillea', 200.00, 'Bougainvillea is a stunning flower that is a favorite to many who have fallen in love with its cheery appearance and brightly colored petals. The actual flower is white but each cluster of 3 flowers is surrounded by 3-6 bracts of bright colors.', 5, 'Bougainvillea.png', '2024-11-06 01:59:01', '2024-11-13 16:49:44', 3),
(31, 'Kuto', 500.00, 'Comfortable for garden and kausi kheti. •length 11.5 inch and width 7 inch. •Product is exactly as shown in the picture.and high quality.', 3, 'Kuto.png', '2024-11-06 02:04:45', '2024-11-06 02:04:45', 10),
(32, 'Garden Scissor ', 900.00, 'Useful for Garden and Kausi Kheti', 3, 'Garden Scissors.png', '2024-11-06 02:07:27', '2024-11-09 05:26:08', 10),
(33, 'Water Can', 500.00, 'Material: Plastic, Color: Dark Greek\r\nPackage Contents: Plastic 5 Liter Watering Can\r\nPremium quality and easy to use\r\nCapacity: 5 ltr color: green material: high grade plastic\r\nA low pressure and uniform watering tool\r\nSuitable to suitable for small and mid sized gardens', 3, 'Water Can.png', '2024-11-06 02:11:56', '2024-11-06 02:11:56', 10);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `comment`, `rating`, `created_at`) VALUES
(1, 2, 4, 'Very beautiful plant!!!', 4, '2024-10-22 03:49:36'),
(2, 3, 18, 'I loved it!!', 5, '2024-10-22 06:48:46'),
(3, 2, 18, 'Nice!!', 5, '2024-10-22 06:50:57'),
(4, 3, 14, 'very good product for plants!!!', 4, '2024-10-22 06:59:42'),
(5, 2, 21, 'I loved it!!', 5, '2024-11-05 14:09:20'),
(6, 2, 2, 'I love it!!!', 4, '2024-11-12 12:53:03');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `order_id`, `user_id`, `product_id`, `quantity`, `price`, `total_amount`, `created_at`) VALUES
(1, 1, 1, 30, 2, 200.00, 400.00, '2024-11-13 16:49:46'),
(2, 2, 1, 21, 3, 25.00, 75.00, '2024-11-13 16:50:36'),
(3, 3, 1, 1, 2, 700.00, 1400.00, '2024-11-13 16:51:23'),
(4, 4, 3, 18, 1, 650.00, 650.00, '2024-11-13 17:09:09'),
(5, 5, 3, 21, 2, 25.00, 50.00, '2024-11-13 22:35:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `phone`, `address`, `role`, `created_at`, `updated_at`, `reset_token`) VALUES
(1, '', '', '', 'admin@gmail.com', '$2y$10$CbtaV7bPfcXDsQEigrQ.luaBgWkSevi5a6PYhSXA9rQidGpkVAaXy', '', '', 'admin', '2024-10-22 03:45:05', '2024-10-22 03:45:05', NULL),
(2, 'Aarya', 'Sapkota', 'aarya01', 'aarya@gmail.com', '$2y$10$fM3.ZiltegaEH907Zm/98.nAc33Vj8NQ7tYIl7bZMUdcJADTFcdNe', '9851005572', 'Tinthana', 'user', '2024-10-22 03:47:02', '2024-10-22 03:47:02', NULL),
(3, 'Sirjana', 'Sapkota', 'Sirju', 'sirjana@gmail.com', '$2y$10$nmGSYvPBYjEx84gjEqugGO/dGjmNsBlCM4cYvi7e8tCeK82IF/2cO', '9811111111', 'Tinthana', 'user', '2024-10-22 06:48:11', '2024-10-22 06:48:11', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
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
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

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
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
