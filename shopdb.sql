-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 03:31 PM
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
-- Database: `shopdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `has_size` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `has_size`) VALUES
(1, 'tops', 'tops', 1),
(2, 'bottoms', 'bottoms', 1),
(3, 'outerwear', 'outerwear', 1),
(4, 'accessories', 'accessories', 0);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `full_name`, `phone`, `email`, `address`, `created_at`) VALUES
(4, 'Bùi Thị Thanh Huyền', '0375024785', 'bunnyhyn@gmail.com', NULL, NULL),
(5, 'Nguyễn Gia Khang', '0375024777', 'nguyenkhang@gmail.com', NULL, NULL),
(6, 'Lê Đức', '0375626781', 'leduc1204@gmail.com', NULL, NULL),
(7, 'Trần Thị Ngọc Diệp', '0916374917', 'ngocdiep2092003@gmail.com', NULL, NULL),
(8, 'Trần Thanh Quốc', '0375876456', 'thanhquoc@gmail.com', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `quantity`, `location`, `last_updated`) VALUES
(9, NULL, 100, 'Kho A', '2025-07-05 08:16:53'),
(10, 3, 100, 'Kho A', '2025-06-28 08:05:07'),
(11, 4, 100, 'Kho A', '2025-06-28 08:05:34'),
(12, 5, 100, 'Kho A', '2025-06-28 08:05:57'),
(13, 7, 100, 'Kho A', '2025-06-28 08:06:48'),
(14, 9, 100, 'Kho B', '2025-06-28 08:07:12'),
(15, 10, 100, 'Kho B', '2025-06-28 08:07:47'),
(16, 11, 100, 'Kho B', '2025-06-28 08:08:10'),
(17, 12, 100, 'Kho B', '2025-06-28 08:09:28'),
(18, 13, 100, 'Kho B', '2025-06-28 08:09:44'),
(19, 14, 100, 'Kho B', '2025-06-28 08:10:07'),
(20, 15, 100, 'Kho B', '2025-06-28 08:10:32'),
(21, 16, 100, 'Kho C', '2025-06-28 08:11:25'),
(22, 17, 100, 'Kho C', '2025-06-28 08:11:45'),
(23, 18, 100, 'Kho C', '2025-06-28 08:12:05'),
(24, 19, 100, 'Kho C', '2025-06-28 08:12:27'),
(25, 21, 100, 'Kho C', '2025-06-28 08:12:57'),
(26, 22, 100, 'Kho C', '2025-06-28 08:13:29'),
(27, 23, 100, 'Kho C', '2025-06-28 08:13:40'),
(28, 24, 50, 'Kho D', '2025-06-28 08:14:19'),
(29, 25, 100, 'Kho D', '2025-06-28 08:14:31'),
(30, 30, 50, 'Kho D', '2025-06-28 08:14:51'),
(31, 31, 100, 'Kho A', '2025-06-28 08:15:13'),
(32, 38, 100, 'Kho A', '2025-06-28 08:15:42'),
(33, 40, 100, 'Kho A', '2025-06-28 08:15:56'),
(34, 41, 100, 'Kho A', '2025-06-28 08:16:11'),
(35, 42, 100, 'Kho A', '2025-06-28 08:16:30'),
(36, 43, 100, 'Kho A', '2025-06-28 08:17:03'),
(37, 44, 100, 'Kho B', '2025-06-28 08:17:20'),
(38, 45, 100, 'Kho B', '2025-06-28 08:17:31'),
(39, 46, 100, 'Kho B', '2025-06-28 08:17:50'),
(40, 47, 100, 'Kho B', '2025-06-28 08:18:03'),
(41, 48, 100, 'Kho B', '2025-06-28 08:18:19'),
(42, 49, 100, 'Kho B', '2025-06-28 08:18:29'),
(43, 50, 100, 'Kho B', '2025-06-28 08:18:40'),
(44, 51, 100, 'Kho C', '2025-06-28 08:19:20'),
(45, 52, 100, 'Kho C', '2025-06-28 08:19:42'),
(46, 53, 100, 'Kho C', '2025-06-28 08:20:24'),
(47, 54, 100, 'Kho C', '2025-06-28 08:20:39'),
(48, 55, 100, 'Kho C', '2025-06-28 08:21:31'),
(49, 56, 50, 'Kho D', '2025-06-28 08:21:50'),
(50, 57, 100, 'Kho D', '2025-06-28 08:22:12'),
(51, 59, 50, 'Kho D', '2025-06-28 08:24:50'),
(52, 60, 50, 'Kho D', '2025-06-28 08:25:08'),
(53, 61, 50, 'Kho D', '2025-06-28 08:25:34'),
(54, 62, 100, 'Kho D', '2025-06-28 08:25:44'),
(55, 63, 100, 'Kho D', '2025-06-28 08:25:56'),
(56, 65, 100, 'Kho D', '2025-06-28 08:26:20'),
(57, 66, 50, 'Kho D', '2025-06-28 08:26:54'),
(58, 67, 100, 'Kho A', '2025-06-28 08:27:31'),
(60, 39, 100, 'Kho A', '2025-07-01 09:37:12'),
(61, 68, 100, 'Kho A', '2025-07-05 08:48:34'),
(62, 69, 100, 'Kho B', '2025-07-05 08:49:19'),
(63, 70, 100, 'Kho B', '2025-07-05 08:49:29'),
(64, 71, 100, 'Kho B', '2025-07-05 08:49:45'),
(65, 72, 100, 'Kho B', '2025-07-05 08:49:58'),
(66, 73, 100, 'Kho B', '2025-07-05 08:50:13'),
(67, 74, 100, 'Kho B', '2025-07-05 08:50:30'),
(68, 75, 100, 'Kho A', '2025-07-05 08:51:44'),
(69, 76, 100, 'Kho A', '2025-07-07 08:49:03');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipping','completed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `user_id`, `customer_name`, `phone`, `email`, `address`, `payment_method`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(4, NULL, NULL, 'Bùi Thị Thanh Huyền', '0375024785', 'bunnyhyn@gmail.com', '35 Tô Ký, TCH 18, TCH, Quận 12', 'cod', 530000.00, 'completed', '2025-07-02 08:14:08', '2025-07-05 00:12:59'),
(5, NULL, NULL, 'Nguyễn Gia Khang', '0375024777', 'nguyenkhang@gmail.com', 'Thuận Giao 22, Thuận An, Bình Dương', 'cod', 670000.00, 'processing', '2025-07-02 08:20:56', '2025-07-02 08:39:44'),
(6, NULL, NULL, 'Nguyễn Gia Khang', '0375024777', 'nguyenkhang@gmail.com', 'Thuận Giao 22, Thuận An, Bình Dương', 'cod', 770000.00, 'shipping', '2025-07-03 00:10:28', '2025-07-03 00:45:01'),
(7, NULL, NULL, 'Lâm Thanh Thủy', '0375024785', 'lamthanhthuy121@gmail.com', '70 Tô Ký, Tân Chánh Hiệp 18, Tân Chánh Hiệp, Quận 12', 'cod', 540000.00, 'pending', '2025-07-03 16:31:22', '2025-07-03 16:31:22'),
(8, NULL, NULL, 'Trần Thị Ngọc Diệp', '0916374917', 'ngocdiep2092003@gmail.com', '145/54 Tô Ký, Tân Chánh Hiệp, Quận 12', 'cod', 480000.00, 'pending', '2025-07-04 23:28:16', '2025-07-04 23:28:16'),
(9, 4, NULL, 'Bùi Thị Thanh Huyền', '0375024785', 'bunnyhyn@gmail.com', '35 Tô Ký, TCH 18, TCH, Quận 12', 'cod', 350000.00, 'completed', '2025-07-05 00:21:40', '2025-07-05 15:05:40'),
(10, 4, NULL, 'Bùi Thị Thanh Huyền', '0375024785', 'bunnyhyn@gmail.com', '35 Tô Ký, TCH 18, TCH, Quận 12', 'cod', 830000.00, 'shipping', '2025-07-05 00:23:56', '2025-07-05 15:06:12'),
(11, NULL, NULL, 'Lê Đức', '0375626781', 'leduc1204@gmail.com', '357/58 Trường Chinh, Tân Bình, HCM', 'cod', 530000.00, 'completed', '2025-07-05 00:26:01', '2025-07-05 15:05:21'),
(12, 6, NULL, 'Lê Đức', '0375626781', 'leduc1204@gmail.com', '357/58 Trường Chinh, Tân Bình, HCM', 'cod', 670000.00, 'shipping', '2025-07-05 00:41:43', '2025-07-05 00:42:13'),
(13, 7, NULL, 'Trần Thị Ngọc Diệp', '0916374917', 'ngocdiep2092003@gmail.com', '145/54 Tô Ký, Tân Chánh Hiệp, Quận 12', 'cod', 430000.00, 'shipping', '2025-07-05 13:30:48', '2025-07-06 10:27:41'),
(14, 6, NULL, 'Lê Đức', '0375626781', 'leduc1204@gmail.com', '357/58 Trường Chinh, Tân Bình, HCM', 'cod', 280000.00, 'completed', '2025-07-05 13:32:59', '2025-07-06 10:28:00'),
(15, 7, NULL, 'Trần Thị Ngọc Diệp', '0916374917', 'ngocdiep2092003@gmail.com', '145/54 Tô Ký, Tân Chánh Hiệp, Quận 12', 'cod', 630000.00, 'processing', '2025-07-05 13:35:11', '2025-07-07 15:46:08'),
(16, 8, NULL, 'Trần Thanh Quốc', '0375876456', 'thanhquoc@gmail.com', 'Thua', 'cod', 430000.00, 'pending', '2025-07-05 14:49:00', '2025-07-05 14:49:00'),
(17, 8, NULL, 'Trần Thanh Quốc', '0375876456', 'thanhquoc@gmail.com', 'Thua', 'cod', 330000.00, 'pending', '2025-07-06 10:22:28', '2025-07-06 10:22:28'),
(18, 5, NULL, 'Nguyễn Gia Khang', '0375024777', 'nguyenkhang@gmail.com', 'Thuận Giao 22, Thuận An, Bình Dương', 'cod', 350000.00, 'pending', '2025-07-07 15:39:06', '2025-07-07 15:39:06');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `size` varchar(10) NOT NULL DEFAULT 'S',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `size`, `created_at`) VALUES
(1, 4, 66, 2, 250000.00, 'M', '2025-07-02 08:14:08'),
(2, 5, 65, 1, 400000.00, 'free', '2025-07-02 08:20:56'),
(3, 5, 60, 1, 240000.00, 'free', '2025-07-02 08:20:56'),
(4, 6, 4, 1, 260000.00, 'L', '2025-07-03 00:10:28'),
(5, 6, 39, 1, 480000.00, 'M', '2025-07-03 00:10:28'),
(6, 7, 66, 1, 250000.00, 'M', '2025-07-03 16:31:22'),
(7, 7, 15, 1, 260000.00, 'M', '2025-07-03 16:31:22'),
(8, 8, 31, 1, 450000.00, 'M', '2025-07-04 23:28:16'),
(9, 9, 48, 1, 320000.00, 'M', '2025-07-05 00:21:40'),
(10, 10, 63, 1, 400000.00, 'free', '2025-07-05 00:23:56'),
(11, 10, 5, 1, 400000.00, 'M', '2025-07-05 00:23:57'),
(12, 11, 62, 1, 500000.00, 'free', '2025-07-05 00:26:01'),
(13, 12, 67, 1, 240000.00, 'L', '2025-07-05 00:41:43'),
(14, 12, 5, 1, 400000.00, 'M', '2025-07-05 00:41:43'),
(15, 13, 65, 1, 400000.00, 'free', '2025-07-05 13:30:48'),
(16, 14, 66, 1, 250000.00, 'M', '2025-07-05 13:32:59'),
(17, 15, 53, 1, 600000.00, 'M', '2025-07-05 13:35:11'),
(18, 16, 5, 1, 400000.00, 'M', '2025-07-05 14:49:00'),
(19, 17, 9, 1, 300000.00, 'L', '2025-07-06 10:22:28'),
(20, 18, 10, 1, 320000.00, 'M', '2025-07-07 15:39:06');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `status`, `notes`, `created_by`, `created_at`) VALUES
(10, 4, 'completed', '', NULL, '2025-07-05 00:12:59'),
(11, 12, 'shipping', '', NULL, '2025-07-05 00:42:13'),
(12, 11, 'shipping', '', NULL, '2025-07-05 15:04:52'),
(13, 11, 'completed', '', NULL, '2025-07-05 15:05:21'),
(14, 9, 'completed', '', NULL, '2025-07-05 15:05:40'),
(15, 10, 'shipping', 'Cảm ơn bạn đã đặt hàng', NULL, '2025-07-05 15:06:12'),
(16, 13, 'shipping', '', NULL, '2025-07-06 10:27:41'),
(17, 14, 'completed', '', NULL, '2025-07-06 10:28:00'),
(18, 15, 'processing', 'hgvxgvgvg', NULL, '2025-07-07 15:46:08');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(50) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page_name`, `title`, `content`, `updated_at`) VALUES
(1, 'about', 'Giới thiệu', 'Chúng tôi là cửa hàng ABC, chuyên cung cấp các sản phẩm chất lượng cao.', '2025-06-07 16:23:32'),
(2, 'policies', 'Chính sách', 'Chính sách đổi trả: 7 ngày, điều kiện còn nguyên vẹn.', '2025-06-07 16:23:32'),
(3, 'faq', 'FAQ', 'Câu hỏi thường gặp: Giao hàng mất bao lâu? - 2-3 ngày.', '2025-06-07 16:23:32');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`, `expires_at`) VALUES
('ngoxuanquynh.dk@gmail.com', 'c4006550f0fe11e33e16f07ae2dbb7462441046802c63783981b09ac4b0d1a20', '2025-07-05 08:42:37', '2025-07-05 11:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Quản lý sản phẩm', 'Cho phép thêm/sửa/xóa sản phẩm', '2025-06-07 15:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `images` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `has_size` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `sold_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`, `category_id`, `images`, `stock`, `has_size`, `created_at`, `sold_count`) VALUES
(3, 'SWE ORBIT LS TEE - BLACK', 500000.00, 'ORBIT L/S TEE - Chiếc áo tay dài mới nằm trong bộ sưu tập \"Refined Origins\" được thiết kế theo phong cách thể thao năng động với điểm nhấn nằm ở những đường viền phối trên thân và tay áo. Hình in mặt trước và sau áo được in kéo lụa giả crack sắc nét.', 'black_stripes.jpg', 1, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(4, 'Áo thun Graffiti Art', 260000.00, 'In họa tiết graffiti, chất liệu thoáng mát', 'graffiti_art.jpg', 1, NULL, 99, 1, '2025-06-27 01:59:39', 0),
(5, 'SWE CREST BOXY TEE - WHITE', 400000.00, 'CREST BOXY TEE - Chiếc áo thun mới với artwork được sử dụng chất liệu in kéo lụa kèm hiệu ứng nứt tinh tế. Điểm nhấn của áo nằm ở phần bo cổ khác màu được dệt riêng bằng sợi PE tạo điểm nhấn cho trang phục của bạn.', 'vintage_wash.jpg', 1, NULL, 97, 1, '2025-06-27 01:59:39', 0),
(7, 'SWE FAM KNIT BOXY TEE - MISTRAL', 400000.00, 'FAM KNIT BOXY TEE - Chiếc áo len mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" được thiết kế theo phong cách retro streetwear nhẹ nhàng đánh dấu cột mốc hành trình 9 năm của SWE. Điểm nhấn của áo nằm ở họa tiết typo mặt trước được dệt tỉ mỉ. Kết cấu dệt 2 lớp giúp áo đứng form tốt đem lại cảm giác mềm mại, mát mẻ, thoáng khí nhưng vẫn giữ được độ rủ của áo.', 'double_layer.jpg', 1, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(9, 'Quần jeans Rách Nam', 300000.00, 'Quần jeans rách phong cách street, ống suông', 'jeans_rach_nam.jpg', 2, NULL, 99, 1, '2025-06-27 01:59:39', 0),
(10, 'Quần jeans Oversized Nữ', 320000.00, 'Thiết kế thoải mái và thời thượng', 'jeans_oversized_nu.jpg', 2, NULL, 99, 1, '2025-06-27 01:59:39', 0),
(11, 'Quần jogger Đen', 280000.00, 'Quần jogger co giãn, phù hợp dạo phố', 'jogger_den.jpg', 2, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(12, 'Quần short Cargo', 250000.00, 'Quần short cargo, phong cách bụi bặm', 'short_cargo.jpg', 2, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(13, 'Quần jeans Slim Fit', 310000.00, 'Quần jeans ôm dáng, chất liệu bền đẹp', 'jeans_slim.jpg', 2, NULL, 99, 1, '2025-06-27 01:59:39', 0),
(14, 'Quần kaki Xám', 150000.00, 'Quần kaki đa năng, dễ phối đồ', 'kaki_xam.jpg', 2, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(15, 'Quần short Jeans', 260000.00, 'Quần ngắn jeans, phong cách mùa hè', 'short_jeans.jpg', 2, NULL, 99, 1, '2025-06-27 01:59:39', 0),
(16, 'Hoodie Unisex Xám', 450000.00, 'Hoodie dày dặn, giữ ấm tốt, phong cách SWE', 'hoodie_xam.jpg', 3, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(17, 'Hoodie Oversized Đen', 470000.00, 'Thiết kế oversized, logo nhỏ trước ngực', 'hoodie_den.jpg', 3, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(18, 'Hoodie In Logo Lớn', 460000.00, 'Logo in lớn phía sau, phong cách streetwear', 'hoodie_logo.jpg', 3, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(19, 'Áo khoác Bomber', 500000.00, 'Áo khoác bomber, chất liệu nhẹ, phong cách', 'bomber_jacket.jpg', 3, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(21, 'Hoodie Zip-up', 490000.00, 'Hoodie có khóa kéo, tiện lợi và năng động', 'hoodie_zip.jpg', 3, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(22, 'Áo khoác Puffer', 550000.00, 'Áo khoác puffer, giữ ấm tối ưu', 'puffer_jacket.jpg', 3, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(23, 'Áo khoác Windbreaker', 430000.00, 'Áo gió chống nước, phong cách trẻ trung', 'windbreaker.jpg', 3, NULL, 100, 1, '2025-06-27 01:59:39', 0),
(24, 'Mũ Snapback Đen', 150000.00, 'Mũ snapback logo SWE, phong cách hip-hop', 'snapback_den.jpg', 4, NULL, 50, 0, '2025-06-27 01:59:39', 0),
(25, 'Túi Đeo Chéo Xám', 200000.00, 'Túi nhỏ gọn, tiện lợi cho dạo phố', 'tui_xam.jpg', 4, NULL, 100, 0, '2025-06-27 01:59:39', 0),
(30, 'Tất Cao Cổ SWE', 80000.00, 'Tất cao cổ in logo, phong cách streetwear', 'tat_cao_co.jpg', 4, NULL, 50, 0, '2025-06-27 01:59:39', 0),
(31, 'LOOP BOXY TEE - GRAY', 450000.00, 'Chiếc áo thun mới được thiết kế đơn giản với 3 phối màu nổi bật.', 'ao_loop_boxy.jpg', 1, NULL, 99, 1, '2025-06-27 01:59:39', 0),
(38, 'SWE SUNSET POCKET SHIRT - CREAM', 450000.00, 'Chiếc áo sơ mi mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" được thiết kế nhẹ nhàng với điểm nhấn của áo nằm ở họa tiết thêu sắc nét trên túi áo trước kèm tên thương hiệu mặt sau mang lại cảm giác tràn đầy năng lượng mùa hè.', 'SWE SUNSET POCKET SHIRT - CREAM.webp', 1, NULL, 100, 1, '2025-06-27 11:47:57', 0),
(39, 'SWE VISION L/S TEE - CREAM', 480000.00, 'Chiếc áo tay dài mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" được thiết kế theo phong cách streetwear hiện đại kết hợp retro thể thao với điểm nhấn nằm ở những họa tiết ở mặt trước và 2 bên tay áo được in lụa sắc nét. Phần bo cổ đặc biệt được dệt riêng bằng sợi PE dày dặn kèm dây rút ở lai áo giúp điều chỉnh linh hoạt và duy trì form dáng đẹp.', 'SWE VISION.jpg', 1, NULL, 99, 1, '2025-06-27 11:49:20', 0),
(40, 'SWE VISION L/S TEE - BROWN', 480000.00, 'Chiếc áo tay dài mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" được thiết kế theo phong cách streetwear hiện đại kết hợp retro thể thao với điểm nhấn nằm ở những họa tiết ở mặt trước và 2 bên tay áo được in lụa sắc nét. Phần bo cổ đặc biệt được dệt riêng bằng sợi PE dày dặn kèm dây rút ở lai áo giúp điều chỉnh linh hoạt và duy trì form dáng đẹp.', 'BROWN.jpg', 1, NULL, 100, 1, '2025-06-27 11:50:33', 0),
(41, 'SWE VISION TEE - CREAM', 430000.00, 'Chiếc áo thun mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" được thiết kế đơn giản với 4 phối màu nổi bật. Điểm nhấn của áo nằm ở họa tiết mặt trước và sau sử dụng kỹ thuật thêu móc xích tỉ mỉ đánh dấu cột mốc hành trình 9 năm của SWE.', 'SWE VISION TEE - CREAM.webp', 1, NULL, 100, 1, '2025-06-27 11:51:59', 0),
(42, 'SWE 4EVERKID BOXY TEE - WHITE', 400000.00, 'Chiếc áo thun mới được thiết kế đơn giản với phối màu tươi sáng. Điểm nhấn của áo nằm ở họa tiết typo mặt trước được sử dụng kỹ thuật thêu móc xích tỉ mỉ. ', 'SWE 4EVERKID BOXY TEE - WHITE.webp', 1, NULL, 100, 1, '2025-06-27 11:53:04', 0),
(43, 'SWE PROJECT L/S SHIRT - WHITE', 450000.00, 'PROJECT L/S SHIRT - Chiếc áo sơ mi mới vừa được ra mắt trong dịp Tết 2025 của nhà SWE. Áo được thiết kế phong cách tối giản với điểm nhấn nằm ở chất liệu vải Kate sọc kèm họa tiết Typography được sử dụng kỹ thuật in lụa sắc nét phía mặt trước của áo.', 'SWE PROJECT.webp', 1, NULL, 100, 1, '2025-06-27 11:54:26', 0),
(44, 'SWE VAULT CARGO SHORTS - WHITE', 450000.00, 'VAULT CARGO SHORTS - Chiếc quần shorts mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" được thiết kế theo phong cách túi hộp với điểm nhấn nằm ở phần nắp túi được gắn tag da SWE nổi bật. Phần lưng thun kèm dây rút giúp điều chỉnh linh hoạt và duy trì form dáng đẹp. Bên trong có lót lưới poly tạo sự thoải mái và thoáng mát khi sử dụng.', 'SWE VAULT CARGO SHORTS - WHITE.webp', 2, NULL, 100, 1, '2025-06-27 11:55:33', 0),
(45, 'SWE CARGO PANTS - DARKBYTE CAMO', 720000.00, 'CARGO PANTS - Chiếc quần túi hộp mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" được thiết kế họa tiết camo trendy. Điểm nhấn của quần nằm ở phần nắp túi được trang trí đóng mắt cáo kèm patch thêu logo swe sắc nét tạo điểm nhấn cho trang phục của bạn. Phần lai quần có dây rút giúp điều chỉnh linh hoạt và duy trì form dáng đẹp.', 'SWE CARGO PANTS - DARKBYTE CAMO.webp', 2, NULL, 100, 1, '2025-06-27 11:56:29', 0),
(46, 'SWE UNIT9 SHORTS - BLACK', 420000.00, 'UNIT9 SHORTS - Chiếc quần shorts mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" không thể thiếu trong tủ đồ của bạn vào những ngày hè nắng nóng. Được sử dụng chất liệu vải dù nylon kèm lót lưới poly tạo sự thoải mái và thoáng mát khi sử dụng. Điếm nhấn nằm ở họa tiết số 09 được thêu đắp dạ mặt sau và logo SWE được thêu sắc nét mặt trước. Cạp chun co giãn kèm dây rút giúp điều chỉnh linh hoạt.', 'SWE UNIT9 SHORTS - BLACK.webp', 2, NULL, 100, 1, '2025-06-27 11:57:27', 0),
(47, 'SWE 2BAR SHORTS - BLUE', 320000.00, '2BAR SHORTS - Chiếc quần shorts mới nằm trong bộ sưu tập \"FOR THE YOUTH\" của nhà SWE mang phong cách trẻ trung và năng động. Điểm nhấn của quần nằm ở họa tiết logo SWE mới được thêu tinh tế phía bên đùi trái kèm các đường line chạy dọc 2 bên tạo cảm giác nổi bật và ấn tượng hơn cho quần. Phần thắt lưng tô điểm bằng những chi tiết viền vải được may đều xung quanh eo. Với thiết kế thể thao cùng chất liệu thoải mái, đây sẽ là một sự lựa chọn hoàn hảo dành cho những bạn nữ ưa thích vận động.', 'SWE 2BAR SHORTS - BLUE.webp', 2, NULL, 100, 1, '2025-06-27 11:58:19', 0),
(48, 'SWE 2BAR SHORTS - RED', 320000.00, '2BAR SHORTS - Chiếc quần shorts mới nằm trong bộ sưu tập \"FOR THE YOUTH\" của nhà SWE mang phong cách trẻ trung và năng động. Điểm nhấn của quần nằm ở họa tiết logo SWE mới được thêu tinh tế phía bên đùi trái kèm các đường line chạy dọc 2 bên tạo cảm giác nổi bật và ấn tượng hơn cho quần. Phần thắt lưng tô điểm bằng những chi tiết viền vải được may đều xung quanh eo. Với thiết kế thể thao cùng chất liệu thoải mái, đây sẽ là một sự lựa chọn hoàn hảo dành cho những bạn nữ ưa thích vận động.', 'SWE 2BAR SHORTS - RED.webp', 2, NULL, 99, 1, '2025-06-27 11:59:11', 0),
(49, 'SWE NEBULA CARGO PANTS - WHITE', 380000.00, 'NEBULA CARGO PANTS - Chiếc quần cargo được thiết kế theo phong cách hiphop. Với điểm nhấn nằm ở phần túi hộp được phối dây ấn tượng kèm phần ống quần được phối lưới độc đáo. Quần có sử dụng dây rút ở eo và ống quần giúp bạn dễ dàng tăng chỉnh kích cỡ để phù hợp với cơ thể.', 'SWE NEBULA CARGO PANTS - WHITE.webp', 2, NULL, 100, 1, '2025-06-27 12:00:28', 0),
(50, 'SWE NEBULA CARGO PANTS - BLACK', 380000.00, 'NEBULA CARGO PANTS - Chiếc quần cargo được thiết kế theo phong cách hiphop. Với điểm nhấn nằm ở phần túi hộp được phối dây ấn tượng kèm phần ống quần được phối lưới độc đáo. Quần có sử dụng dây rút ở eo và ống quần giúp bạn dễ dàng tăng chỉnh kích cỡ để phù hợp với cơ thể.', 'SWE NEBULA CARGO PANTS - BLACK.webp', 2, NULL, 100, 1, '2025-06-27 12:01:17', 0),
(51, 'SWE BADGE BOMBER JACKET - BLACK', 900000.00, 'BADGE BOMBER JACKET - Chiếc áo khoác mới nằm trong bộ sưu tập \"Refined Origins\" được thiết kế theo phong cách đơn giản nhưng đầy cá tính với điểm nhấn nằm ở những đường may phối rã kèm logo SWE mới làm bằng kim loại được gắn tinh tế trên ngực trái. Áo được sử dụng khóa zip 2 chiều giúp bạn có thể dễ dàng phối đồ theo nhiều phong cách khác nhau.', 'SWE BADGE BOMBER JACKET - BLACK.webp', 3, NULL, 100, 1, '2025-06-27 12:03:00', 0),
(52, 'SWE RIDGE ZIP HOODIE - BROWN', 720000.00, 'RIDGE ZIP HOODIE - Chiếc hoodie nỉ mới nằm trong bộ sưu tập \"Refined Origins\" được thiết kế theo phong cách bụi bặm với điểm nhấn nằm ở những chi tiết may phối rã bắt mắt. Áo được xử lý washed để tạo ra tone màu nâu ấn tượng. Logo SWE mới được cách điệu may đắp trước ngực áo, kèm các hạt đá được đính tinh tế.', 'SWE RIDGE ZIP HOODIE - BROWN.webp', 3, NULL, 100, 1, '2025-06-27 12:04:03', 0),
(53, 'SWE SLATE JACKET', 600000.00, 'SLATE JACKET - Chiếc áo khoác mới nằm trong bộ SLATE SET được sử dụng chất liệu vải denim cotton 100%, độ dày 13 Oz. Áo được lấy cảm hứng từ những chiếc Vintage denim jacket, với chi tiết túi được cách điệu kèm chi tiết xếp ly đằng sau áo ấn tượng. Phần nút kim loại được đúc logo SWE sắc nét. Áo sử dụng công nghệ stone wash tạo ra tone màu xanh đặc trưng của các sản phẩm denim.', '1751001179_SWE SLATE JACKET.jpg', 3, NULL, 99, 1, '2025-06-27 12:05:16', 0),
(54, 'SWE INFINITY LEATHER JACKET - BLACK', 720000.00, 'INFINITY LEATHER JACKET - Chiếc áo da mới với phong cách may phối rã rập ấn tượng. Điểm nhấn của áo nằm ở họa tiết logo SWE mới được thêu tinh tế phía bên ngực phải kèm một chiếc túi nhỏ phía bên ngực trái. Áo được thiết kế cổ trụ và lót vải dù phía bên trong tạo cảm giác thoải mái cho bạn khi sử dụng. Phần đầu khóa kéo được đúc logo SWE sắc nét.', 'SWE INFINITY LEATHER JACKET - BLACK.webp', 3, NULL, 100, 1, '2025-06-27 12:06:25', 0),
(55, 'SWE INTERWAVE ZIP HOODIE - CREAM', 500000.00, 'INTERWAVE ZIP HOODIE - Chiếc áo hoodie mới nằm trong bộ sưu tập \"FOR THE YOUTH\" của nhà SWE mang phong cách trẻ trung và năng động. Áo được lấy cảm hứng từ mẫu áo WAVELINES ZIP HOODIE cũ với điểm nhấn của áo nằm ở các đường line lượn sóng dọc 2 bên cánh tay. Họa tiết logo SWE mới ở trước ngực trái và họa tiết chữ Kid Atelier phía sau lưng được thêu móc xích rất tinh tế, phần đầu khóa kéo được đúc logo SWE sắc nét.', 'SWE INTERWAVE ZIP HOODIE - CREAM.webp', 3, NULL, 100, 1, '2025-06-27 12:07:22', 0),
(56, 'SWE RAINCOAT', 150000.00, 'SWE RAINCOAT - Chiếc áo mưa mới của nhà SWE mang phong cách tối giản với điểm nhấn nằm ở họa tiết logo SWE được in tinh tế ở ngực trái, phần mũ áo có kèm dây rút giúp bạn dễ dàng điều chỉnh form phù hợp. Với kích thước 1m2x1m3 áo có thể được sử dụng tối đa dành cho 2 người lớn.', 'SWE RAINCOAT.webp', 4, NULL, 50, 1, '2025-06-27 12:08:13', 0),
(57, 'SWE CHERRY BANDANA', 150000.00, 'CHERRY BANDANA - Chiếc khăn mới được làm từ vải lụa satin poly 100%, định lượng 120gsm mang đến sự mềm mại và dễ chịu khi sử dụng. Với thiết kế hoạ tiết cherry SWE dễ thương, sản phẩm sẽ là điểm nhấn nổi bật cho phong cách của bạn.', 'SWE CHERRY BANDANA.webp', 4, NULL, 100, 1, '2025-06-27 12:08:59', 0),
(59, 'SWE TRADEMARK SCARF - RED', 240000.00, 'TRADEMARK SCARF - Chiếc khăn quàng cổ mới được thiết kế đơn giản nhưng cũng đầy cá tính với điểm nhấn nằm ở họa tiết logo SWE được in kéo lụa sắc nét. Khăn được làm bằng chất liệu len cao cấp không bị nhão, không bị xù lông khi sử dụng. Nó không chỉ giúp bạn giữ ấm mà còn là một món phụ kiện hoàn hảo để hoàn thiện trang phục mùa Đông của bạn.', 'SWE TRADEMARK SCARF - RED.webp', 4, NULL, 50, 1, '2025-06-27 12:10:14', 0),
(60, 'SWE TRADEMARK SCARF - BLACK', 240000.00, 'TRADEMARK SCARF - Chiếc khăn quàng cổ mới được thiết kế đơn giản nhưng cũng đầy cá tính với điểm nhấn nằm ở họa tiết logo SWE được in kéo lụa sắc nét. Khăn được làm bằng chất liệu len cao cấp không bị nhão, không bị xù lông khi sử dụng. Nó không chỉ giúp bạn giữ ấm mà còn là một món phụ kiện hoàn hảo để hoàn thiện trang phục mùa Đông của bạn.', 'SWE TRADEMARK SCARF - BLACK.webp', 4, NULL, 49, 1, '2025-06-27 12:11:50', 0),
(61, 'SWE COLD CUP', 250000.00, 'SWE COLD CUP - 1 chiếc ly giữ nhiệt vừa được nhà SWE cho ra mắt trong năm mới 2024. Họa tiết đơn giản với hình in logo SWE được thiết kế bởi các ngôi sao màu bạc. SWE COLD CUP phù hợp cho cả việc đi học, đi làm hoặc đi chơi. Sản phẩm được làm bằng chất liệu Inox 304 2 lớp nên các bạn yên tâm có thể sử dụng được nhiều lần mà không sợ bị hư hỏng.', 'SWE COLD CUP.webp', 4, NULL, 50, 1, '2025-06-27 12:14:16', 0),
(62, 'SWE 2 POCKET BACKPACK', 500000.00, 'Kích thước : 40 x 31 x 14 cm Có hai túi hộp phía trước, có ngăn túi ẩn bên hông. Ngăn đựng laptop Chất liệu : Polyester trượt nước Khoá kéo YKK với thiết kế logo SWE dập nổi.', 'SWE 2 POCKET BACKPACK.webp', 4, NULL, 99, 1, '2025-06-27 12:15:14', 0),
(63, 'SWE BEACH TOWEL - BLUE', 400000.00, 'BEACH TOWEL - Chiếc khăn biển mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" được thiết kế đơn giản với họa tiết tên thương hiệu được in chuyển nhiệt sắc nét và mang đậm dấu ấn của SWE. Đây không chỉ là một chiếc khăn biển mà còn là một món phụ kiện hoàn hảo để hoàn thiện trang phục mùa Hè của bạn.', 'SWE BEACH TOWEL - BLUE.webp', 4, NULL, 99, 1, '2025-06-27 12:16:11', 0),
(65, 'SWE BEACH TOWEL - RED', 400000.00, 'BEACH TOWEL - Chiếc khăn biển mới nằm trong bộ sưu tập \"THE FUTURE IS BRIGHT\" được thiết kế đơn giản với họa tiết tên thương hiệu được in chuyển nhiệt sắc nét và mang đậm dấu ấn của SWE. Đây không chỉ là một chiếc khăn biển mà còn là một món phụ kiện hoàn hảo để hoàn thiện trang phục mùa Hè của bạn.', 'SWE BEACH TOWEL - RED.webp', 4, NULL, 98, 1, '2025-06-27 12:17:05', 0),
(66, 'SWE IGNITE JERSEY - BLACK', 250000.00, 'IGNITE JERSEY - Chiếc áo jersey mới được thiết kế theo phong cách thể thao và năng động với điểm nhấn nằm ở các hình in mặt trước và sau áo được in chuyển nhiệt sắc nét. Họa tiết monogram mới nhà SWE được in chìm khắp bề mặt của áo tạo cảm giác ấn tượng hơn cho trang phục của bạn.', 'SWE IGNITE JERSEY - BLACK.webp', 1, NULL, 44, 1, '2025-06-27 12:19:36', 0),
(67, 'SWE SHADOW TANKTOP (WOMEN) - GRAPHITE', 240000.00, 'SHADOW TANKTOP - Mẫu áo tank top mới nằm trong bộ sưu tập \"Refined Origins\". Với điểm nhấn của áo nằm ở những đường may phối rã kèm logo SWE mới được thêu tinh tế tệp với màu áo ở giữa ngực. Áo được xử lý washed để tạo ra những tone màu đặc biệt.', 'SWE SHADOW TANKTOP (WOMEN) - GRAPHITE.webp', 1, NULL, 98, 1, '2025-06-27 12:20:29', 0),
(68, 'SWE KA SHIRT - PINK', 500000.00, 'KA SHIRT - Chiếc áo sơ mi mới vừa được nhà SWE cho ra mắt nằm trong SWE SUMMER COLLECTION 2024 với 2 phối màu đầy tươi sáng. Điểm nhấn của chiếc áo nằm ở các họa tiết chữ được thêu phía trước và sau áo, dãy số \"25251325\" mang 1 thông điệp đầy ý nghĩa \"yêu em, yêu em, trọn đời yêu em\".', 'SWE KA SHIRT - PINK.jpg', 1, NULL, 100, 1, '2025-07-05 14:15:28', 0),
(69, 'SWE RED BUTTON SHIRT - BLACK', 450000.00, 'RED BUTTON SHIRT - Chiếc áo sơ mi mới vừa được nhà SWE cho ra mắt vào dịp tết 2024 với 2 phối màu đen và kem. Điểm nhấn của chiếc áo nằm ở các họa tiết được thêu ngay phần cổ áo và logo SWE được thêu ở ngực trái. Phần nút áo đặc biệt với 1 nút màu đỏ khác màu với các nút còn lại để tượng trưng cho sự may mắn trong năm mới.', 'SWE RED BUTTON SHIRT - BLACK.jpg', 1, NULL, 100, 1, '2025-07-05 14:17:08', 0),
(70, 'SWE RED BUTTON SHIRT - OFF WHITE', 450000.00, 'RED BUTTON SHIRT - Chiếc áo sơ mi mới vừa được nhà SWE cho ra mắt vào dịp tết 2024 với 2 phối màu đen và kem. Điểm nhấn của chiếc áo nằm ở các họa tiết được thêu ngay phần cổ áo và logo SWE được thêu ở ngực trái. Phần nút áo đặc biệt với 1 nút màu đỏ khác màu với các nút còn lại để tượng trưng cho sự may mắn trong năm mới.', 'SWE RED BUTTON SHIRT - OFF WHITE.jpg', 1, NULL, 100, 1, '2025-07-05 14:18:33', 0),
(71, 'SWE BAGGY CARGO SHORTS - BLACK', 480000.00, 'BAGGY CARGO SHORTS - Những chiếc quần BAGGY SHORTS đang trở thành xu hướng hiện nay bởi sự thoải mái và kiểu dáng trendy. Nắm bắt điều đó nhà SWE đã cho ra mắt BAGGY CARGO SHORTS với điểm nhấn ở phần túi hộp được đặt 2 bên ống quần, giúp bạn mang theo được nhiều vật dụng khi ra đường. Phần lưng và ống quần được may dây rút để bạn có thể điều chỉnh phù hợp với tỉ lệ cơ thể.', 'SWE BAGGY CARGO SHORTS - BLACK.jpg', 2, NULL, 100, 1, '2025-07-05 14:21:22', 0),
(72, 'SWE EMBLEM WASHED SWEATPANTS - RUSTIC', 470000.00, 'EMBLEM WASHED SWEATPANTS - Chiếc quần nỉ mới nằm trong bộ sưu tập \"Refined Origins\" được thiết kế theo phong cách bụi bặm với điểm nhấn nằm ở những chi tiết sờn rách kèm may phối vải bo trên sườn quần bắt mắt. Quần được xử lý washed để tạo ra tone màu ấn tượng. Logo SWE mới làm bằng kim loại được gắn tinh tế trên túi quần trái. Sự kết hợp này cùng với EMBLEM WASHED HOODIE sẽ giúp bạn có 1 set đồ cực kì thời trang và phong cách.', 'SWE EMBLEM WASHED SWEATPANTS - RUSTIC.jpg', 2, NULL, 100, 1, '2025-07-05 14:24:42', 0),
(73, 'SWE BLAZE JEANS', 500000.00, 'BLAZE JEANS - Được sử dụng chất liệu vải jeans cotton 100% 12 Oz và xử lý wash màu kèm phun PP giúp tạo ra được tone màu độc đáo. Điểm nhấn của quần nằm ở những chi tiết cách điệu chạy dọc xung quanh ống, cùng mắt cáo ấn tượng được đặt khắp bề mặt quần. Mang đến sự phá cách và tinh nghịch cho những ai yêu quyến rũ. Form dáng BAGGY tạo sự rộng rãi, thoải mái khi sử dụng.', 'SWE BLAZE JEANS.jpg', 2, NULL, 100, 1, '2025-07-05 14:26:14', 0),
(74, 'Monogram Laser Baggy Jeans/ Blue', 600000.00, 'Chiếc quần jeans mới nằm trong bộ sưu tập \"Refined Origins\" được thiết kế theo phong cách đơn giản với điểm nhấn nằm ở những chi tiết cách điệu 2 bên thân quần và đường viền túi sau bắt mắt. Quần được xử lý washed để tạo ra những tone màu bụi bặm.', 'Monogram Laser Baggy Jeans.webp', 2, NULL, 100, 1, '2025-07-05 14:30:59', 0),
(75, 'Striped Baseball Jersey/ Red', 550000.00, 'KA SHIRT - Chiếc áo sơ mi mới vừa được nhà SWE cho ra mắt nằm trong SWE SUMMER COLLECTION 2024 với 2 phối màu đầy tươi sáng. Điểm nhấn của chiếc áo nằm ở các họa tiết chữ được thêu phía trước và sau áo, dãy số \"25251325\" mang 1 thông điệp đầy ý nghĩa \"yêu em, yêu em, trọn đời yêu em\".', 'Striped Baseball Jersey.webp', 1, NULL, 100, 1, '2025-07-05 14:35:58', 0),
(76, 'Áo thun', 500000.00, 'thoáng mát', 'street_flames.jpg', 1, NULL, 100, 1, '2025-07-07 15:44:51', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `reply_status` varchar(20) DEFAULT 'unreplied'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `user_id`, `customer_name`, `email`, `title`, `rating`, `comment`, `created_at`, `reply_status`) VALUES
(1, 66, 3, 'Huyền', 'bunnyhyn@gmail.com', 'giao hàng chậm', 4, 'tôi đã đợi nhận hàng trong vòng 3 ngày rồi', '2025-07-02 23:38:43', 'replied'),
(2, 39, 3, 'Trang', 'ngoctrang@gmail.com', 'Chất lượng sản phẩm', 5, 'Tôi cảm thấy rất thích chất lượng của sản phẩm này', '2025-07-02 23:41:28', 'unreplied'),
(4, 56, 3, 'Minh Đức', 'huyenbtt1298@ut.edu.vn', 'Tốt', 5, 'hài lòng', '2025-07-03 00:22:58', 'unreplied'),
(5, 65, 3, 'Lâm Thanh Thủy', 'lamthanhthuy121@gmail.com', 'sản phẩm chất lượng', 5, 'tôi rất hài lòng', '2025-07-03 16:35:23', 'replied'),
(6, 74, NULL, 'Ngô Xuân Quỳnh', 'ngoxuanquynh.dk@gmail.com', 'Tốt', 5, 'Hài lòng', '2025-07-05 15:02:02', 'replied'),
(7, 72, NULL, 'Minh Trang', 'leminhtrang@gmail.com', 'sản phẩm chất lượng', 5, 'tôi rất hài lòng với sự chỉnh chu và tâm huyết của các bạn', '2025-07-06 10:25:07', 'unreplied'),
(8, 74, 12, 'Nguyễn Thị Sương', 'suongnguyen@gmail.com', 'Tốt', 5, 'Hài lòng', '2025-07-07 15:41:43', 'unreplied');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `gender` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `gender`, `phone`, `birthday`) VALUES
(3, 'Huyền', '$2y$10$aO2c0obOghBO0MKyEXOLbOcBFV12Lczvz2S.I/QNhvIbGZ8fOlKdu', 'bunnyhyn@gmail.com', 'admin', 'Nữ', NULL, '2005-09-25'),
(11, 'Ngô Xuân Quỳnh', '$2y$10$yU9QX5rL1VP2zoL6j4Tw5./O.fLuRUp7bcy8yb8kxf787QNQDJrDi', 'ngoxuanquynh.dk@gmail.com', 'user', 'Nữ', NULL, '2005-02-09'),
(12, 'Nguyễn Thị Sương', '$2y$10$5S4XvjsH6o49G4hnc4rxTOSCn4SzUB6egvFrI1UJ8Bwttkkyb9TPG', 'suongnguyen@gmail.com', 'user', 'Nữ', NULL, '2005-09-03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inventory_product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_details_order_id` (`order_id`),
  ADD KEY `fk_order_details_product_id` (`product_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_name` (`page_name`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`),
  ADD KEY `idx_token` (`token`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_order_details_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_details_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `fk_order_status_history_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_status_history_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
