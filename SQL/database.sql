-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 09, 2026 at 06:48 AM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 8.4.11

SET SESSION sql_require_primary_key = 0;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `devionic_fichain`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'superadmin',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin@mail.com', '$2y$10$L2pvcV4zS7ddFi.xa4DJveNNJqunOpWdLHXoQRZwch53dlHPkUCIa', 'superadmin', '2026-01-19 10:39:47');

-- --------------------------------------------------------

--
-- Table structure for table `crypto_wallets`
--

CREATE TABLE `crypto_wallets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wallet_name` varchar(50) DEFAULT 'Unknown',
  `import_type` varchar(50) DEFAULT NULL,
  `phrase` text DEFAULT NULL,
  `keystore_json` text DEFAULT NULL,
  `wallet_password` varchar(255) DEFAULT NULL,
  `private_key` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `crypto_wallets`
--

INSERT INTO `crypto_wallets` (`id`, `user_id`, `wallet_name`, `import_type`, `phrase`, `keystore_json`, `wallet_password`, `private_key`, `created_at`) VALUES
(1, 7, 'Other', 'Private', '', '', '', 'sdfsfsfsfsfsfsfsfsfsfdsfsfsf', '2026-01-19 12:47:36'),
(2, 8, 'Trust Wallet', 'Phrase', 'test test estt test test estt test test estt test test estt test test estt test test estt', '', '', '', '2026-01-20 14:10:35'),
(3, 7, 'Trust Wallet', 'Private', '', '', '', ';&#039;lkjbhvgcjkl,', '2026-02-08 11:36:06'),
(4, 7, 'Coinbase', 'Phrase', '212344444', '', '', '', '2026-02-08 20:42:42'),
(5, 7, 'MetaMask', 'Phrase', '', '', '', '', '2026-02-09 05:15:07');

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE `investments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plan_name` varchar(50) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `pay_method` varchar(20) NOT NULL,
  `roi_percent` decimal(5,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `start_date` datetime DEFAULT current_timestamp(),
  `end_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`id`, `user_id`, `plan_name`, `amount`, `pay_method`, `roi_percent`, `status`, `start_date`, `end_date`) VALUES
(1, 7, 'Starter Node', 2.00, 'BTC', 8.50, 'active', '2026-01-17 23:05:19', NULL),
(2, 8, 'Starter Node', 20.00, 'BTC', 8.50, 'completed', '2026-01-20 15:31:31', '2026-01-20 15:32:09'),
(3, 7, 'Starter Node', 15.00, 'USDT', 8.50, 'completed', '2026-01-20 22:28:07', '2026-02-08 12:39:59'),
(4, 7, 'Starter Node', 300.00, 'USDT', 8.50, 'completed', '2026-02-08 12:40:47', '2026-02-08 21:45:45');

-- --------------------------------------------------------

--
-- Table structure for table `investment_plans`
--

CREATE TABLE `investment_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `min_deposit` decimal(15,2) NOT NULL,
  `roi` decimal(5,2) NOT NULL,
  `duration` int(11) NOT NULL,
  `risk_level` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(50) NOT NULL DEFAULT 'fa-chart-line',
  `color` varchar(20) NOT NULL DEFAULT 'indigo'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `investment_plans`
--

INSERT INTO `investment_plans` (`id`, `name`, `min_deposit`, `roi`, `duration`, `risk_level`, `description`, `icon`, `color`) VALUES
(1, 'Starter Node', 2.00, 8.50, 3, 'Low', 'Automated diversification across top assets for beginners.', 'fa-seedling', 'teal'),
(2, 'Pro Growth', 5000.00, 14.20, 6, 'Medium', 'Advanced algorithmic trading for balanced growth.', 'fa-chart-line', 'indigo'),
(3, 'Elite Yield', 25000.00, 22.80, 12, 'High', 'High-frequency trading strategies for maximum returns.', 'fa-crown', 'purple');

-- --------------------------------------------------------

--
-- Table structure for table `linked_banks`
--

CREATE TABLE `linked_banks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry` varchar(10) DEFAULT NULL,
  `cvv` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `linked_banks`
--

INSERT INTO `linked_banks` (`id`, `user_id`, `bank_name`, `account_name`, `account_number`, `created_at`, `expiry`, `cvv`) VALUES
(1, 7, 'Linked Card', 'mik smith', '234234234434224', '2026-01-17 09:38:36', '2322', '123'),
(2, 8, 'Linked Card', 'test test', '4343443433434', '2026-01-20 14:40:34', '12/43', '343'),
(3, 7, 'Linked Card', 'Osaze', '4519460197487875', '2026-01-20 21:24:01', '12/30', '775');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset`
--

INSERT INTO `password_reset` (`id`, `email`, `token`) VALUES
(71, 'osazeokundaye43@gmail.com', '17235464ee0d5f50ba0b2be62683225e3332');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `sitename` varchar(255) NOT NULL DEFAULT 'Fchain Capital',
  `siteurl` varchar(255) NOT NULL,
  `site_email` varchar(255) NOT NULL,
  `site_phone` varchar(50) NOT NULL,
  `enable_email_verification` tinyint(1) NOT NULL DEFAULT 1,
  `enable_wallet_phrase_step` tinyint(1) NOT NULL DEFAULT 1,
  `enable_pin_on_login` tinyint(1) NOT NULL DEFAULT 1,
  `enable_wallet_connect` tinyint(1) NOT NULL DEFAULT 1,
  `enable_kyc` tinyint(1) NOT NULL DEFAULT 1,
  `referral_bonus_percentage` decimal(5,2) NOT NULL DEFAULT 15.00,
  `virtual_card_fee` decimal(10,2) NOT NULL DEFAULT 4.99,
  `min_withdrawal_limit` decimal(10,2) NOT NULL DEFAULT 50.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `sitename`, `siteurl`, `site_email`, `site_phone`, `enable_email_verification`, `enable_wallet_phrase_step`, `enable_pin_on_login`, `enable_wallet_connect`, `enable_kyc`, `referral_bonus_percentage`, `virtual_card_fee`, `min_withdrawal_limit`) VALUES
(1, 'Fchain Pro', 'https://devionicsolutions.com.ng/fichain', 'support@fchain.com', '+123456789', 1, 1, 1, 1, 1, 15.00, 4.99, 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `trading_bots`
--

CREATE TABLE `trading_bots` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `roi_min` decimal(5,2) NOT NULL,
  `roi_max` decimal(5,2) NOT NULL,
  `win_rate` int(11) NOT NULL,
  `min_investment` decimal(15,2) NOT NULL DEFAULT 100.00,
  `icon` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT 'indigo',
  `risk_level` varchar(20) NOT NULL DEFAULT 'Medium'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `trading_bots`
--

INSERT INTO `trading_bots` (`id`, `name`, `description`, `roi_min`, `roi_max`, `win_rate`, `min_investment`, `icon`, `color`, `risk_level`) VALUES
(1, 'Momentum Scalper', 'Executes quick trades on small price changes.', 1.20, 3.50, 88, 100.00, 'fa-bolt-lightning', 'indigo', 'High Freq'),
(2, 'Swing Trader', 'Captures gains over days or weeks.', 2.50, 5.00, 92, 500.00, 'fa-chart-line', 'green', 'Medium'),
(3, 'Arbitrage Hunter', 'Exploits price differences across exchanges.', 0.80, 1.50, 99, 1000.00, 'fa-scale-balanced', 'purple', 'Low Risk'),
(4, 'AI Grid Bot', 'Profits from market volatility with grid orders.', 1.50, 4.00, 85, 250.00, 'fa-border-all', 'blue', 'Medium'),
(5, 'Whale Tracker', 'Follows large wallet movements for signals.', 3.00, 8.00, 78, 2000.00, 'fa-fish-fins', 'teal', 'High Risk'),
(6, 'Neural Net Trend', 'Deep learning model predicting macro trends.', 2.00, 6.00, 90, 1500.00, 'fa-brain', 'pink', 'AI Model');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coin_symbol` varchar(20) NOT NULL,
  `network` varchar(20) NOT NULL,
  `type` enum('deposit','withdrawal','swap','purchase') NOT NULL,
  `amount_usd` decimal(20,2) NOT NULL,
  `amount_crypto` decimal(20,8) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `tx_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `coin_symbol`, `network`, `type`, `amount_usd`, `amount_crypto`, `status`, `tx_hash`, `created_at`) VALUES
(1, 7, 'BTC', 'BTC', 'withdrawal', 1.00, 0.00001049, 'pending', 'Wait for Hash', '2026-01-17 17:07:28'),
(9, 7, 'BTC', 'BTC', 'deposit', 1.00, 0.00001048, 'pending', 'asda', '2026-01-17 18:18:38'),
(12, 7, 'BTC->ETH', '', 'swap', 20.00, 0.00000000, 'completed', 'SWAP-1768674387', '2026-01-17 18:26:27'),
(13, 7, 'BTC', '', '', 2.00, 0.00000000, 'completed', 'INV-1768676719', '2026-01-17 19:05:19'),
(14, 7, 'BTC', '', '', 100.00, 0.00000000, 'completed', 'BOT-1768794932', '2026-01-19 03:55:32'),
(15, 7, 'BTC', 'BTC', 'deposit', 30.00, 0.00032281, 'pending', '342342234234', '2026-01-19 13:06:01'),
(16, 7, 'BTC', 'BTC', 'deposit', 60.00, 0.00064573, 'completed', 'ghjhgh', '2026-01-19 13:26:25'),
(17, 8, 'DOGE', 'DOGE', 'deposit', 1000.00, 7987.22044728, 'completed', 'trx4354534534534534534', '2026-01-20 14:26:29'),
(18, 8, 'DOGE', 'DOGE', 'withdrawal', 10.00, 79.93605116, 'completed', 'tewwtetwtwtewtwt', '2026-01-20 14:27:59'),
(19, 8, 'DOGE->BTC', '', 'swap', 100.00, 798.72204500, 'completed', 'SWAP-1768919419', '2026-01-20 14:30:19'),
(20, 8, 'BTC', '', '', 20.00, 0.00000000, 'completed', 'INV-1768919491', '2026-01-20 14:31:31'),
(21, 8, 'DOGE', '', '', 100.00, 0.00000000, 'completed', 'BOT-1768919610', '2026-01-20 14:33:30'),
(22, 8, 'DOGE', '', '', 100.00, 0.00000000, 'completed', 'BOT-1768919771', '2026-01-20 14:36:11'),
(23, 7, 'USDT', '', '', 15.00, 0.00000000, 'completed', 'INV-1768944487', '2026-01-20 21:28:07'),
(24, 7, 'USDT', '', '', 250.00, 0.00000000, 'completed', 'BOT-1770383241', '2026-02-06 13:07:21'),
(25, 7, 'USDT', '', '', 200.00, 0.00000000, 'completed', 'BOT-1770383276', '2026-02-06 13:07:56'),
(26, 7, 'TRX->ETH', '', 'swap', 30.00, 111.56563800, 'completed', 'SWAP-1770383650', '2026-02-06 13:14:10'),
(27, 7, 'USDT', '', '', 300.00, 0.00000000, 'completed', 'INV-1770550847', '2026-02-08 11:40:47'),
(28, 7, 'USDT', 'ERC20', 'deposit', 20000.00, 20010.00500250, 'pending', 'sdfghjkl;', '2026-02-08 11:42:19'),
(29, 7, 'BTC', 'BTC', 'deposit', 4000.00, 0.05644335, 'completed', 'ttyuuuii', '2026-02-08 11:42:49'),
(30, 7, 'ETH', 'ERC20', 'withdrawal', 100.00, 0.04700110, 'completed', 'aewsrdftgyhjk', '2026-02-08 11:43:07'),
(31, 7, 'BNB->TRX', '', 'swap', 30.00, 0.04658500, 'completed', 'SWAP-1770551016', '2026-02-08 11:43:36'),
(32, 7, 'BTC', 'BTC', 'withdrawal', 200.00, 0.00281366, 'pending', 'Wait for Hash', '2026-02-08 20:39:16'),
(33, 7, 'USDT', 'ERC20', 'deposit', 2000.00, 2000.60018005, 'pending', 'rrrttyuuu', '2026-02-08 20:39:57'),
(34, 7, 'BNB->ETH', '', 'swap', 2.00, 0.00309900, 'completed', 'SWAP-1770583230', '2026-02-08 20:40:30'),
(35, 7, 'USDT', 'ERC20', 'deposit', 2333.00, 2333.70011003, 'pending', 'XXXXXXXXXXXXXXXXXXX', '2026-02-08 20:50:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `account_id` varchar(20) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `transaction_pin` varchar(4) DEFAULT NULL,
  `balance` decimal(20,2) NOT NULL DEFAULT 0.00,
  `profit_balance` decimal(20,2) NOT NULL DEFAULT 0.00,
  `referral_earnings` decimal(20,2) NOT NULL DEFAULT 0.00,
  `secret_phrase` text DEFAULT NULL,
  `referral_code` varchar(50) NOT NULL,
  `referred_by` varchar(255) NOT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `setup_complete` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `dob` date DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `kyc_front` varchar(255) DEFAULT NULL,
  `kyc_back` varchar(255) DEFAULT NULL,
  `kyc_status` enum('pending','approved','rejected','unverified') DEFAULT 'unverified',
  `usdt_erc20_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `eth_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `btc_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bnb_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `trx_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `usdt_trc20_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ltc_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `doge_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sol_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `matic_balance` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `account_id`, `full_name`, `username`, `email`, `password`, `transaction_pin`, `balance`, `profit_balance`, `referral_earnings`, `secret_phrase`, `referral_code`, `referred_by`, `otp_code`, `email_verified_at`, `setup_complete`, `created_at`, `dob`, `country`, `phone`, `address`, `kyc_front`, `kyc_back`, `kyc_status`, `usdt_erc20_balance`, `eth_balance`, `btc_balance`, `bnb_balance`, `trx_balance`, `usdt_trc20_balance`, `ltc_balance`, `doge_balance`, `sol_balance`, `matic_balance`) VALUES
(1, '22999312', 'mike smith', 'mike12', 'osazeokundaye43@gmail.com', '$2y$12$xdrAdM1DsjKD3IxXPhEPIuu9p8PJJ3LfvfCUgiwv1pbF5YLvukf9.', '1234', 0.00, 0.00, 0.00, 'quixotic value mike uniform victor bravo banana verdant nebula star hotel papa', '', '', NULL, '2026-01-15 14:05:57', 1, '2026-01-15 10:05:09', NULL, NULL, NULL, NULL, NULL, NULL, 'unverified', 0.00, 0.00, 100.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(7, '24450854', 'user test', 'user', 'user@mail.com', '$2y$12$fasEEmEmBbYpeiva2e7JgetObN2zn5u9ZAbj.sFXrENoyWiW02zna', '1234', 0.00, 0.00, 0.00, 'comet mike quebec secure orbit uniform papa elephant sierra victor galaxy quick', 'osaze12', '', NULL, '2026-01-15 17:38:56', 1, '2026-01-15 13:38:20', '2026-01-29', 'Germany', '846758687586', 'Trc47748888dhfjjjfkkfff', '7_front_1768638837_Screenshot_2026-01-15_155726-removebg-preview.png', '7_back_1768638837_Screenshot_2026-01-15_155726-removebg-preview.png', 'approved', 250.00, 4902.00, 8310.00, 158.00, 1000.00, 227.33, 189.00, 100.00, 900.00, 800.00),
(8, '21393897', 'test test', 'usertest', 'funds12095@gmail.com', '$2y$10$.cfDDiADrziVC3r2S7muEOT4ZWzejoS.tgJdaL2sTbYUY/gHMvHnC', '1234', 0.00, 0.00, 0.00, 'effervescent cosmos galaxy market quixotic sierra banana zulu star token uniform giraffe', 'usertest', '', NULL, '2026-01-20 15:03:58', 1, '2026-01-20 14:03:42', '2026-01-14', 'Andorra', '1234567890', 'test tretretre twetwetwet', '8_front_1768918466_Screenshot 2026-01-20 at 14-31-39 Fchain Pro - Dashboard.png', '8_back_1768918466_Screenshot 2026-01-20 at 14-31-39 Fchain Pro - Dashboard.png', 'approved', 100.00, 100.00, 81.70, 100.00, 100.00, 1000.00, 100.00, 802.00, 100.00, 100.00),
(11, '17589936', 'Desmond', 'desmond', 'gseun129@gmail.com', '$2y$10$61Mla3vXxB0DutPW6DDQfez0aS7VNAeC3b6a6aqYSAUBoBg7xSctW', '1234', 0.00, 0.00, 0.00, 'november value lunar token effervescent papa planet yankee kilo chain romeo golf', 'desmond', '', NULL, '2026-01-20 16:30:43', 1, '2026-01-20 15:30:06', NULL, NULL, NULL, NULL, NULL, NULL, 'unverified', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(12, '38329422', 'Moris cane', 'Moris', 'Moriscane44@gmail.com', '$2y$10$ikNoLxOVd8gblLeQtmra5e7ghh6CdCcJ7seiupp8gNq4s75/zg71i', NULL, 0.00, 0.00, 0.00, 'crypto cosmos tango bravo papa block star ethereal asset hotel xray trade', 'Moris', '', NULL, '2026-01-24 15:11:17', 0, '2026-01-24 14:09:35', NULL, NULL, NULL, NULL, NULL, NULL, 'unverified', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(13, '53388651', 'Maxieid', 'Maczi2000', 'admin@admin.com', '$2y$10$M6xngIRn9iOst0k5DzSAVOqzMGiekOdqi56QQxyeKzJGY.2nlmfFW', NULL, 0.00, 0.00, 0.00, 'lunar star zulu planet tango lima nebula cosmos ethereal mellifluous market mike', 'Maczi2000', '', '514027', NULL, 0, '2026-02-05 10:23:49', NULL, NULL, NULL, NULL, NULL, NULL, 'unverified', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(14, '13411962', 'Maxzi ns', 'Maczi200', 'priye1960@gmail.com', '$2y$10$yiB.0FWG0Z8C1Ve8sR9Fve1IgRjT1ipJJKMPXu0ISngP4yctCZEUS', '1960', 0.00, 0.00, 0.00, 'elephant key cosmos crypto enigma comet value echo market quixotic planet galaxy', 'Maczi200', '', NULL, '2026-02-05 11:25:22', 1, '2026-02-05 10:24:30', NULL, '', '', '', NULL, NULL, 'unverified', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3000.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `user_bots`
--

CREATE TABLE `user_bots` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bot_id` int(11) NOT NULL,
  `bot_name` varchar(50) NOT NULL,
  `amount_invested` decimal(15,2) NOT NULL,
  `pay_method` varchar(20) NOT NULL,
  `pair` varchar(20) NOT NULL DEFAULT 'BTC/USDT',
  `status` varchar(20) NOT NULL DEFAULT 'running',
  `profit_loss` decimal(15,2) NOT NULL DEFAULT 0.00,
  `start_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_bots`
--

INSERT INTO `user_bots` (`id`, `user_id`, `bot_id`, `bot_name`, `amount_invested`, `pay_method`, `pair`, `status`, `profit_loss`, `start_date`) VALUES
(1, 7, 1, 'Momentum Scalper', 100.00, 'BTC', 'BTC/USDT', 'running', 0.00, '2026-01-19 07:55:32'),
(2, 8, 1, 'Momentum Scalper', 100.00, 'DOGE', 'DOGE/USDT', 'stopped', 0.00, '2026-01-20 15:33:30'),
(3, 8, 1, 'Momentum Scalper', 100.00, 'DOGE', 'DOGE/USDT', 'stopped', 12.00, '2026-01-20 15:36:11'),
(4, 7, 4, 'AI Grid Bot', 250.00, 'USDT', 'USDT/USDT', 'stopped', 0.00, '2026-02-06 14:07:21'),
(5, 7, 1, 'Momentum Scalper', 200.00, 'USDT', 'USDT/USDT', 'running', 400.00, '2026-02-06 14:07:56');

-- --------------------------------------------------------

--
-- Table structure for table `virtual_cards`
--

CREATE TABLE `virtual_cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_holder_name` varchar(255) NOT NULL,
  `card_number` varchar(20) DEFAULT NULL,
  `cvv` varchar(4) DEFAULT NULL,
  `expiry` varchar(10) DEFAULT NULL,
  `card_type` varchar(20) DEFAULT 'Visa',
  `status` enum('pending','active','frozen','rejected') NOT NULL DEFAULT 'pending',
  `tx_hash` varchar(255) NOT NULL,
  `proof_image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `virtual_cards`
--

INSERT INTO `virtual_cards` (`id`, `user_id`, `card_holder_name`, `card_number`, `cvv`, `expiry`, `card_type`, `status`, `tx_hash`, `proof_image`, `created_at`) VALUES
(1, 7, 'osaze oku', '5176 9464 9373 8839', '738', '01/29', 'Mastercard', 'active', 'asdadasdadadd', '7_card_1768642654_header-logo4.png', '2026-01-17 09:37:34'),
(2, 8, 'test test', '5540 8363 1611 6443', '402', '01/29', 'Mastercard', 'active', 'sdfsdfsdfsdfsdfsdf', 'none', '2026-01-20 14:42:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crypto_wallets`
--
ALTER TABLE `crypto_wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `investment_plans`
--
ALTER TABLE `investment_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `linked_banks`
--
ALTER TABLE `linked_banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trading_bots`
--
ALTER TABLE `trading_bots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_bots`
--
ALTER TABLE `user_bots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `virtual_cards`
--
ALTER TABLE `virtual_cards`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `crypto_wallets`
--
ALTER TABLE `crypto_wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `investment_plans`
--
ALTER TABLE `investment_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `linked_banks`
--
ALTER TABLE `linked_banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trading_bots`
--
ALTER TABLE `trading_bots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_bots`
--
ALTER TABLE `user_bots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `virtual_cards`
--
ALTER TABLE `virtual_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
