-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 24, 2026 at 05:46 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plantwatering`
--
CREATE DATABASE IF NOT EXISTS `plantwatering` DEFAULT CHARACTER SET utf8 COLLATE utf8_hungarian_ci;
USE `plantwatering`;

-- --------------------------------------------------------

--
-- Table structure for table `family`
--

DROP TABLE IF EXISTS `family`;
CREATE TABLE IF NOT EXISTS `family` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `family`
--

INSERT INTO `family` (`id`, `name`) VALUES
(3, 'Araceae'),
(5, 'Asparagaceae'),
(1, 'Brassicaceae'),
(7, 'Crassulaceae'),
(2, 'Ericaceae'),
(4, 'Lamiaceae'),
(12, 'Lythraceae'),
(6, 'Moraceae'),
(11, 'Passifloraceae'),
(8, 'Rosaceae'),
(10, 'Rutaceae'),
(9, 'Solanaceae');

-- --------------------------------------------------------

--
-- Table structure for table `my_plant`
--

DROP TABLE IF EXISTS `my_plant`;
CREATE TABLE IF NOT EXISTS `my_plant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(100) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `watering_interval_hours` int(11) DEFAULT NULL,
  `next_watering` datetime NOT NULL,
  `needs_attention` tinyint(1) NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `species_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_my_plant_user` (`user_id`),
  KEY `fk_my_plant_species` (`species_id`),
  KEY `idx_my_plant_next_watering` (`next_watering`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `my_plant`
--

INSERT INTO `my_plant` (`id`, `nickname`, `location`, `watering_interval_hours`, `next_watering`, `needs_attention`, `note`, `created_at`, `user_id`, `species_id`) VALUES
(1, 'Desert crucifer', 'South windowsill', NULL, '2026-07-28 05:44:03', 0, 'Grown from a seed packet, keep it dry over the summer.', '2026-07-24 04:36:39', 1, 1),
(2, 'Big monstera', 'Living room corner', 144, '2026-07-30 05:32:44', 0, 'New leaf unfurling, watch for spider mites.', '2026-07-24 04:36:39', 1, 3),
(3, 'Balcony lavender', 'Balcony', NULL, '2026-08-03 05:44:00', 0, NULL, '2026-07-24 04:36:39', 1, 4),
(4, 'Kitchen basil', 'Kitchen window', NULL, '2026-07-25 05:43:54', 0, 'Pinch the tips every week.', '2026-07-24 04:36:39', 1, 5),
(5, 'Hallway snake plant', 'Hallway', NULL, '2026-08-02 04:36:39', 0, NULL, '2026-07-24 04:36:39', 2, 6),
(6, 'Fiddle', 'Office', 120, '2026-07-22 04:36:39', 1, 'Dropped two lower leaves after the move.', '2026-07-24 04:36:39', 2, 7),
(7, 'Grandmother\'s jade', 'Bedroom shelf', NULL, '2026-07-29 04:36:39', 0, NULL, '2026-07-24 04:36:39', 3, 8),
(8, 'Mountain rhodo', 'Shaded garden bed', 48, '2026-07-25 04:36:39', 0, 'Mulch with pine bark in autumn.', '2026-07-24 04:36:39', 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `channel` varchar(10) NOT NULL,
  `message` varchar(255) NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `my_plant_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notification_plant` (`my_plant_id`),
  KEY `fk_notification_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `type`, `channel`, `message`, `sent_at`, `is_read`, `my_plant_id`, `user_id`) VALUES
(1, 'watering_due', 'email', 'Big monstera needs water.', '2026-07-24 04:11:39', 0, 2, 1),
(2, 'watering_due', 'push', 'Big monstera needs water.', '2026-07-24 04:11:39', 1, 2, 1),
(3, 'attention', 'email', 'Fiddle has been overdue for two days.', '2026-07-23 04:36:39', 0, 6, 2),
(4, 'watering_due', 'push', 'Hallway snake plant is due tomorrow.', NULL, 0, 5, 2),
(5, 'attention', 'push', 'Grandmother\'s jade has not been watered for over a week.', '2026-07-22 04:36:39', 1, 7, 3);

-- --------------------------------------------------------

--
-- Table structure for table `phase`
--

DROP TABLE IF EXISTS `phase`;
CREATE TABLE IF NOT EXISTS `phase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `label` varchar(50) NOT NULL,
  `icon` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phase`
--

INSERT INTO `phase` (`id`, `code`, `label`, `icon`) VALUES
(1, 'blooming', 'Blooming', '*'),
(2, 'pruning', 'Pruning', 'X'),
(3, 'fruiting', 'Fruiting', 'O');

-- --------------------------------------------------------

--
-- Table structure for table `species`
--

DROP TABLE IF EXISTS `species`;
CREATE TABLE IF NOT EXISTS `species` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `latin_name` varchar(150) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `habitat` varchar(120) DEFAULT NULL,
  `light_need` varchar(60) DEFAULT NULL,
  `watering_interval_hours` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `family_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `latin_name` (`latin_name`),
  KEY `fk_species_family` (`family_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `species`
--

INSERT INTO `species` (`id`, `name`, `latin_name`, `image_url`, `habitat`, `light_need`, `watering_interval_hours`, `description`, `family_id`) VALUES
(1, 'Long-styled eigia', 'Eigia longistyla', 'https://floraupload.s3.amazonaws.com/files2/457/45750.jpeg', 'Eastern Mediterranean desert and semi-desert, Palestine and Saudi Arabia', 'Full sun', 96, 'The only species of the monotypic genus Eigia, described by Soják in 1980 and named after the botanist Alexander Eig. A small arid-zone crucifer with a notably long style; it completes its cycle in the short winter rain season and needs no summer water.', 1),
(2, 'Taiwan rhododendron', 'Rhododendron formosanum', NULL, 'Mid-altitude forests, island wide in Taiwan', 'Partial shade', 72, 'Evergreen shrub with leathery leaves and pale pink to white flowers, the throat marked with red to brown spots. Wants acidic, freely draining soil.', 2),
(3, 'Swiss cheese plant', 'Monstera deliciosa', 'https://commons.wikimedia.org/wiki/Special:FilePath/Monstera_deliciosa_002.jpg?width=400', 'Indoor, native to Central American rainforests', 'Bright indirect', 168, 'Climbing aroid with fenestrated leaves. Prefers an airy, free draining mix; wipe the leaves to keep them dust free.', 3),
(4, 'English lavender', 'Lavandula angustifolia', 'https://commons.wikimedia.org/wiki/Special:FilePath/Lavandula_angustifolia_003.JPG?width=400', 'Dry and sunny Mediterranean slopes', 'Full sun', 240, 'Cut back after flowering to keep it compact. It does not tolerate overwatering or heavy soil.', 4),
(5, 'Sweet basil', 'Ocimum basilicum', 'https://commons.wikimedia.org/wiki/Special:FilePath/Ocimum_basilicum_001.JPG?width=400', 'Warm kitchen garden or windowsill', 'Full sun', 24, 'Tender annual herb. Pinch the growing tips often and remove flower spikes to keep the leaves productive.', 4),
(6, 'Snake plant', 'Dracaena trifasciata', 'https://commons.wikimedia.org/wiki/Special:FilePath/Sansevieria_trifasciata_Prain_%2849388534642%29.jpg?width=400', 'Indoor, native to West Africa', 'Low to bright indirect', 336, 'Very drought tolerant succulent-leaved plant. Far more plants are lost to overwatering than to neglect.', 5),
(7, 'Fiddle-leaf fig', 'Ficus lyrata', 'https://commons.wikimedia.org/wiki/Special:FilePath/Ficus_lyrata_DSCN4457.jpg?width=400', 'Indoor, native to West African lowland rainforest', 'Bright indirect', 168, 'Dislikes being moved and dislikes cold draughts. Water when the top few centimetres of the mix are dry.', 6),
(8, 'Jade plant', 'Crassula ovata', 'https://commons.wikimedia.org/wiki/Special:FilePath/Crassula_ovata_-_Jade_Plant_-_South_Africa_7.JPG?width=400', 'Indoor, native to South Africa', 'Full sun to bright indirect', 336, 'Succulent with thick glossy leaves. Water deeply but rarely, and keep it nearly dry in winter.', 7),
(9, 'Garden Strawberry', 'Fragaria x ananassa', 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Strawberries.jpg/800px-Strawberries.jpg', 'Outdoor garden beds, temperate zones', 'Full sun', 48, 'Low-growing perennial plant known for its sweet red fruit. Requires consistent moisture during fruiting.', 8),
(10, 'Red Raspberry', 'Rubus idaeus', 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Raspberries_02.jpg/800px-Raspberries_02.jpg', 'Temperate thickets and woodland margins', 'Full sun to partial shade', 72, 'Deciduous fruiting cane producing sweet aggregate berries. Prune spent canes back annually.', 8),
(11, 'Sweet Cherry', 'Prunus avium', 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Cherry_tree_Orgiva_Andalusia_Spain.jpg/800px-Cherry_tree_Orgiva_Andalusia_Spain.jpg', 'Temperate orchard regions, woodland edges', 'Full sun', 120, 'Deciduous fruit tree producing sweet dark berries. Prune in summer to reduce risk of silver leaf fungus.', 8),
(12, 'Tomato', 'Solanum lycopersicum', 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Tomato_je.jpg/800px-Tomato_je.jpg', 'Vegetable gardens, native to South America', 'Full sun', 24, 'Popular warm-season crop. Benefits from regular pinching of side shoots (suckers) and steady moisture.', 9),
(13, 'Chili Pepper', 'Capsicum annuum', 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/28/Red_chili_pepper.jpg/800px-Red_chili_pepper.jpg', 'Warm sunny gardens or containers', 'Full sun', 48, 'Tender perennial grown as an annual. Produces spicy fruits that ripen from green to bright red.', 9),
(14, 'Sweet Orange', 'Citrus x sinensis', 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Orange-Fruit-Pieces.jpg/800px-Orange-Fruit-Pieces.jpg', 'Subtropical orchard or indoor sunny spot', 'Full sun', 96, 'Evergreen citrus tree with fragrant white blooms and sweet citrus fruits that ripen over winter.', 10),
(15, 'Meyer Lemon', 'Citrus x meyeri', 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Meyer_lemon_3.jpg/800px-Meyer_lemon_3.jpg', 'Subtropical regions, patio containers', 'Full sun', 72, 'Compact citrus hybrid producing sweet, juicy yellow fruits. Flowers abundantly throughout spring and summer.', 10),
(16, 'Purple Passion Fruit', 'Passiflora edulis', 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/Passiflora_edulis_fruit.jpg/800px-Passiflora_edulis_fruit.jpg', 'Warm temperate to tropical climbing vine', 'Full sun', 48, 'Vigorous climber with striking flowers followed by aromatic, purple-skinned fruit rich in juice.', 11),
(17, 'Blue Passionflower', 'Passiflora caerulea', 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Passiflora_caerulea_1.jpg/800px-Passiflora_caerulea_1.jpg', 'Sheltered outdoor walls, sunny gardens', 'Full sun', 72, 'Semi-evergreen climber producing elaborate blue-white blossoms and orange egg-shaped fruits.', 11),
(18, 'Pomegranate', 'Punica granatum', 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/76/Pomegranate_fruit.jpg/800px-Pomegranate_fruit.jpg', 'Dry Mediterranean and Middle Eastern regions', 'Full sun', 120, 'Drought-tolerant deciduous shrub bearing scarlet flowers and juicy, seed-filled fruits in autumn.', 12);

-- --------------------------------------------------------

--
-- Table structure for table `species_phase`
--

DROP TABLE IF EXISTS `species_phase`;
CREATE TABLE IF NOT EXISTS `species_phase` (
  `species_id` int(11) NOT NULL,
  `phase_id` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  PRIMARY KEY (`species_id`,`phase_id`,`month`),
  KEY `fk_species_phase_phase` (`phase_id`)
) ;

--
-- Dumping data for table `species_phase`
--

INSERT INTO `species_phase` (`species_id`, `phase_id`, `month`) VALUES
(1, 1, 2),
(1, 1, 3),
(1, 1, 4),
(1, 3, 4),
(1, 3, 5),
(2, 1, 4),
(2, 1, 5),
(2, 2, 10),
(2, 2, 11),
(2, 3, 7),
(2, 3, 8),
(2, 3, 9),
(3, 1, 6),
(3, 1, 7),
(3, 2, 3),
(3, 2, 4),
(4, 1, 6),
(4, 1, 7),
(4, 1, 8),
(4, 2, 3),
(4, 2, 9),
(5, 1, 7),
(5, 1, 8),
(5, 2, 6),
(5, 2, 7),
(5, 2, 8),
(6, 1, 5),
(6, 1, 6),
(7, 2, 4),
(7, 2, 5),
(8, 1, 1),
(8, 1, 11),
(8, 1, 12),
(8, 2, 5),
(9, 1, 4),
(9, 1, 5),
(9, 2, 8),
(9, 2, 9),
(9, 3, 6),
(9, 3, 7),
(10, 1, 5),
(10, 1, 6),
(10, 2, 11),
(10, 2, 12),
(10, 3, 7),
(10, 3, 8),
(11, 1, 4),
(11, 2, 7),
(11, 2, 8),
(11, 3, 6),
(11, 3, 7),
(12, 1, 6),
(12, 1, 7),
(12, 2, 6),
(12, 2, 7),
(12, 2, 8),
(12, 3, 7),
(12, 3, 8),
(12, 3, 9),
(13, 1, 6),
(13, 1, 7),
(13, 2, 5),
(13, 3, 8),
(13, 3, 9),
(13, 3, 10),
(14, 1, 3),
(14, 1, 4),
(14, 2, 2),
(14, 2, 3),
(14, 3, 1),
(14, 3, 11),
(14, 3, 12),
(15, 1, 3),
(15, 1, 4),
(15, 1, 5),
(15, 2, 2),
(15, 2, 3),
(15, 3, 9),
(15, 3, 10),
(15, 3, 11),
(16, 1, 5),
(16, 1, 6),
(16, 1, 7),
(16, 2, 3),
(16, 2, 4),
(16, 3, 8),
(16, 3, 9),
(16, 3, 10),
(17, 1, 6),
(17, 1, 7),
(17, 1, 8),
(17, 2, 3),
(17, 2, 4),
(17, 3, 9),
(17, 3, 10),
(18, 1, 5),
(18, 1, 6),
(18, 2, 2),
(18, 2, 3),
(18, 3, 9),
(18, 3, 10),
(18, 3, 11);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `notify_email` tinyint(1) NOT NULL DEFAULT 1,
  `notify_push` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password_hash`, `name`, `notify_email`, `notify_push`, `created_at`) VALUES
(1, 'fru@example.com', '$2y$10$abcdefghijklmnopqrstuvABCDEFGHIJKLMNOPQRSTUV1234567890', 'Fru', 1, 1, '2026-07-24 04:36:38'),
(2, 'anna@example.com', '$2y$10$bcdefghijklmnopqrstuvwBCDEFGHIJKLMNOPQRSTUVW1234567890', 'Anna', 1, 0, '2026-07-24 04:36:38'),
(3, 'peter@example.com', '$2y$10$cdefghijklmnopqrstuvwxCDEFGHIJKLMNOPQRSTUVWX1234567890', 'Peter', 0, 1, '2026-07-24 04:36:38');

-- --------------------------------------------------------

--
-- Table structure for table `watering_log`
--

DROP TABLE IF EXISTS `watering_log`;
CREATE TABLE IF NOT EXISTS `watering_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `watered_at` datetime NOT NULL,
  `amount_ml` int(11) DEFAULT NULL,
  `source` varchar(20) NOT NULL DEFAULT 'manual',
  `my_plant_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_watering_log_plant_time` (`my_plant_id`,`watered_at`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `watering_log`
--

INSERT INTO `watering_log` (`id`, `watered_at`, `amount_ml`, `source`, `my_plant_id`) VALUES
(1, '2026-07-23 04:36:39', 150, 'manual', 1),
(2, '2026-07-19 04:36:39', 150, 'manual', 1),
(3, '2026-07-18 04:36:39', 500, 'manual', 2),
(4, '2026-07-11 04:36:39', 500, 'manual', 2),
(5, '2026-07-04 04:36:39', 450, 'auto', 2),
(6, '2026-07-15 04:36:39', 300, 'manual', 3),
(7, '2026-07-05 04:36:39', 300, 'manual', 3),
(8, '2026-07-23 10:36:39', 200, 'manual', 4),
(9, '2026-07-22 10:36:39', 200, 'manual', 4),
(10, '2026-07-21 10:36:39', 180, 'manual', 4),
(11, '2026-07-19 04:36:39', 250, 'manual', 5),
(12, '2026-07-17 04:36:39', 600, 'manual', 6),
(13, '2026-07-10 04:36:39', 600, 'auto', 6),
(14, '2026-07-15 04:36:39', 120, 'manual', 7),
(15, '2026-07-23 04:36:39', 400, 'manual', 8),
(16, '2026-07-21 04:36:39', 400, 'manual', 8),
(17, '2026-07-24 05:31:36', NULL, 'manual', 2),
(18, '2026-07-24 05:32:44', NULL, 'manual', 2),
(19, '2026-07-24 05:43:54', NULL, 'manual', 4),
(20, '2026-07-24 05:44:00', NULL, 'manual', 3),
(21, '2026-07-24 05:44:03', NULL, 'manual', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `my_plant`
--
ALTER TABLE `my_plant`
  ADD CONSTRAINT `fk_my_plant_species` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_my_plant_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notification_plant` FOREIGN KEY (`my_plant_id`) REFERENCES `my_plant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notification_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `species`
--
ALTER TABLE `species`
  ADD CONSTRAINT `fk_species_family` FOREIGN KEY (`family_id`) REFERENCES `family` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `species_phase`
--
ALTER TABLE `species_phase`
  ADD CONSTRAINT `fk_species_phase_phase` FOREIGN KEY (`phase_id`) REFERENCES `phase` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_species_phase_species` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `watering_log`
--
ALTER TABLE `watering_log`
  ADD CONSTRAINT `fk_watering_log_plant` FOREIGN KEY (`my_plant_id`) REFERENCES `my_plant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
