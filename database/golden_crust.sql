-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2026 at 03:03 PM
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
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '123456'),
(6, 'demo', '987654'),
(7, 'bansi', '185207');

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
(25, 'Premium Fresh Fruit Fantasy Cake', 'Fruit', 1499.00, '1784298080.jpg', 'Delicious vanilla cream cake decorated with kiwi, apples, oranges, strawberries, cherries, and handcrafted chocolate pieces. Fresh, colorful, and full of fruity flavor.', 10, '2026-07-17 14:21:20'),
(26, 'Chocolate Berry Fruit Cake', 'Fruit', 1699.00, '1784298111.jpg', 'Moist chocolate cake covered with silky chocolate ganache and topped with fresh strawberries, blueberries, raspberries, kiwi, and premium chocolate drizzle.', 9, '2026-07-17 14:21:51'),
(27, 'Classic Mixed Fruit Cake', 'Fruit', 999.00, '1784298145.jpg', 'Soft vanilla sponge with whipped cream frosting, topped with apples, kiwi, grapes, oranges, cherries, chocolate sticks, and colorful sprinkles. A timeless bakery favorite.', 10, '2026-07-17 14:22:25'),
(28, 'Fresh Berry Chocolate Drip Cake', 'Fruit', 1599.00, '1784298174.jpg', 'Smooth vanilla cream cake finished with chocolate drip and loaded with fresh strawberries, blueberries, raspberries, kiwi, and cherries. Perfect for birthdays and family celebrations.', 11, '2026-07-17 14:22:54'),
(29, 'Classic Berry Glazed Bundt Cake', 'Bundt', 1000.00, '1784459586.jpg', 'Soft vanilla bundt cake topped with smooth white glaze and decorated with fresh grapes, cranberries, and mint leaves. Perfect for tea-time and family celebrations.', 15, '2026-07-19 11:13:06'),
(30, 'Red Velvet Mini Bundt Cake', 'Bundt', 400.00, '1784459619.jpg', 'Moist red velvet mini bundt cake finished with creamy vanilla glaze and red velvet crumbs. A delightful individual dessert for every occasion.', 30, '2026-07-19 11:13:39'),
(31, 'Chocolate Blackberry Bundt Cake', 'Bundt', 1399.00, '1784459652.jpg', 'Rich chocolate bundt cake decorated with fresh blackberries, edible flowers, and a soft chocolate finish. Perfect for premium dessert lovers.', 10, '2026-07-19 11:14:12'),
(32, 'Celebration Sprinkle Bundt Cake', 'Bundt', 1199.00, '1784459684.jpg', 'Classic vanilla bundt cake topped with whipped cream frosting and colorful rainbow sprinkles. A fun and festive cake for birthdays and parties.', 12, '2026-07-19 11:14:44'),
(33, 'Pecan Vanilla Bundt Cake', 'Bundt', 1299.00, '1784459712.jpg', 'Soft buttery vanilla bundt cake glazed with sweet icing and topped with crunchy roasted pecans. Best served with tea or coffee.', 10, '2026-07-19 11:15:12'),
(34, 'Chocolate Sprinkle Bundt Cake', 'Bundt', 1200.00, '1784459756.jpg', 'Moist chocolate bundt cake coated with creamy vanilla glaze and colorful sprinkles. A delicious combination of rich chocolate flavor and sweet frosting.', 10, '2026-07-19 11:15:56'),
(35, 'Classic Red Velvet Cake', 'Velvet', 899.00, '1784459791.webp', 'Soft and moist red velvet sponge layered with smooth cream cheese frosting and finished with elegant white whipped cream swirls and red velvet crumbs. A timeless bakery favorite.', 20, '2026-07-19 11:16:31'),
(36, 'Premium Red Velvet Delight Cake', 'Velvet', 1099.00, '1784459846.jpg', 'Rich red velvet cake covered with velvety crumbs, fresh whipped cream, and premium chocolate decorations. Perfect for birthdays and celebrations.', 15, '2026-07-19 11:17:26'),
(37, 'Red Velvet Layer Cake', 'Velvet', 1199.00, '1784459880.jpg', 'Delicious square-shaped red velvet cake with multiple layers of soft sponge and creamy cream cheese frosting. Finished with red velvet crumbs for a rich taste.', 12, '2026-07-19 11:18:00'),
(38, 'Rose Velvet Celebration Cake', 'Velvet', 1399.00, '1784459964.webp', 'Elegant velvet cake decorated with handcrafted red buttercream roses, white flowers, and smooth cream frosting. Ideal for anniversaries and romantic occasions.', 10, '2026-07-19 11:19:24'),
(39, 'Heartfelt Red Velvet Cake', 'Velvet', 1499.00, '1784459996.jpg', 'Premium heart-themed red velvet cake topped with white chocolate hearts, cream cheese frosting, and rich red velvet flakes. Perfect for Valentines Day and anniversaries.', 8, '2026-07-19 11:19:56'),
(40, 'Royal White Red Velvet Cake', 'Velvet', 999.00, '1784460029.jpg', 'Soft red velvet sponge layered with silky whipped cream frosting and topped with white chocolate roses and fine red velvet crumbs. A perfect choice for any celebration.', 15, '2026-07-19 11:20:29'),
(41, 'Berry Bliss Ice Cream Cake', 'Ice Cream', 1799.00, '1784460092.jpg', 'Creamy strawberry and vanilla ice cream cake topped with ice cream scoops, waffle cones, fresh berries, colorful sprinkles, and delicious purple drip glaze. Perfect for birthdays and summer celebrations.', 8, '2026-07-19 11:21:32'),
(42, 'Mint Scoop Ice Cream Cake', 'Ice Cream', 1550.00, '1784460133.jpg', 'Refreshing mint-flavored ice cream cake decorated with crunchy waffle cones, creamy frosting, and a premium ice cream scoop. A cool and delightful dessert for every occasion.', 10, '2026-07-19 11:22:13'),
(43, 'Rainbow Party Ice Cream Cake', 'Ice Cream', 1999.00, '1784460177.jpg', 'Colorful multi-flavored ice cream cake featuring chocolate, strawberry, and vanilla scoops, waffle cones, chocolate drip, rainbow sprinkles, and festive candy toppings.', 6, '2026-07-19 11:22:57'),
(45, 'Strawberry Ice Cream Cake', 'Ice Cream', 1400.00, '1784556114.jpg', 'Fresh strawberry ice cream cake decorated with strawberry glaze, whipped cream, and fresh strawberries.', 10, '2026-07-20 14:01:54'),
(46, 'Classic Vanilla Ice Cream Cake', 'Ice Cream', 1200.00, '1784556143.jpg', 'Creamy vanilla ice cream cake layered with soft sponge and topped with whipped cream and chocolate drizzle.', 10, '2026-07-20 14:02:23'),
(47, 'Chocolate Ice Cream Cake', 'Ice Cream', 1559.00, '1784556183.jpg', 'Rich chocolate ice cream cake with chocolate sponge, chocolate ganache, and premium chocolate toppings.', 5, '2026-07-20 14:03:03'),
(48, 'Oreo Ice Cream Cake', 'Ice Cream', 1750.00, '1784556219.jpg', 'Creamy vanilla ice cream cake loaded with Oreo cookies, chocolate drizzle, and cookie crumbs.', 8, '2026-07-20 14:03:39'),
(51, 'Strawberry Bliss Cupcake', 'Cupcake', 249.00, '1784630666.jpg', 'Soft vanilla cupcake topped with fresh strawberry buttercream, pink sprinkles, and a sweet candy garnish. A perfect treat for birthdays and celebrations.', 30, '2026-07-21 10:44:26'),
(52, 'Cherry Vanilla Cupcake', 'Cupcake', 279.00, '1784630692.jpg', 'Fluffy vanilla cupcake finished with creamy cherry frosting, colorful sprinkles, and a juicy maraschino cherry. Sweet, light, and delicious.', 25, '2026-07-21 10:44:52'),
(53, 'Blue Dream Cupcake', 'Cupcake', 299.00, '1784630723.jpg', 'Moist vanilla cupcake topped with silky blue vanilla frosting, edible stars, and a cute fondant decoration. Perfect for kids and themed parties.', 20, '2026-07-21 10:45:23'),
(54, 'Chocolate Heart Delight Cupcake', 'Cupcake', 359.00, '1784630769.jpg', 'Rich chocolate cupcake topped with silky pink buttercream, red heart sprinkles, and layered fondant heart decorations. A perfect Valentines Day or anniversary treat.', 16, '2026-07-21 10:46:09'),
(55, 'Lavender Pearl Swirl Cupcake', 'Cupcake', 350.00, '1784630800.jpg', 'Soft vanilla cupcake finished with elegant lavender buttercream swirls and shimmering edible pearl sprinkles. A premium dessert for weddings and celebrations.', 19, '2026-07-21 10:46:40'),
(56, 'Marshmallow Garden Cupcake', 'Cupcake', 300.00, '1784630829.jpg', 'Moist vanilla cupcake topped with creamy yellow buttercream, fluffy marshmallows, pastel flowers, heart decorations, and colorful candy sprinkles. A cheerful dessert for birthdays and gifting.', 21, '2026-07-21 10:47:09'),
(57, 'Strawberry Ice Cream Swiss Roll', 'Roll', 900.00, '1784630936.jpg', 'Soft vanilla sponge rolled with fresh strawberry cream and strawberry jam, topped with two strawberry ice cream scoops, whipped cream, fresh strawberries, red pearls, and mint leaves. A premium dessert for birthdays and celebrations.', 12, '2026-07-21 10:48:56'),
(58, 'Banana Caramel Swiss Roll', 'Roll', 1200.00, '1784630965.jpg', 'Light vanilla sponge filled with smooth vanilla cream and fresh banana slices, finished with rich caramel drizzle, whipped cream, crunchy roasted nuts, and fresh banana topping. Perfect for tea-time and family gatherings.', 18, '2026-07-21 10:49:25'),
(59, 'Strawberry Bunny Swiss Roll', 'Roll', 400.00, '1784631012.jpg', 'Moist red velvet sponge filled with fluffy whipped cream and whole fresh strawberries, decorated with whipped cream, strawberries, and a cute bunny topper. A delightful dessert for kids and special occasions.', 20, '2026-07-21 10:50:12'),
(60, 'Pink Strawberry Spiral Roll', 'Roll', 1400.00, '1784631059.jpg', 'Fluffy strawberry sponge rolled with creamy vanilla filling, coated in pink strawberry frosting, and garnished with fresh strawberries and crunchy strawberry crisps. A refreshing fruit-inspired dessert.', 14, '2026-07-21 10:50:59'),
(61, 'Chocolate Black Forest Swiss Roll', 'Roll', 1500.00, '1784631090.jpg', 'Rich chocolate sponge rolled with vanilla whipped cream, topped with chocolate shavings, dark chocolate chunks, chocolate drizzle, and fresh cherries. An indulgent treat for chocolate lovers.', 10, '2026-07-21 10:51:30'),
(62, 'Rainbow Sprinkle Vanilla Swiss Roll', 'Roll', 1250.00, '1784631124.jpg', 'Soft vanilla sponge filled with rainbow-colored vanilla cream, coated in smooth white frosting, and covered with colorful rainbow sprinkles. A cheerful cake perfect for birthdays and festive celebrations.', 16, '2026-07-21 10:52:04'),
(63, 'Classic Red Velvet Pastry', 'Pastry', 250.00, '1784631163.jpg', 'Soft and moist red velvet sponge layered with rich cream cheese frosting, topped with fresh strawberries and red velvet crumbs. A classic individual dessert with a smooth, creamy finish.', 40, '2026-07-21 10:52:43'),
(64, 'Cherry Berry Mirror Pastry', 'Pastry', 340.00, '1784631198.jpg', 'Premium berry mousse pastry coated with a glossy cherry mirror glaze, decorated with fresh cherries, red currants, whipped cream, and edible flowers. Rich, fruity, and elegant.', 25, '2026-07-21 10:53:18'),
(65, 'Blueberry Velvet Pastry', 'Pastry', 299.00, '1784631224.jpg', 'Fluffy blueberry sponge layered with silky blueberry cream, topped with fresh blueberries, grapes, and mint leaves. A refreshing berry-flavored pastry for every occasion.', 30, '2026-07-21 10:53:44'),
(66, 'Raspberry Vanilla Pastry', 'Pastry', 270.00, '1784631252.jpg', 'Soft vanilla sponge layered with raspberry cream, finished with a smooth pink glaze, fresh raspberries, wafer sticks, cookies, and edible flowers. Sweet and delicately balanced.', 35, '2026-07-21 10:54:12'),
(67, 'Sweetheart Berry Pastry', 'Pastry', 325.00, '1784631281.jpg', 'Heart-shaped vanilla and berry pastry layered with pink cream and purple sponge, decorated with heart sprinkles and whipped cream. Ideal for Valentines Day and romantic celebrations.', 22, '2026-07-21 10:54:41'),
(68, 'Premium Red Velvet Strawberry Pastry', 'Pastry', 400.00, '1784631321.jpg', 'Rich red velvet pastry layered with creamy frosting, topped with white chocolate drip, fresh strawberries, and chocolate pearls. A premium handcrafted dessert for special moments.', 10, '2026-07-21 10:55:21'),
(76, 'Princess Floral Birthday Cake', 'Celebration', 800.00, '1785516559.jpeg', 'Elegant pastel pink birthday cake decorated with handcrafted flowers, butterflies, moon topper and girl figurine. Perfect for girls birthday celebrations.', 20, '2026-07-31 16:49:19'),
(77, 'Luxury Engagement Cake', 'Celebration', 1500.00, '1785516595.jpeg', 'Premium two-tier engagement cake with white roses, edible gold detailing and elegant floral decorations.', 30, '2026-07-31 16:49:55'),
(78, 'Welcome Baby Theme Cake', 'Celebration', 2000.00, '1785516624.jpeg', 'Cute baby shower cake featuring sleeping baby topper, teddy bear, clouds, balloons and moon decoration.', 10, '2026-07-31 16:50:24'),
(79, 'Pink Floral Designer Cake', 'Celebration', 1500.00, '1785516661.jpeg', 'Premium designer cake with pink roses, pearl decorations and luxurious gold finish for special occasions.', 10, '2026-07-31 16:51:01'),
(80, 'Luxury Anniversary Cake', 'Celebration', 3000.00, '1785516698.jpeg', 'Romantic anniversary cake decorated with roses, gold drip and elegant floral arrangements.', 5, '2026-07-31 16:51:38'),
(81, 'Graduation Celebration Cake', 'Celebration', 2200.00, '1784738030.jpeg', 'Stylish graduation cake featuring graduation cap, diploma, books and beautiful flower decorations.', 12, '2026-07-31 16:52:14');

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
(55, 14, 79, 'Pink Floral Designer Cake', 1500.00, 1, 1500.00, '1785516661.jpeg', 'bansi', '2026-09-16 12:52:12'),
(56, 14, 64, 'Cherry Berry Mirror Pastry', 340.00, 4, 1360.00, '1784631198.jpg', 'bansi', '2026-09-16 12:52:30');

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
  `payment_status` varchar(20) DEFAULT 'pending',
  `order_notes` text DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `username`, `total_amount`, `address`, `phone`, `payment_method`, `payment_status`, `order_notes`, `order_date`, `status`) VALUES
(7, 10, 'user', 4748.00, 'junagadh', '125478658', 'COD', 'pending', '', '2026-07-19 15:23:49', 'pending'),
(9, 14, 'bansi', 4500.00, 'junagadh', '9854765201', 'COD', 'pending', '', '2026-08-24 10:55:43', 'Cancelled'),
(10, 14, 'bansi', 2200.00, 'junagadh', '9854765201', 'UPI', 'Paid', '', '2026-08-24 11:12:08', 'Cancelled'),
(11, 14, 'bansi', 2200.00, 'junagadh', '9854765201', 'COD', 'Pending', '', '2026-08-24 11:13:11', 'Cancelled'),
(15, 14, 'bansi', 900.00, 'junagadh', '9854765201', 'Online', 'Paid', '', '2026-08-24 11:39:34', 'Paid'),
(16, 14, 'bansi', 3109.00, 'junagadh', '1547859874', 'COD', 'Pending', '', '2026-08-24 11:49:17', 'Pending'),
(17, 14, 'bansi', 1300.00, 'junagadh', '1245764127', 'COD', 'Pending', '', '2026-08-24 12:08:24', 'Pending'),
(18, 14, 'bansi', 400.00, 'junagadh', '2154785694', 'COD', 'Pending', '', '2026-08-24 12:10:36', 'Pending'),
(19, 14, 'bansi', 3000.00, 'junagadh', '9854752165', 'COD', 'Pending', '', '2026-09-07 17:33:53', 'Pending'),
(20, 14, 'bansi', 800.00, 'junagadh', '9854765201', 'COD', 'pending', '', '2026-09-07 17:36:21', 'Pending'),
(24, 10, 'user', 400.00, 'junagadh', '2541658745', 'COD', 'Pending', '', '2026-09-09 13:39:48', 'Pending'),
(26, 14, 'bansi', 299.00, 'junagadh', '2154687459', 'COD', 'Pending', '', '2026-09-09 14:08:11', 'Pending'),
(27, 10, 'user', 1799.00, 'junagadh', '5478546852', 'COD', 'Pending', '', '2026-09-09 14:16:02', 'Pending'),
(28, 10, 'user', 400.00, 'junagadh', '5412658745', 'COD', 'Pending', '', '2026-09-25 14:59:49', 'Pending');

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
(13, 7, 42, 'Mint Scoop Ice Cream Cake', 1550.00, 1, 1550.00, '1784460133.jpg'),
(14, 7, 41, 'Berry Bliss Ice Cream Cake', 1799.00, 1, 1799.00, '1784460092.jpg'),
(15, 7, 38, 'Rose Velvet Celebration Cake', 1399.00, 1, 1399.00, '1784459964.webp'),
(17, 9, 79, 'Pink Floral Designer Cake', 1500.00, 1, 1500.00, '1785516661.jpeg'),
(18, 9, 80, 'Luxury Anniversary Cake', 3000.00, 1, 3000.00, '1785516698.jpeg'),
(19, 10, 81, 'Graduation Celebration Cake', 2200.00, 1, 2200.00, '1784738030.jpeg'),
(20, 11, 81, 'Graduation Celebration Cake', 2200.00, 1, 2200.00, '1784738030.jpeg'),
(27, 15, 57, 'Strawberry Ice Cream Swiss Roll', 900.00, 1, 900.00, '1784630936.jpg'),
(28, 16, 42, 'Mint Scoop Ice Cream Cake', 1550.00, 1, 1550.00, '1784460133.jpg'),
(29, 16, 47, 'Chocolate Ice Cream Cake', 1559.00, 1, 1559.00, '1784556183.jpg'),
(30, 17, 63, 'Classic Red Velvet Pastry', 250.00, 1, 250.00, '1784631163.jpg'),
(31, 17, 55, 'Lavender Pearl Swirl Cupcake', 350.00, 3, 1050.00, '1784630800.jpg'),
(32, 18, 68, 'Premium Red Velvet Strawberry Pastry', 400.00, 1, 400.00, '1784631321.jpg'),
(33, 19, 80, 'Luxury Anniversary Cake', 3000.00, 1, 3000.00, '1785516698.jpeg'),
(34, 20, 76, 'Princess Floral Birthday Cake', 800.00, 1, 800.00, '1785516559.jpeg'),
(39, 24, 68, 'Premium Red Velvet Strawberry Pastry', 400.00, 1, 400.00, '1784631321.jpg'),
(41, 26, 53, 'Blue Dream Cupcake', 299.00, 1, 299.00, '1784630723.jpg'),
(42, 27, 41, 'Berry Bliss Ice Cream Cake', 1799.00, 1, 1799.00, '1784460092.jpg'),
(43, 28, 68, 'Premium Red Velvet Strawberry Pastry', 400.00, 1, 400.00, '1784631321.jpg');

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
(14, 'bansi', 'junagadh', 'junagadh', '362015', 'gujrat', 'India', 'bansi', 'bansi123@gmail.com', '2007-05-18', '185207', 'Female', '5874598652', '2026-07-19 14:12:56', NULL);

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
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `added_date`) VALUES
(6, 10, 41, '2026-07-19 18:15:10'),
(7, 10, 29, '2026-07-19 18:33:22'),
(8, 14, 80, '2026-08-24 14:52:42'),
(9, 14, 60, '2026-08-24 14:52:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `cake_images`
--
ALTER TABLE `cake_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
