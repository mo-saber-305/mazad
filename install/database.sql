-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2022 at 04:21 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `viserlab_bidwin`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `username`, `email_verified_at`, `image`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@site.com', 'admin', NULL, '62209217f0ef11646301719.jpg', '$2y$10$2qcOUKrDIUqyyCklvHp7IO8fGNcJ1gAXtxouTn1isZPHu6H8CfHPq', NULL, '2022-03-03 10:02:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `merchant_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_status` tinyint(1) NOT NULL DEFAULT 0,
  `click_url` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_password_resets`
--

CREATE TABLE `admin_password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

CREATE TABLE `advertisements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `size` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect_url` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `impression` int(11) NOT NULL DEFAULT 0,
  `click` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bids`
--

CREATE TABLE `bids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: Inactive, 1: Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `method_code` int(10) UNSIGNED NOT NULL,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `method_currency` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `final_amo` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `detail` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btc_amo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `btc_wallet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trx` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `try` int(10) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>success, 2=>pending, 3=>cancel',
  `from_api` tinyint(1) NOT NULL DEFAULT 0,
  `admin_feedback` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `merchant_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `mail_sender` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_from` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_to` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_sms_templates`
--

CREATE TABLE `email_sms_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `act` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subj` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_body` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_body` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shortcodes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_status` tinyint(1) NOT NULL DEFAULT 1,
  `sms_status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_sms_templates`
--

INSERT INTO `email_sms_templates` (`id`, `act`, `name`, `subj`, `email_body`, `sms_body`, `shortcodes`, `email_status`, `sms_status`, `created_at`, `updated_at`) VALUES
(1, 'PASS_RESET_CODE', 'Password Reset', 'Password Reset', '<div>We have received a request to reset the password for your account on <b>{{time}} .<br></b></div><div>Requested From IP: <b>{{ip}}</b> using <b>{{browser}}</b> on <b>{{operating_system}} </b>.</div><div><br></div><br><div><div><div>Your account recovery code is:&nbsp;&nbsp; <font size=\"6\"><b>{{code}}</b></font></div><div><br></div></div></div><div><br></div><div><font size=\"4\" color=\"#CC0000\">If you do not wish to reset your password, please disregard this message.&nbsp;</font><br></div><br>', 'Your account recovery code is: {{code}}', ' {\"code\":\"Password Reset Code\",\"ip\":\"IP of User\",\"browser\":\"Browser of User\",\"operating_system\":\"Operating System of User\",\"time\":\"Request Time\"}', 1, 1, '2019-09-24 23:04:05', '2021-01-06 00:49:06'),
(2, 'PASS_RESET_DONE', 'Password Reset Confirmation', 'You have Reset your password', '<div><p>\r\n    You have successfully reset your password.</p><p>You changed from&nbsp; IP: <b>{{ip}}</b> using <b>{{browser}}</b> on <b>{{operating_system}}&nbsp;</b> on <b>{{time}}</b></p><p><b><br></b></p><p><font color=\"#FF0000\"><b>If you did not changed that, Please contact with us as soon as possible.</b></font><br></p></div>', 'Your password has been changed successfully', '{\"ip\":\"IP of User\",\"browser\":\"Browser of User\",\"operating_system\":\"Operating System of User\",\"time\":\"Request Time\"}', 1, 1, '2019-09-24 23:04:05', '2020-03-07 10:23:47'),
(3, 'EVER_CODE', 'Email Verification', 'Please verify your email address', '<div><br></div><div>Thanks For join with us. <br></div><div>Please use below code to verify your email address.<br></div><div><br></div><div>Your email verification code is:<font size=\"6\"><b> {{code}}</b></font></div>', 'Your email verification code is: {{code}}', '{\"code\":\"Verification code\"}', 1, 1, '2019-09-24 23:04:05', '2021-01-03 23:35:10'),
(4, 'SVER_CODE', 'SMS Verification ', 'Please verify your phone', 'Your phone verification code is: {{code}}', 'Your phone verification code is: {{code}}', '{\"code\":\"Verification code\"}', 0, 1, '2019-09-24 23:04:05', '2020-03-08 01:28:52'),
(5, '2FA_ENABLE', 'Google Two Factor - Enable', 'Google Two Factor Authentication is now  Enabled for Your Account', '<div>You just enabled Google Two Factor Authentication for Your Account.</div><div><br></div><div>Enabled at <b>{{time}} </b>From IP: <b>{{ip}}</b> using <b>{{browser}}</b> on <b>{{operating_system}} </b>.</div>', 'Your verification code is: {{code}}', '{\"ip\":\"IP of User\",\"browser\":\"Browser of User\",\"operating_system\":\"Operating System of User\",\"time\":\"Request Time\"}', 1, 1, '2019-09-24 23:04:05', '2020-03-08 01:42:59'),
(6, '2FA_DISABLE', 'Google Two Factor Disable', 'Google Two Factor Authentication is now  Disabled for Your Account', '<div>You just Disabled Google Two Factor Authentication for Your Account.</div><div><br></div><div>Disabled at <b>{{time}} </b>From IP: <b>{{ip}}</b> using <b>{{browser}}</b> on <b>{{operating_system}} </b>.</div>', 'Google two factor verification is disabled', '{\"ip\":\"IP of User\",\"browser\":\"Browser of User\",\"operating_system\":\"Operating System of User\",\"time\":\"Request Time\"}', 1, 1, '2019-09-24 23:04:05', '2020-03-08 01:43:46'),
(16, 'ADMIN_SUPPORT_REPLY', 'Support Ticket Reply ', 'Reply Support Ticket', '<div><p><span style=\"font-size: 11pt;\" data-mce-style=\"font-size: 11pt;\"><strong>A member from our support team has replied to the following ticket:</strong></span></p><p><b><span style=\"font-size: 11pt;\" data-mce-style=\"font-size: 11pt;\"><strong><br></strong></span></b></p><p><b>[Ticket#{{ticket_id}}] {{ticket_subject}}<br><br>Click here to reply:&nbsp; {{link}}</b></p><p>----------------------------------------------</p><p>Here is the reply : <br></p><p> {{reply}}<br></p></div><div><br></div>', '{{subject}}\r\n\r\n{{reply}}\r\n\r\n\r\nClick here to reply:  {{link}}', '{\"ticket_id\":\"Support Ticket ID\", \"ticket_subject\":\"Subject Of Support Ticket\", \"reply\":\"Reply from Staff/Admin\",\"link\":\"Ticket URL For relpy\"}', 1, 1, '2020-06-08 18:00:00', '2020-05-04 02:24:40'),
(206, 'DEPOSIT_COMPLETE', 'Automated Deposit - Successful', 'Deposit Completed Successfully', '<div>Your deposit of <b>{{amount}} {{currency}}</b> is via&nbsp; <b>{{method_name}} </b>has been completed Successfully.<b><br></b></div><div><b><br></b></div><div><b>Details of your Deposit :<br></b></div><div><br></div><div>Amount : {{amount}} {{currency}}</div><div>Charge: <font color=\"#000000\">{{charge}} {{currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{currency}} = {{rate}} {{method_currency}}</div><div>Payable : {{method_amount}} {{method_currency}} <br></div><div>Paid via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><font size=\"5\"><b><br></b></font></div><div><font size=\"5\">Your current Balance is <b>{{post_balance}} {{currency}}</b></font></div><div><br></div><div><br><br><br></div>', '{{amount}} {{currrency}} Deposit successfully by {{gateway_name}}', '{\"trx\":\"Transaction Number\",\"amount\":\"Request Amount By user\",\"charge\":\"Gateway Charge\",\"currency\":\"Site Currency\",\"rate\":\"Conversion Rate\",\"method_name\":\"Deposit Method Name\",\"method_currency\":\"Deposit Method Currency\",\"method_amount\":\"Deposit Method Amount After Conversion\", \"post_balance\":\"Users Balance After this operation\"}', 1, 1, '2020-06-24 18:00:00', '2020-11-17 03:10:00'),
(207, 'DEPOSIT_REQUEST', 'Manual Deposit - User Requested', 'Deposit Request Submitted Successfully', '<div>Your deposit request of <b>{{amount}} {{currency}}</b> is via&nbsp; <b>{{method_name}} </b>submitted successfully<b> .<br></b></div><div><b><br></b></div><div><b>Details of your Deposit :<br></b></div><div><br></div><div>Amount : {{amount}} {{currency}}</div><div>Charge: <font color=\"#FF0000\">{{charge}} {{currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{currency}} = {{rate}} {{method_currency}}</div><div>Payable : {{method_amount}} {{method_currency}} <br></div><div>Pay via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div><br></div>', '{{amount}} Deposit requested by {{method}}. Charge: {{charge}} . Trx: {{trx}}\r\n', '{\"trx\":\"Transaction Number\",\"amount\":\"Request Amount By user\",\"charge\":\"Gateway Charge\",\"currency\":\"Site Currency\",\"rate\":\"Conversion Rate\",\"method_name\":\"Deposit Method Name\",\"method_currency\":\"Deposit Method Currency\",\"method_amount\":\"Deposit Method Amount After Conversion\"}', 1, 1, '2020-05-31 18:00:00', '2020-06-01 18:00:00'),
(208, 'DEPOSIT_APPROVE', 'Manual Deposit - Admin Approved', 'Your Deposit is Approved', '<div>Your deposit request of <b>{{amount}} {{currency}}</b> is via&nbsp; <b>{{method_name}} </b>is Approved .<b><br></b></div><div><b><br></b></div><div><b>Details of your Deposit :<br></b></div><div><br></div><div>Amount : {{amount}} {{currency}}</div><div>Charge: <font color=\"#FF0000\">{{charge}} {{currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{currency}} = {{rate}} {{method_currency}}</div><div>Payable : {{method_amount}} {{method_currency}} <br></div><div>Paid via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><font size=\"5\"><b><br></b></font></div><div><font size=\"5\">Your current Balance is <b>{{post_balance}} {{currency}}</b></font></div><div><br></div><div><br><br></div>', 'Admin Approve Your {{amount}} {{gateway_currency}} payment request by {{gateway_name}} transaction : {{transaction}}', '{\"trx\":\"Transaction Number\",\"amount\":\"Request Amount By user\",\"charge\":\"Gateway Charge\",\"currency\":\"Site Currency\",\"rate\":\"Conversion Rate\",\"method_name\":\"Deposit Method Name\",\"method_currency\":\"Deposit Method Currency\",\"method_amount\":\"Deposit Method Amount After Conversion\", \"post_balance\":\"Users Balance After this operation\"}', 1, 1, '2020-06-16 18:00:00', '2020-06-14 18:00:00'),
(209, 'DEPOSIT_REJECT', 'Manual Deposit - Admin Rejected', 'Your Deposit Request is Rejected', '<div>Your deposit request of <b>{{amount}} {{currency}}</b> is via&nbsp; <b>{{method_name}} has been rejected</b>.<b><br></b></div><br><div>Transaction Number was : {{trx}}</div><div><br></div><div>if you have any query, feel free to contact us.<br></div><br><div><br><br></div>\r\n\r\n\r\n\r\n{{rejection_message}}', 'Admin Rejected Your {{amount}} {{gateway_currency}} payment request by {{gateway_name}}\r\n\r\n{{rejection_message}}', '{\"trx\":\"Transaction Number\",\"amount\":\"Request Amount By user\",\"charge\":\"Gateway Charge\",\"currency\":\"Site Currency\",\"rate\":\"Conversion Rate\",\"method_name\":\"Deposit Method Name\",\"method_currency\":\"Deposit Method Currency\",\"method_amount\":\"Deposit Method Amount After Conversion\",\"rejection_message\":\"Rejection message\"}', 1, 1, '2020-06-09 18:00:00', '2020-06-14 18:00:00'),
(210, 'WITHDRAW_REQUEST', 'Withdraw  - User Requested', 'Withdraw Request Submitted Successfully', '<div>Your withdraw request of <b>{{amount}} {{currency}}</b>&nbsp; via&nbsp; <b>{{method_name}} </b>has been submitted Successfully.<b><br></b></div><div><b><br></b></div><div><b>Details of your withdraw:<br></b></div><div><br></div><div>Amount : {{amount}} {{currency}}</div><div>Charge: <font color=\"#FF0000\">{{charge}} {{currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{currency}} = {{rate}} {{method_currency}}</div><div>You will get: {{method_amount}} {{method_currency}} <br></div><div>Via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><font size=\"4\" color=\"#FF0000\"><b><br></b></font></div><div><font size=\"4\" color=\"#FF0000\"><b>This may take {{delay}} to process the payment.</b></font><br></div><div><font size=\"5\"><b><br></b></font></div><div><font size=\"5\"><b><br></b></font></div><div><font size=\"5\">Your current Balance is <b>{{post_balance}} {{currency}}</b></font></div><div><br></div><div><br><br><br><br></div>', '{{amount}} {{currency}} withdraw requested by {{method_name}}. You will get {{method_amount}} {{method_currency}} in {{delay}}. Trx: {{trx}}', '{\"trx\":\"Transaction Number\",\"amount\":\"Request Amount By user\",\"charge\":\"Gateway Charge\",\"currency\":\"Site Currency\",\"rate\":\"Conversion Rate\",\"method_name\":\"Deposit Method Name\",\"method_currency\":\"Deposit Method Currency\",\"method_amount\":\"Deposit Method Amount After Conversion\", \"post_balance\":\"Users Balance After this operation\", \"delay\":\"Delay time for processing\"}', 1, 1, '2020-06-07 18:00:00', '2021-05-08 06:49:06'),
(211, 'WITHDRAW_REJECT', 'Withdraw - Admin Rejected', 'Withdraw Request has been Rejected and your money is refunded to your account', '<div>Your withdraw request of <b>{{amount}} {{currency}}</b>&nbsp; via&nbsp; <b>{{method_name}} </b>has been Rejected.<b><br></b></div><div><b><br></b></div><div><b>Details of your withdraw:<br></b></div><div><br></div><div>Amount : {{amount}} {{currency}}</div><div>Charge: <font color=\"#FF0000\">{{charge}} {{currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{currency}} = {{rate}} {{method_currency}}</div><div>You should get: {{method_amount}} {{method_currency}} <br></div><div>Via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div><br></div><div>----</div><div><font size=\"3\"><br></font></div><div><font size=\"3\"> {{amount}} {{currency}} has been <b>refunded </b>to your account and your current Balance is <b>{{post_balance}}</b><b> {{currency}}</b></font></div><div><br></div><div>-----</div><div><br></div><div><font size=\"4\">Details of Rejection :</font></div><div><font size=\"4\"><b>{{admin_details}}</b></font></div><div><br></div><div><br><br><br><br><br><br></div>', 'Admin Rejected Your {{amount}} {{currency}} withdraw request. Your Main Balance {{main_balance}}  {{method}} , Transaction {{transaction}}', '{\"trx\":\"Transaction Number\",\"amount\":\"Request Amount By user\",\"charge\":\"Gateway Charge\",\"currency\":\"Site Currency\",\"rate\":\"Conversion Rate\",\"method_name\":\"Deposit Method Name\",\"method_currency\":\"Deposit Method Currency\",\"method_amount\":\"Deposit Method Amount After Conversion\", \"post_balance\":\"Users Balance After this operation\", \"admin_details\":\"Details Provided By Admin\"}', 1, 1, '2020-06-09 18:00:00', '2020-06-14 18:00:00'),
(212, 'WITHDRAW_APPROVE', 'Withdraw - Admin  Approved', 'Withdraw Request has been Processed and your money is sent', '<div>Your withdraw request of <b>{{amount}} {{currency}}</b>&nbsp; via&nbsp; <b>{{method_name}} </b>has been Processed Successfully.<b><br></b></div><div><b><br></b></div><div><b>Details of your withdraw:<br></b></div><div><br></div><div>Amount : {{amount}} {{currency}}</div><div>Charge: <font color=\"#FF0000\">{{charge}} {{currency}}</font></div><div><br></div><div>Conversion Rate : 1 {{currency}} = {{rate}} {{method_currency}}</div><div>You will get: {{method_amount}} {{method_currency}} <br></div><div>Via :&nbsp; {{method_name}}</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div><div>-----</div><div><br></div><div><font size=\"4\">Details of Processed Payment :</font></div><div><font size=\"4\"><b>{{admin_details}}</b></font></div><div><br></div><div><br><br><br><br><br></div>', 'Admin Approve Your {{amount}} {{currency}} withdraw request by {{method}}. Transaction {{transaction}}', '{\"trx\":\"Transaction Number\",\"amount\":\"Request Amount By user\",\"charge\":\"Gateway Charge\",\"currency\":\"Site Currency\",\"rate\":\"Conversion Rate\",\"method_name\":\"Deposit Method Name\",\"method_currency\":\"Deposit Method Currency\",\"method_amount\":\"Deposit Method Amount After Conversion\", \"admin_details\":\"Details Provided By Admin\"}', 1, 1, '2020-06-10 18:00:00', '2020-06-06 18:00:00'),
(215, 'BAL_ADD', 'Balance Add by Admin', 'Your Account has been Credited', '<div>{{amount}} {{currency}} has been added to your account .</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div>Your Current Balance is : <font size=\"3\"><b>{{post_balance}}&nbsp; {{currency}}&nbsp;</b></font>', '{{amount}} {{currency}} credited in your account. Your Current Balance {{remaining_balance}} {{currency}} . Transaction: #{{trx}}', '{\"trx\":\"Transaction Number\",\"amount\":\"Request Amount By Admin\",\"currency\":\"Site Currency\", \"post_balance\":\"Users Balance After this operation\"}', 1, 1, '2019-09-14 19:14:22', '2021-01-06 00:46:18'),
(216, 'BAL_SUB', 'Balance Subtracted by Admin', 'Your Account has been Debited', '<div>{{amount}} {{currency}} has been subtracted from your account .</div><div><br></div><div>Transaction Number : {{trx}}</div><div><br></div>Your Current Balance is : <font size=\"3\"><b>{{post_balance}}&nbsp; {{currency}}</b></font>', '{{amount}} {{currency}} debited from your account. Your Current Balance {{remaining_balance}} {{currency}} . Transaction: #{{trx}}', '{\"trx\":\"Transaction Number\",\"amount\":\"Request Amount By Admin\",\"currency\":\"Site Currency\", \"post_balance\":\"Users Balance After this operation\"}', 1, 1, '2019-09-14 19:14:22', '2019-11-10 09:07:12'),
(217, 'BID_COMPLETE', 'Product Bid - Successful', 'Your Product has been Bided Successfully', '<div>Your product has been bided by <b>{{amount}} {{currency}}</b> successfully.<br></div>\r\n<div><b><br></b></div>\r\n<div><b>Details of your Bid :<br></b></div>\r\n<div><br></div>\r\n<div>Product: {{product}}</div>\r\n<div>Product price : {{product_price}} {{currency}}</div>\r\n<div>Bid price : {{amount}} {{currency}}</div>\r\n\r\n<div><br></div>\r\n\r\n<div>Transaction Number : {{trx}}</div>\r\n<div>\r\n    <font size=\"5\"><b><br></b></font>\r\n</div>\r\n<div>\r\n    <font size=\"5\">Your current Balance is <b>{{ post_balance }} {{ currency }}</b></font>\r\n</div>\r\n<div><br></div>\r\n<div><br><br><br></div>', '{{amount}} {{currrency}} Bid successfully', '{\r\n    \"trx\": \"Transaction Number\",\r\n    \"amount\": \"Request Amount By user\",\r\n    \"currency\": \"Site Currency\",\r\n    \"product\": \"Your Product Name\",\r\n    \"product_price\": \"Product Price\",\r\n    \"post_balance\": \"Users Balance After this operation\"\r\n}', 1, 1, '2020-06-24 18:00:00', '2022-02-19 10:54:11'),
(218, 'BID_WINNER', 'Bid Winner', 'You have won the bid', '<div>Congratulations. You have won the <b>{{product}}</b></div>\r\n<div><b><br></b></div>\r\n<div><b>Details of your Winning Bid :<br></b></div>\r\n<div><br></div>\r\n<div>Product: {{product}}</div>\r\n<div>Product price : {{product_price}} {{currency}}</div>\r\n<div>Bid price : {{amount}} {{currency}}</div>\r\n<div><br></div><div><br></div>\r\n<div><br></div>\r\n<div><br><br><br></div>', 'You have won  the {{product}}', '{\r\n    \"product\": \"Your Product Name\",\r\n    \"product_price\": \"Product Price\",\r\n    \"currency\": \"Site Currency\",\r\n    \"amount\": \"Request Amount By user\"\r\n}', 1, 1, '2020-06-24 18:00:00', '2022-02-24 07:03:59');

-- --------------------------------------------------------

--
-- Table structure for table `extensions`
--

CREATE TABLE `extensions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `act` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `script` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shortcode` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'object',
  `support` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'help section',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `extensions`
--

INSERT INTO `extensions` (`id`, `act`, `name`, `description`, `image`, `script`, `shortcode`, `support`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'tawk-chat', 'Tawk.to', 'Key location is shown bellow', 'tawky_big.png', '<script>\r\n                        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();\r\n                        (function(){\r\n                        var s1=document.createElement(\"script\"),s0=document.getElementsByTagName(\"script\")[0];\r\n                        s1.async=true;\r\n                        s1.src=\"https://embed.tawk.to/{{app_key}}\";\r\n                        s1.charset=\"UTF-8\";\r\n                        s1.setAttribute(\"crossorigin\",\"*\");\r\n                        s0.parentNode.insertBefore(s1,s0);\r\n                        })();\r\n                    </script>', '{\"app_key\":{\"title\":\"App Key\",\"value\":\"------\"}}', 'twak.png', 0, NULL, '2019-10-18 23:16:05', '2021-05-18 05:37:12'),
(2, 'google-recaptcha2', 'Google Recaptcha 2', 'Key location is shown bellow', 'recaptcha3.png', '\r\n<script src=\"https://www.google.com/recaptcha/api.js\"></script>\r\n<div class=\"g-recaptcha\" data-sitekey=\"{{sitekey}}\" data-callback=\"verifyCaptcha\"></div>\r\n<div id=\"g-recaptcha-error\"></div>', '{\"sitekey\":{\"title\":\"Site Key\",\"value\":\"6Lfpm3cUAAAAAGIjbEJKhJNKS4X1Gns9ANjh8MfH\"}}', 'recaptcha.png', 0, NULL, '2019-10-18 23:16:05', '2022-03-07 11:22:07'),
(3, 'custom-captcha', 'Custom Captcha', 'Just Put Any Random String', 'customcaptcha.png', NULL, '{\"random_key\":{\"title\":\"Random String\",\"value\":\"SecureString\"}}', 'na', 0, NULL, '2019-10-18 23:16:05', '2022-03-07 12:41:18'),
(4, 'google-analytics', 'Google Analytics', 'Key location is shown bellow', 'google_analytics.png', '<script async src=\"https://www.googletagmanager.com/gtag/js?id={{app_key}}\"></script>\r\n                <script>\r\n                  window.dataLayer = window.dataLayer || [];\r\n                  function gtag(){dataLayer.push(arguments);}\r\n                  gtag(\"js\", new Date());\r\n                \r\n                  gtag(\"config\", \"{{app_key}}\");\r\n                </script>', '{\"app_key\":{\"title\":\"App Key\",\"value\":\"------\"}}', 'ganalytics.png', 0, NULL, NULL, '2021-05-04 10:19:12'),
(5, 'fb-comment', 'Facebook Comment ', 'Key location is shown bellow', 'Facebook.png', '<div id=\"fb-root\"></div><script async defer crossorigin=\"anonymous\" src=\"https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v4.0&appId={{app_key}}&autoLogAppEvents=1\"></script>', '{\"app_key\":{\"title\":\"App Key\",\"value\":\"----\"}}', 'fb_com.PNG', 0, NULL, NULL, '2022-03-05 11:48:01');

-- --------------------------------------------------------

--
-- Table structure for table `frontends`
--

CREATE TABLE `frontends` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data_keys` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_values` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `frontends`
--

INSERT INTO `frontends` (`id`, `data_keys`, `data_values`, `created_at`, `updated_at`) VALUES
(1, 'seo.data', '{\"seo_image\":\"1\",\"keywords\":[\"bid\",\"auction\",\"bidding\",\"bidding platform\",\"auction bidding\",\"product bidding\"],\"description\":\"ViserBid is a multivendor auction bidding platform for your product.\",\"social_title\":\"ViserBId - Multivendor Auction Bidding Platform\",\"social_description\":\"ViserBid is a multivendor auction bidding platform for your product.\",\"image\":\"62261df3490761646665203.png\"}', '2020-07-04 23:42:52', '2022-03-07 09:00:03'),
(24, 'about.content', '{\"has_image\":\"1\",\"heading\":\"Know About US\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\",\"description\":\"Libero exercitationem fugit dignissimos facilis suscipit voluptatibus error consectetur amet sed necessitatibus in dolor, ut non. Temporibus, maiores? Reprehenderit quae inventore obcaecati! Libero exercitationem fugit dignissimos facilis suscipit voluptatibus error consectetur amet sed necessitatibus in dolor, ut non. Temporibus, maiores? Reprehenderit quae inventore obcaecati!\",\"video_url\":\"https:\\/\\/www.youtube.com\\/watch?v=WOb4cj7izpE\",\"about_image\":\"6212259ed1dbb1645356446.jpg\"}', '2020-10-28 00:51:20', '2022-02-20 11:00:11'),
(25, 'blog.content', '{\"heading\":\"Recent Posts\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\"}', '2020-10-28 00:51:34', '2022-02-26 05:54:57'),
(26, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"In the same way that starting to read more can make you a better writer\",\"description_nic\":\"<p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile.digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile..<\\/p><blockquote class=\\\"mb-3 d-block\\\" style=\\\"background:rgba(1,28,61,0.9);padding:25px 20px;border-left:2px solid rgba(1,28,61,0.9);font-style:italic;color:rgb(164,189,206);font-family:Nunito, sans-serif;\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impnetworks and other enhancements to ensure fast loading times for both desktop and mobile.<p class=\\\"m-0\\\" style=\\\"margin-bottom:25px;font-size:20px;font-weight:600;margin-top:12px;\\\">--- John Doe<\\/p><\\/blockquote><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p>\",\"blog_image\":\"6219c1f7553b71645855223.jpg\"}', '2020-10-28 00:57:19', '2022-02-26 06:00:23'),
(27, 'contact_us.content', '{\"title\":\"Get In Touch With Us\",\"short_details\":\"Dolor sit amet, consectetur adipiscing elit. Et luctus nisl volutpat arcu nibh blandit.\",\"email_address\":\"contact@demo.com\",\"contact_details\":\"4553 Woodville Hwy, New York, USA\",\"contact_number\":\"(123) 456 - 7890\"}', '2020-10-28 00:59:19', '2022-03-03 10:12:37'),
(28, 'counter.content', '{\"heading\":\"Latest News\",\"subheading\":\"Register New Account\"}', '2020-10-28 01:04:02', '2020-10-28 01:04:02'),
(30, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Maddison will learn from casino mistake\",\"description_nic\":\"<p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile.digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile..<\\/p><blockquote class=\\\"mb-3 d-block\\\" style=\\\"background:rgba(1,28,61,0.9);padding:25px 20px;border-left:2px solid rgba(1,28,61,0.9);font-style:italic;color:rgb(164,189,206);font-family:Nunito, sans-serif;\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impnetworks and other enhancements to ensure fast loading times for both desktop and mobile.<p class=\\\"m-0\\\" style=\\\"margin-bottom:25px;font-size:20px;font-weight:600;margin-top:12px;\\\">--- John Doe<\\/p><\\/blockquote><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p>\",\"blog_image\":\"6219c1edf2da01645855213.jpg\"}', '2020-10-31 00:39:05', '2022-02-26 06:00:14'),
(31, 'social_icon.element', '{\"title\":\"Facebook\",\"social_icon\":\"<i class=\\\"las la-expand\\\"><\\/i>\",\"url\":\"https:\\/\\/www.google.com\\/\"}', '2020-11-12 04:07:30', '2021-05-12 05:56:59'),
(33, 'feature.content', '{\"heading\":\"asdf\",\"subheading\":\"asdf\"}', '2021-01-03 23:40:54', '2021-01-03 23:40:55'),
(34, 'feature.element', '{\"title\":\"24\\/7 Online Support\",\"feature_icon\":\"<i class=\\\"las la-headset\\\"><\\/i>\"}', '2021-01-03 23:41:02', '2022-02-01 08:18:01'),
(35, 'service.element', '{\"trx_type\":\"withdraw\",\"service_icon\":\"<i class=\\\"las la-highlighter\\\"><\\/i>\",\"title\":\"asdfasdf\",\"description\":\"asdfasdfasdfasdf\"}', '2021-03-06 01:12:10', '2021-03-06 01:12:10'),
(36, 'service.content', '{\"trx_type\":\"withdraw\",\"heading\":\"asdf fffff\",\"subheading\":\"asdf asdfasdf\"}', '2021-03-06 01:27:34', '2021-03-06 02:19:39'),
(39, 'banner.content', '{\"has_image\":\"1\",\"heading\":\"Multivendor Auction Bidding Platform\",\"subheading\":\"Dolor sit amet consectetur adipisicing elit. Eligendi sit commodi ex, id recusandae rerum quae optio quaerat totam consequuntur ad illo ducimus magnam nulla.\",\"button\":\"Become a user\",\"button_url\":\"register\",\"link\":\"Become a Merchant\",\"link_url\":\"merchant\\/register\",\"background_image\":\"61f8f23878ae61643704888.jpg\"}', '2021-05-02 06:09:30', '2022-03-07 13:58:15'),
(41, 'cookie.data', '{\"link\":\"#\",\"description\":\"<font color=\\\"#000\\\" face=\\\"Exo, sans-serif\\\"><span style=\\\"font-size: 18px;\\\">We may use cookies or any other tracking technologies when you visit our website, including any other media form, mobile website, or mobile application related or connected to help customize the Site and improve your experience.<\\/span><\\/font><br>\",\"status\":1}', '2020-07-04 23:42:52', '2022-03-03 10:26:19'),
(42, 'policy_pages.element', '{\"title\":\"Privacy Policy\",\"details\":\"<div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\">\\r\\n    <h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:#a4bdce;\\\">What\\r\\n        information do we collect?<\\/h3>\\r\\n    <p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We gather data from you when you\\r\\n        register on our site, submit a request, buy any services, react to an overview, or round out a structure. At the\\r\\n        point when requesting any assistance or enrolling on our site, as suitable, you might be approached to enter\\r\\n        your: name, email address, or telephone number. You may, nonetheless, visit our site anonymously.<\\/p>\\r\\n<\\/div>\\r\\n<div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\">\\r\\n    <h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:#a4bdce;\\\">How do\\r\\n        we protect your information?<\\/h3>\\r\\n    <p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">All provided delicate\\/credit data is\\r\\n        sent through Stripe.<br \\/>After an exchange, your private data (credit cards, social security numbers, financials,\\r\\n        and so on) won\'t be put away on our workers.<\\/p>\\r\\n<\\/div>\\r\\n<div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\">\\r\\n    <h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:#a4bdce;\\\">Do we\\r\\n        disclose any information to outside parties?<\\/h3>\\r\\n    <p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t sell, exchange, or in any case\\r\\n        move to outside gatherings by and by recognizable data. This does exclude confided in outsiders who help us in\\r\\n        working our site, leading our business, or adjusting you, since those gatherings consent to keep this data\\r\\n        private. We may likewise deliver your data when we accept discharge is suitable to follow the law, implement our\\r\\n        site strategies, or ensure our own or others\' rights, property, or wellbeing.<\\/p>\\r\\n<\\/div>\\r\\n<div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\">\\r\\n    <h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:#a4bdce;\\\">\\r\\n        Children\'s Online Privacy Protection Act Compliance<\\/h3>\\r\\n    <p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We are consistent with the prerequisites\\r\\n        of COPPA (Children\'s Online Privacy Protection Act), we don\'t gather any data from anybody under 13 years old.\\r\\n        Our site, items, and administrations are completely coordinated to individuals who are in any event 13 years of\\r\\n        age or more established.<\\/p>\\r\\n<\\/div>\\r\\n<div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\">\\r\\n    <h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:#a4bdce;\\\">Changes\\r\\n        to our Privacy Policy<\\/h3>\\r\\n    <p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">If we decide to change our privacy\\r\\n        policy, we will post those changes on this page.<\\/p>\\r\\n<\\/div>\\r\\n<div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\">\\r\\n    <h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:#a4bdce;\\\">How long\\r\\n        we retain your information?<\\/h3>\\r\\n    <p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">At the point when you register for our\\r\\n        site, we cycle and keep your information we have about you however long you don\'t erase the record or withdraw\\r\\n        yourself (subject to laws and guidelines).<\\/p>\\r\\n<\\/div>\\r\\n<div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\">\\r\\n    <h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;color:#a4bdce;\\\">What we\\r\\n        don\\u2019t do with your data<\\/h3>\\r\\n    <p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t and will never share, unveil,\\r\\n        sell, or in any case give your information to different organizations for the promoting of their items or\\r\\n        administrations.<\\/p>\\r\\n<\\/div>\"}', '2021-06-09 08:50:42', '2022-03-03 09:35:27'),
(43, 'policy_pages.element', '{\"title\":\"Terms of Service\",\"details\":\"<div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We claim all authority to dismiss,\\r\\nend,\\r\\nor handicap any help with or without cause per administrator discretion. This is a Complete independent facilitating,\\r\\non the off chance that you misuse our ticket or Livechat or emotionally supportive network by submitting solicitations or protests we will impair your record. The solitary time you should reach us about the seaward facilitating is if there is an issue with the worker. We have not many substance limitations and everything is as per laws and guidelines. Try not to join on the off chance that you intend to do anything contrary to the guidelines,\\r\\nwe do check these things and we will know,\\r\\ndon\'t burn through our own and your time by joining on the off chance that you figure you will have the option to sneak by us and break the terms.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><ul class=\\\"font-18\\\" style=\\\"padding-left:15px;list-style-type:disc;font-size:18px;\\\"><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Configuration requests - If you have a fully managed dedicated server with us then we offer custom PHP\\/MySQL configurations, firewalls for dedicated IPs, DNS, and httpd configurations.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Software requests - Cpanel Extension Installation will be granted as long as it does not interfere with the security, stability, and performance of other users on the server.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Emergency Support - We do not provide emergency support \\/ Phone Support \\/ LiveChat Support. Support may take some hours sometimes.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Webmaster help - We do not offer any support for webmaster related issues and difficulty including coding, &amp; installs, Error solving. if there is an issue where a library or configuration of the server then we can help you if it\'s possible from our end.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Backups - We keep backups but we are not responsible for data loss,\\r\\nyou are fully responsible for all backups.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">We Don\'t support any child porn or such material.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No spam-related sites or material, such as email lists, mass mail programs, and scripts, etc.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No harassing material that may cause people to retaliate against you.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No phishing pages.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">You may not run any exploitation script from the server. reason can be terminated immediately.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">If Anyone attempting to hack or exploit the server by using your script or hosting, we will terminate your account to keep safe other users.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Malicious Botnets are strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Spam, mass mailing, or email marketing in any way are strictly forbidden here.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Malicious hacking materials, trojans, viruses, &amp; malicious bots running or for download are forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Resource and cronjob abuse is forbidden and will result in suspension or termination.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Php\\/CGI proxies are strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">CGI-IRC is strictly forbidden.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">No fake or disposal mailers, mass mailing, mail bombers, SMS bombers, etc.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">NO CREDIT OR REFUND will be granted for interruptions of service, due to User Agreement violations.<\\/li><\\/ul><\\/div><div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;\\\">Terms &amp; Conditions for Users<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">Before getting to this site, you are consenting to be limited by these site Terms and Conditions of Use, every single appropriate law, and guidelines, and concur that you are answerable for consistency with any material neighborhood laws. If you disagree with any of these terms, you are restricted from utilizing or getting to this site.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;\\\">Support<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">Whenever you have downloaded our item, you may get in touch with us for help through email and we will give a valiant effort to determine your issue. We will attempt to answer using the Email for more modest bug fixes, after which we will refresh the center bundle. Content help is offered to confirmed clients by Tickets as it were. Backing demands made by email and Livechat.<\\/p><p class=\\\"my-3 font-18 font-weight-bold\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">On the off chance that your help requires extra adjustment of the System, at that point, you have two alternatives:<\\/p><ul class=\\\"font-18\\\" style=\\\"padding-left:15px;list-style-type:disc;font-size:18px;\\\"><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Hang tight for additional update discharge.<\\/li><li style=\\\"margin-top:0px;margin-right:0px;margin-left:0px;\\\">Or on the other hand, enlist a specialist (We offer customization for extra charges).<\\/li><\\/ul><\\/div><div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;\\\">Ownership<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">You may not guarantee scholarly or selective possession of any of our items, altered or unmodified. All items are property, we created them. Our items are given \\\"with no guarantees\\\" without guarantee of any sort, either communicated or suggested. On no occasion will our juridical individual be subject to any harms including, however not restricted to, immediate, roundabout, extraordinary, accidental, or significant harms or different misfortunes emerging out of the utilization of or powerlessness to utilize our items.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;\\\">Warranty<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We don\'t offer any guarantee or assurance of these Services in any way. When our Services have been modified we can\'t ensure they will work with all outsider plugins, modules, or internet browsers. Program similarity ought to be tried against the show formats on the demo worker. If you don\'t mind guarantee that the programs you use will work with the component,\\r\\nas we can not ensure that our systems will work with all program mixes.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;\\\">Unauthorized\\/Illegal Usage<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">You may not utilize our things for any illicit or unapproved reason or may you,\\r\\nin the utilization of the stage,\\r\\ndisregard any laws in your locale (counting yet not restricted to copyright laws) just as the laws of your nation and International law. Specifically,\\r\\nit is disallowed to utilize the things on our foundation for pages that advance: brutality,\\r\\nillegal intimidation,\\r\\nhard sexual entertainment,\\r\\nbigotry,\\r\\nobscenity content or warez programming joins.<br \\/><br \\/>You can\'t imitate, copy, duplicate, sell, exchange or adventure any of our segment, utilization of the offered on our things, or admittance to the administration without the express composed consent by us or item proprietor.<br \\/><br \\/>Our Members are liable for all substance posted on the discussion and demo and movement that happens under your record.<br \\/><br \\/>We hold the chance of hindering your participation account quickly if we will think about a particularly not allowed conduct.<br \\/><br \\/>If you make a record on our site, you are liable for keeping up the security of your record, and you are completely answerable for all exercises that happen under the record and some other activities taken regarding the record. You should quickly inform us, of any unapproved employments of your record or some other penetrates of security.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;\\\">Fiverr, Seoclerks Sellers Or Affiliates<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We do NOT ensure full SEO campaign conveyance within 24 hours. We make no assurance for conveyance time by any means. We give our best assessment to orders during the putting in of requests, anyway, these are gauges. We won\'t be considered liable for loss of assets,\\r\\nnegative surveys or you being prohibited for late conveyance. If you are selling on a site that requires time touchy outcomes,\\r\\nutilize Our SEO Services at your own risk.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;\\\">Payment\\/Refund Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">No refund or cash back will be made. After a deposit has been finished,\\r\\nit is extremely unlikely to invert it. You should utilize your equilibrium on requests our administrations,\\r\\nHosting,\\r\\nSEO campaign. You concur that once you complete a deposit,\\r\\nyou won\'t document a debate or a chargeback against us in any way, shape, or form.<br \\/><br \\/>If you document a debate or chargeback against us after a deposit, we claim all authority to end every single future request, prohibit you from our site. False action, for example, utilizing unapproved or taken charge cards will prompt the end of your record. There are no special cases.<\\/p><\\/div><div class=\\\"mb-5\\\" style=\\\"color:#dcf3ff;font-family:Nunito, sans-serif;margin-bottom:3rem;\\\"><h3 class=\\\"mb-3\\\" style=\\\"font-weight:600;line-height:1.3;font-size:24px;font-family:Exo, sans-serif;\\\">Free Balance \\/ Coupon Policy<\\/h3><p class=\\\"font-18\\\" style=\\\"margin-right:0px;margin-left:0px;font-size:18px;\\\">We offer numerous approaches to get FREE Balance, Coupons and Deposit offers yet we generally reserve the privilege to audit it and deduct it from your record offset with any explanation we may it is a sort of misuse. If we choose to deduct a few or all of free Balance from your record balance, and your record balance becomes negative, at that point the record will naturally be suspended. If your record is suspended because of a negative Balance you can request to make a custom payment to settle your equilibrium to actuate your record.<\\/p><\\/div>\"}', '2021-06-09 08:51:18', '2022-03-03 09:37:36'),
(44, 'feature.element', '{\"title\":\"Many Payment Gatways\",\"feature_icon\":\"<i class=\\\"las la-wallet\\\"><\\/i>\"}', '2022-02-01 08:18:18', '2022-02-01 08:18:18'),
(45, 'feature.element', '{\"title\":\"100% Money Back Guarantee\",\"feature_icon\":\"<i class=\\\"las la-dollar-sign\\\"><\\/i>\"}', '2022-02-01 08:18:37', '2022-02-01 08:18:37'),
(46, 'feature.element', '{\"title\":\"Worldwide Free Shipping\",\"feature_icon\":\"<i class=\\\"las la-shipping-fast\\\"><\\/i>\"}', '2022-02-01 08:18:52', '2022-02-01 08:18:52'),
(47, 'categories.content', '{\"heading\":\"Auction Categories\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\"}', '2022-02-01 08:25:09', '2022-02-01 08:25:09'),
(48, 'categories.element', '{\"heading\":\"Sports\",\"icon\":\"<i class=\\\"las la-chess-knight\\\"><\\/i>\"}', '2022-02-01 08:26:03', '2022-02-01 08:26:03'),
(49, 'categories.element', '{\"heading\":\"Electronics\",\"icon\":\"<i class=\\\"las la-mobile-alt\\\"><\\/i>\"}', '2022-02-01 08:26:17', '2022-02-01 08:26:17'),
(50, 'categories.element', '{\"heading\":\"Watches\",\"icon\":\"<i class=\\\"las la-hourglass-start\\\"><\\/i>\"}', '2022-02-01 08:26:26', '2022-02-01 08:26:26'),
(51, 'categories.element', '{\"heading\":\"Vehicles, Marine\",\"icon\":\"<i class=\\\"las la-car-side\\\"><\\/i>\"}', '2022-02-01 08:26:50', '2022-02-01 08:26:50'),
(52, 'categories.element', '{\"heading\":\"Real Estate\",\"icon\":\"<i class=\\\"las la-place-of-worship\\\"><\\/i>\"}', '2022-02-01 08:27:10', '2022-02-01 08:27:10'),
(53, 'categories.element', '{\"heading\":\"Jewelry\",\"icon\":\"<i class=\\\"las la-flask\\\"><\\/i>\"}', '2022-02-01 08:27:33', '2022-02-01 08:27:33'),
(54, 'categories.element', '{\"heading\":\"Industrial Machinery\",\"icon\":\"<i class=\\\"las la-industry\\\"><\\/i>\"}', '2022-02-01 08:27:40', '2022-02-01 08:27:40'),
(55, 'categories.element', '{\"heading\":\"Farm &amp; Agriculture\",\"icon\":\"<i class=\\\"las la-home\\\"><\\/i>\"}', '2022-02-01 08:28:18', '2022-02-01 08:28:18'),
(56, 'categories.element', '{\"heading\":\"Dolls, Bears &amp; Toys\",\"icon\":\"<i class=\\\"las la-universal-access\\\"><\\/i>\"}', '2022-02-01 08:28:26', '2022-02-01 08:28:26'),
(57, 'categories.element', '{\"heading\":\"Decorative Art\",\"icon\":\"<i class=\\\"las la-holly-berry\\\"><\\/i>\"}', '2022-02-01 08:28:45', '2022-02-01 08:28:53'),
(58, 'winner.content', '{\"heading\":\"Our Latest Winners\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\",\"has_image\":\"1\",\"image\":\"61f8fe1524b391643707925.png\"}', '2022-02-01 09:01:48', '2022-02-01 09:02:13'),
(59, 'how_to_bid.content', '{\"heading\":\"How To Bid\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\"}', '2022-02-01 09:12:30', '2022-02-01 09:12:30'),
(60, 'how_to_bid.element', '{\"heading\":\"Win Your Bid\",\"icon\":\"<i class=\\\"las la-trophy\\\"><\\/i>\"}', '2022-02-01 09:14:42', '2022-02-01 09:14:42'),
(61, 'how_to_bid.element', '{\"heading\":\"Make Bid Product\",\"icon\":\"<i class=\\\"las la-gavel\\\"><\\/i>\"}', '2022-02-01 09:14:55', '2022-02-01 09:14:55'),
(62, 'how_to_bid.element', '{\"heading\":\"Choose Products\",\"icon\":\"<i class=\\\"las la-hand-pointer\\\"><\\/i>\"}', '2022-02-01 09:15:07', '2022-02-01 09:15:07'),
(63, 'testimonial.content', '{\"heading\":\"Auction Winners Say\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\"}', '2022-02-01 09:19:29', '2022-02-01 09:19:29'),
(64, 'testimonial.element', '{\"name\":\"Mabrur Rashid Banna\",\"designation\":\"Businessman\",\"star\":\"4\",\"description\":\"Fugiat expedita eius quas consectetur culpa. Neque veniam et molestias laborum non corporis aperiam optio culpa. Fuga ipsum harum aliquam quod nisi nostrum nam quis, ipsam excepturi qui dignissimos quidem\",\"has_image\":\"1\",\"user_image\":\"61f903ddab6a31643709405.jpg\"}', '2022-02-01 09:26:45', '2022-02-22 13:22:33'),
(65, 'testimonial.element', '{\"name\":\"Mabrur Rashid Banna\",\"designation\":\"Sportsman\",\"star\":\"5\",\"description\":\"Fugiat expedita eius quas consectetur culpa. Neque veniam et molestias laborum non corporis aperiam optio culpa. Fuga ipsum harum aliquam quod nisi nostrum nam quis, ipsam excepturi qui dignissimos quidem.\",\"has_image\":\"1\",\"user_image\":\"61f9040db8c6b1643709453.jpg\"}', '2022-02-01 09:27:33', '2022-02-22 13:22:15'),
(66, 'testimonial.element', '{\"name\":\"John Doe\",\"designation\":\"Businessman\",\"star\":\"5\",\"description\":\"Fugiat expedita eius quas consectetur culpa. Neque veniam et molestias laborum non corporis aperiam optio culpa. Fuga ipsum harum aliquam quod nisi nostrum nam quis, ipsam excepturi qui dignissimos quidem.\",\"has_image\":\"1\",\"user_image\":\"61f904417d5061643709505.jpg\"}', '2022-02-01 09:28:25', '2022-02-22 13:22:21'),
(67, 'testimonial.element', '{\"name\":\"Mr David Roy\",\"designation\":\"Businessman\",\"star\":\"4\",\"description\":\"Fugiat expedita eius quas consectetur culpa. Neque veniam et molestias laborum non corporis aperiam optio culpa. Fuga ipsum harum aliquam quod nisi nostrum nam quis, ipsam excepturi qui dignissimos quidem.\",\"has_image\":\"1\",\"user_image\":\"61f9046e9dc841643709550.jpg\"}', '2022-02-01 09:29:10', '2022-02-22 13:22:28'),
(68, 'sponsors.element', '{\"has_image\":\"1\",\"image\":\"621c6e3f062971646030399.png\"}', '2022-02-01 09:34:38', '2022-02-28 06:39:59'),
(69, 'sponsors.element', '{\"has_image\":\"1\",\"image\":\"621c6e6d55ced1646030445.png\"}', '2022-02-01 09:34:43', '2022-02-28 06:40:45'),
(70, 'sponsors.element', '{\"has_image\":\"1\",\"image\":\"621c6e73ab3711646030451.png\"}', '2022-02-01 09:34:48', '2022-02-28 06:40:51'),
(71, 'sponsors.element', '{\"has_image\":\"1\",\"image\":\"621c6e7a527ef1646030458.png\"}', '2022-02-01 09:34:54', '2022-02-28 06:40:58'),
(72, 'sponsors.element', '{\"has_image\":\"1\",\"image\":\"621c6e7fcb3651646030463.png\"}', '2022-02-01 09:34:58', '2022-02-28 06:41:03'),
(73, 'sponsors.element', '{\"has_image\":\"1\",\"image\":\"621c6e85bb9d11646030469.png\"}', '2022-02-01 09:35:03', '2022-02-28 06:41:09'),
(74, 'auth.content', '{\"has_image\":\"1\",\"background_image\":\"61f90fbb32a711643712443.png\"}', '2022-02-01 10:17:23', '2022-02-01 10:17:23'),
(75, 'header.content', '{\"email\":\"demo@demo.com\",\"mobile\":\"959-595-959\"}', '2022-02-20 07:55:56', '2022-02-20 07:57:57'),
(76, 'faq.content', '{\"heading\":\"Frequently Asked Questions\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\"}', '2022-02-20 08:45:20', '2022-02-20 08:45:20'),
(77, 'faq.element', '{\"question\":\"Eligendi, aut ut sunt amet aperiam cum doloremque ?\",\"answer\":\"<div class=\\\"faq__title\\\" style=\\\"padding:20px 45px 20px 20px;margin-right:20px;color:rgb(164,189,206);font-family:Nunito, sans-serif;\\\"><h5 class=\\\"title\\\" style=\\\"line-height:1.2;font-size:20px;color:rgb(193,81,204);font-family:Jost, sans-serif;\\\"><\\/h5><p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">irst :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);margin-bottom:-7px;\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><\\/div>\"}', '2022-02-20 08:45:45', '2022-03-06 07:19:06'),
(78, 'faq.element', '{\"question\":\"Aut ut sunt amet aperiam cum doloremque distinctio ?\",\"answer\":\"<p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">First :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);margin-bottom:-7px;\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p>\"}', '2022-02-20 08:47:20', '2022-03-06 07:19:26'),
(79, 'faq.element', '{\"question\":\"Cum asperiores eligendi, aut ut sunt amet aperiam cum?\",\"answer\":\"<p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">irst :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);margin-bottom:-7px;\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p>\"}', '2022-02-20 08:47:33', '2022-03-06 07:19:42'),
(80, 'faq.element', '{\"question\":\"Adipisicing elit. Et quia ipsa quidem, ea in consectetur?\",\"answer\":\"<p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">First :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;color:rgb(164,189,206);font-family:Nunito, sans-serif;font-size:16px;background-color:rgb(0,19,41);margin-bottom:-7px;\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p>\"}', '2022-02-20 09:05:19', '2022-03-06 07:19:50'),
(81, 'faq.element', '{\"question\":\"Officiis iste minima, nemo, qui veritatis velit nesciunt?\",\"answer\":\"<p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">First :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);margin-bottom:-7px;\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p>\"}', '2022-02-20 09:07:42', '2022-03-06 07:19:56'),
(82, 'faq.element', '{\"question\":\"Aut ut sunt amet aperiam cum doloremque distinctio?\",\"answer\":\"<p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">First :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);margin-bottom:-7px;\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p>\"}', '2022-02-20 09:07:53', '2022-03-06 07:20:06'),
(83, 'faq.element', '{\"question\":\"Distinctio asperiores eligendi, aut ut sunt amet aperiam?\",\"answer\":\"<p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">First :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);margin-bottom:-7px;\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p>\"}', '2022-02-20 09:07:59', '2022-03-06 07:20:18'),
(84, 'faq.element', '{\"question\":\"Nulla dolore provident? Enim iste odio, eaque, adipisci?\",\"answer\":\"<p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">First :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);margin-bottom:-7px;\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p>\"}', '2022-02-20 09:08:06', '2022-03-06 07:20:25'),
(85, 'faq.element', '{\"question\":\"Minus nisi deserunt ea rerum ipsam ipsum eum. Quis tene?\",\"answer\":\"<p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">First :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);margin-bottom:-7px;\\\"><span class=\\\"text--base\\\" style=\\\"font-weight:bolder;font-family:Jost, sans-serif;color:rgb(193,81,204);\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p>\"}', '2022-02-20 09:08:18', '2022-03-06 07:20:31'),
(86, 'about.element', '{\"about_list\":\"Cum debitis delectus nulla minima placeat.\"}', '2022-02-20 10:58:17', '2022-02-20 10:58:17'),
(87, 'about.element', '{\"about_list\":\"Nisi perspiciatis id explicabo aliquam\"}', '2022-02-20 10:58:28', '2022-02-20 10:58:28'),
(88, 'about.element', '{\"about_list\":\"Repellendus qui in quae earum rerum ad officia.\"}', '2022-02-20 10:58:41', '2022-02-20 10:58:41'),
(89, 'counter.element', '{\"title\":\"Average Rating\",\"counter_digit\":\"4.5\",\"counter_icon\":\"<i class=\\\"lar la-smile\\\"><\\/i>\"}', '2022-02-20 11:49:16', '2022-02-20 11:49:16'),
(90, 'counter.element', '{\"title\":\"Winner Client\",\"counter_digit\":\"120\",\"counter_icon\":\"<i class=\\\"las la-user-friends\\\"><\\/i>\"}', '2022-02-20 11:50:01', '2022-02-20 11:50:01'),
(91, 'counter.element', '{\"title\":\"Total Bid\",\"counter_digit\":\"700\",\"counter_icon\":\"<i class=\\\"las la-store-alt\\\"><\\/i>\"}', '2022-02-20 11:53:52', '2022-02-20 11:53:52'),
(92, 'counter.element', '{\"title\":\"Satisfied Client\",\"counter_digit\":\"3000\",\"counter_icon\":\"<i class=\\\"las la-users\\\"><\\/i>\"}', '2022-02-20 11:54:19', '2022-02-20 11:54:19'),
(93, 'quick_banner.element', '{\"has_image\":\"1\",\"button\":\"Bid Now\",\"url\":\"category\\/4\\/industrial-machinery\",\"image\":\"6214e510d32891645536528.jpg\"}', '2022-02-22 12:58:48', '2022-03-06 07:13:06'),
(94, 'quick_banner.element', '{\"has_image\":\"1\",\"button\":\"Bid Now\",\"url\":\"category\\/2\\/dolls-bears-and-toys\",\"image\":\"6214e5251a1661645536549.jpg\"}', '2022-02-22 12:59:09', '2022-03-06 07:13:20'),
(95, 'quick_banner.element', '{\"has_image\":\"1\",\"button\":\"Bid Now\",\"url\":\"category\\/3\\/farm-and-agriculture\",\"image\":\"6214e52e0f82b1645536558.jpg\"}', '2022-02-22 12:59:18', '2022-03-06 07:14:07'),
(96, 'login.content', '{\"has_image\":\"1\",\"background_image\":\"62172609a21711645684233.png\"}', '2022-02-24 06:30:33', '2022-02-24 06:30:34'),
(97, 'register.content', '{\"has_image\":\"1\",\"background_image\":\"6217262fbae031645684271.png\"}', '2022-02-24 06:31:11', '2022-02-24 06:31:12'),
(98, '2fa_verify.content', '{\"has_image\":\"1\",\"background_image\":\"6217279c829dc1645684636.png\"}', '2022-02-24 06:37:16', '2022-02-24 06:37:16'),
(99, 'code_verify.content', '{\"has_image\":\"1\",\"background_image\":\"621727a336e2e1645684643.png\"}', '2022-02-24 06:37:23', '2022-02-24 06:37:23'),
(100, 'email_verify.content', '{\"has_image\":\"1\",\"background_image\":\"621727ab9dce11645684651.png\"}', '2022-02-24 06:37:31', '2022-02-24 06:37:32'),
(101, 'reset_password.content', '{\"has_image\":\"1\",\"background_image\":\"621727b1dfa8d1645684657.png\"}', '2022-02-24 06:37:37', '2022-02-24 06:37:38'),
(102, 'reset_password_email.content', '{\"has_image\":\"1\",\"background_image\":\"621727b82a4d51645684664.png\"}', '2022-02-24 06:37:44', '2022-02-24 06:37:44'),
(103, 'sms_verify.content', '{\"has_image\":\"1\",\"background_image\":\"621727bd571881645684669.png\"}', '2022-02-24 06:37:49', '2022-02-24 06:37:49');
INSERT INTO `frontends` (`id`, `data_keys`, `data_values`, `created_at`, `updated_at`) VALUES
(104, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Dolor sit amet consectetur adipisicing elit quis saepe minus culpa laborum\",\"description_nic\":\"<p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">\\u00a0Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile.digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile..<\\/p><blockquote class=\\\"mb-3 d-block\\\" style=\\\"background:rgba(1,28,61,0.9);padding:25px 20px;border-left:2px solid rgba(1,28,61,0.9);font-style:italic;color:rgb(164,189,206);font-family:Nunito, sans-serif;\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impnetworks and other enhancements to ensure fast loading times for both desktop and mobile.<p class=\\\"m-0\\\" style=\\\"margin-top:12px;margin-bottom:25px;font-size:20px;font-weight:600;\\\">--- John Doe<\\/p><\\/blockquote><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p>\",\"blog_image\":\"6219c74f321e91645856591.jpg\"}', '2022-02-26 06:21:44', '2022-02-26 06:23:11'),
(105, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Aspernatur nesciunt esse, quisquam dolor ullam voluptatem consequuntur possimus\",\"description_nic\":\"<p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile.digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile..<\\/p><blockquote class=\\\"mb-3 d-block\\\" style=\\\"background:rgba(1,28,61,0.9);padding:25px 20px;border-left:2px solid rgba(1,28,61,0.9);font-style:italic;color:rgb(164,189,206);font-family:Nunito, sans-serif;\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impnetworks and other enhancements to ensure fast loading times for both desktop and mobile.<p class=\\\"m-0\\\" style=\\\"margin-top:12px;margin-bottom:25px;font-size:20px;font-weight:600;\\\">--- John Doe<\\/p><\\/blockquote><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p>\",\"blog_image\":\"6219c705c15761645856517.jpg\"}', '2022-02-26 06:21:57', '2022-02-26 06:21:58'),
(106, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Ullam aliquam accusantium voluptates ad officia laudantium delectus animi\",\"description_nic\":\"<p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile.digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile..<\\/p><blockquote class=\\\"mb-3 d-block\\\" style=\\\"background:rgba(1,28,61,0.9);padding:25px 20px;border-left:2px solid rgba(1,28,61,0.9);font-style:italic;color:rgb(164,189,206);font-family:Nunito, sans-serif;\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impnetworks and other enhancements to ensure fast loading times for both desktop and mobile.<p class=\\\"m-0\\\" style=\\\"margin-top:12px;margin-bottom:25px;font-size:20px;font-weight:600;\\\">--- John Doe<\\/p><\\/blockquote><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p>\",\"blog_image\":\"6219c71a4659f1645856538.jpg\"}', '2022-02-26 06:22:18', '2022-02-26 06:22:18'),
(107, 'blog.element', '{\"has_image\":[\"1\"],\"title\":\"Deserunt nihil asperiores reiciendis assumenda reprehenderit corporis nam omnis\",\"description_nic\":\"<p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile.digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impact user satisfaction and visitor retention. Google also factors website performance in its ranking algorithm. Our designers and developers use best practice tools and techniques when it comes to building websites such as image optimisation, using content delivery networks and other enhancements to ensure fast loading times for both desktop and mobile..<\\/p><blockquote class=\\\"mb-3 d-block\\\" style=\\\"background:rgba(1,28,61,0.9);padding:25px 20px;border-left:2px solid rgba(1,28,61,0.9);font-style:italic;color:rgb(164,189,206);font-family:Nunito, sans-serif;\\\">Website performance, or how fast web pages load onto a client\\u2019s browser can impnetworks and other enhancements to ensure fast loading times for both desktop and mobile.<p class=\\\"m-0\\\" style=\\\"margin-top:12px;margin-bottom:25px;font-size:20px;font-weight:600;\\\">--- John Doe<\\/p><\\/blockquote><p style=\\\"margin-bottom:25px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\">We are a full-service web design, web development and digital solutions company. Mettro\\u2019s web designers, developers, user experience designers, SEO specialists, and marketing experts are adept at fusing vision, design, and technology to provide you with custom digital solutions that will help you achieve your business goals.<\\/p>\",\"blog_image\":\"6219c72768c211645856551.jpg\"}', '2022-02-26 06:22:31', '2022-02-26 06:22:31'),
(108, 'quick_banner.element', '{\"has_image\":\"1\",\"button\":\"Bid Now\",\"url\":\"category\\/10\\/sports\",\"image\":\"622065a4733a41646290340.jpg\"}', '2022-03-03 06:51:48', '2022-03-06 07:14:20'),
(109, 'live_auction.content', '{\"heading\":\"Live Auction\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\"}', '2022-03-03 07:04:04', '2022-03-03 07:04:04'),
(110, 'recently_expired.content', '{\"heading\":\"Recently Expired Auctions\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\"}', '2022-03-03 07:04:05', '2022-03-03 07:04:05'),
(111, 'upcoming_auction.content', '{\"heading\":\"Upcoming Auctions\",\"subheading\":\"Praesentium ipsam modi nostrum, quibusdam voluptas minus qui quas dicta consequuntur placeat animi cumque\"}', '2022-03-03 07:04:07', '2022-03-03 07:04:07'),
(112, 'breadcrumb.content', '{\"has_image\":\"1\",\"background_image\":\"6224a2b19dd171646568113.jpg\"}', '2022-03-06 11:56:07', '2022-03-06 12:01:53'),
(113, 'faq.element', '{\"question\":\"Aperiam cum doloremque distias periores eligendi?\",\"answer\":\"<p style=\\\"margin-top:-12px;margin-bottom:20px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"color:rgb(193,81,204);font-weight:bolder;font-family:Jost, sans-serif;\\\">irst :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p><p style=\\\"margin-top:-12px;margin-bottom:-7px;color:rgb(164,189,206);font-size:16px;font-family:Nunito, sans-serif;background-color:rgb(0,19,41);\\\"><span class=\\\"text--base\\\" style=\\\"color:rgb(193,81,204);font-weight:bolder;font-family:Jost, sans-serif;\\\">Second :\\u00a0<\\/span>Obcaecati aperiam cumque corporis, deleniti officiis deserunt cum dignissimos totam corrupti natus amet. deleniti officiis deserunt cum dignissimos totam corrupti natus amet.<\\/p>\"}', '2022-03-07 05:01:16', '2022-03-07 05:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `gateways`
--

CREATE TABLE `gateways` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` int(10) DEFAULT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NULL',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=>enable, 2=>disable',
  `gateway_parameters` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supported_currencies` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crypto` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: fiat currency, 1: crypto currency',
  `extra` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `input_form` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gateways`
--

INSERT INTO `gateways` (`id`, `code`, `name`, `alias`, `image`, `status`, `gateway_parameters`, `supported_currencies`, `crypto`, `extra`, `description`, `input_form`, `created_at`, `updated_at`) VALUES
(1, 101, 'Paypal', 'Paypal', '5f6f1bd8678601601117144.jpg', 1, '{\"paypal_email\":{\"title\":\"PayPal Email\",\"global\":true,\"value\":\"sb-owud61543012@business.example.com\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:04:38'),
(2, 102, 'Perfect Money', 'PerfectMoney', '5f6f1d2a742211601117482.jpg', 1, '{\"passphrase\":{\"title\":\"ALTERNATE PASSPHRASE\",\"global\":true,\"value\":\"hR26aw02Q1eEeUPSIfuwNypXX\"},\"wallet_id\":{\"title\":\"PM Wallet\",\"global\":false,\"value\":\"\"}}', '{\"USD\":\"$\",\"EUR\":\"\\u20ac\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 01:35:33'),
(3, 103, 'Stripe Hosted', 'Stripe', '5f6f1d4bc69e71601117515.jpg', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:48:36'),
(4, 104, 'Skrill', 'Skrill', '5f6f1d41257181601117505.jpg', 1, '{\"pay_to_email\":{\"title\":\"Skrill Email\",\"global\":true,\"value\":\"merchant@skrill.com\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"---\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JOD\":\"JOD\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"KWD\":\"KWD\",\"MAD\":\"MAD\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"OMR\":\"OMR\",\"PLN\":\"PLN\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"SAR\":\"SAR\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TND\":\"TND\",\"TRY\":\"TRY\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\",\"COP\":\"COP\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 01:30:16'),
(5, 105, 'PayTM', 'Paytm', '5f6f1d1d3ec731601117469.jpg', 1, '{\"MID\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"DIY12386817555501617\"},\"merchant_key\":{\"title\":\"Merchant Key\",\"global\":true,\"value\":\"bKMfNxPPf_QdZppa\"},\"WEBSITE\":{\"title\":\"Paytm Website\",\"global\":true,\"value\":\"DIYtestingweb\"},\"INDUSTRY_TYPE_ID\":{\"title\":\"Industry Type\",\"global\":true,\"value\":\"Retail\"},\"CHANNEL_ID\":{\"title\":\"CHANNEL ID\",\"global\":true,\"value\":\"WEB\"},\"transaction_url\":{\"title\":\"Transaction URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/oltp-web\\/processTransaction\"},\"transaction_status_url\":{\"title\":\"Transaction STATUS URL\",\"global\":true,\"value\":\"https:\\/\\/pguat.paytm.com\\/paytmchecksum\\/paytmCallback.jsp\"}}', '{\"AUD\":\"AUD\",\"ARS\":\"ARS\",\"BDT\":\"BDT\",\"BRL\":\"BRL\",\"BGN\":\"BGN\",\"CAD\":\"CAD\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"COP\":\"COP\",\"HRK\":\"HRK\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EGP\":\"EGP\",\"EUR\":\"EUR\",\"GEL\":\"GEL\",\"GHS\":\"GHS\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"KES\":\"KES\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"MAD\":\"MAD\",\"NPR\":\"NPR\",\"NZD\":\"NZD\",\"NGN\":\"NGN\",\"NOK\":\"NOK\",\"PKR\":\"PKR\",\"PEN\":\"PEN\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"ZAR\":\"ZAR\",\"KRW\":\"KRW\",\"LKR\":\"LKR\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"TRY\":\"TRY\",\"UGX\":\"UGX\",\"UAH\":\"UAH\",\"AED\":\"AED\",\"GBP\":\"GBP\",\"USD\":\"USD\",\"VND\":\"VND\",\"XOF\":\"XOF\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 03:00:44'),
(6, 106, 'Payeer', 'Payeer', '5f6f1bc61518b1601117126.jpg', 0, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"866989763\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"7575\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"RUB\":\"RUB\"}', 0, '{\"status\":{\"title\": \"Status URL\",\"value\":\"ipn.Payeer\"}}', NULL, NULL, '2019-09-14 13:14:22', '2020-12-28 01:26:58'),
(7, 107, 'PayStack', 'Paystack', '5f7096563dfb71601214038.jpg', 1, '{\"public_key\":{\"title\":\"Public key\",\"global\":true,\"value\":\"pk_test_cd330608eb47970889bca397ced55c1dd5ad3783\"},\"secret_key\":{\"title\":\"Secret key\",\"global\":true,\"value\":\"sk_test_8a0b1f199362d7acc9c390bff72c4e81f74e2ac3\"}}', '{\"USD\":\"USD\",\"NGN\":\"NGN\"}', 0, '{\"callback\":{\"title\": \"Callback URL\",\"value\":\"ipn.Paystack\"},\"webhook\":{\"title\": \"Webhook URL\",\"value\":\"ipn.Paystack\"}}\r\n', NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 01:49:51'),
(8, 108, 'VoguePay', 'Voguepay', '5f6f1d5951a111601117529.jpg', 1, '{\"merchant_id\":{\"title\":\"MERCHANT ID\",\"global\":true,\"value\":\"demo\"}}', '{\"USD\":\"USD\",\"GBP\":\"GBP\",\"EUR\":\"EUR\",\"GHS\":\"GHS\",\"NGN\":\"NGN\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 01:22:38'),
(9, 109, 'Flutterwave', 'Flutterwave', '5f6f1b9e4bb961601117086.jpg', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"----------------\"},\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"-----------------------\"},\"encryption_key\":{\"title\":\"Encryption Key\",\"global\":true,\"value\":\"------------------\"}}', '{\"BIF\":\"BIF\",\"CAD\":\"CAD\",\"CDF\":\"CDF\",\"CVE\":\"CVE\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"GHS\":\"GHS\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"KES\":\"KES\",\"LRD\":\"LRD\",\"MWK\":\"MWK\",\"MZN\":\"MZN\",\"NGN\":\"NGN\",\"RWF\":\"RWF\",\"SLL\":\"SLL\",\"STD\":\"STD\",\"TZS\":\"TZS\",\"UGX\":\"UGX\",\"USD\":\"USD\",\"XAF\":\"XAF\",\"XOF\":\"XOF\",\"ZMK\":\"ZMK\",\"ZMW\":\"ZMW\",\"ZWD\":\"ZWD\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-06-05 11:37:45'),
(10, 110, 'RazorPay', 'Razorpay', '5f6f1d3672dd61601117494.jpg', 1, '{\"key_id\":{\"title\":\"Key Id\",\"global\":true,\"value\":\"rzp_test_kiOtejPbRZU90E\"},\"key_secret\":{\"title\":\"Key Secret \",\"global\":true,\"value\":\"osRDebzEqbsE1kbyQJ4y0re7\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:51:32'),
(11, 111, 'Stripe Storefront', 'StripeJs', '5f7096a31ed9a1601214115.jpg', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:53:10'),
(12, 112, 'Instamojo', 'Instamojo', '5f6f1babbdbb31601117099.jpg', 1, '{\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_2241633c3bc44a3de84a3b33969\"},\"auth_token\":{\"title\":\"Auth Token\",\"global\":true,\"value\":\"test_279f083f7bebefd35217feef22d\"},\"salt\":{\"title\":\"Salt\",\"global\":true,\"value\":\"19d38908eeff4f58b2ddda2c6d86ca25\"}}', '{\"INR\":\"INR\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:56:20'),
(13, 501, 'Blockchain', 'Blockchain', '5f6f1b2b20c6f1601116971.jpg', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"55529946-05ca-48ff-8710-f279d86b1cc5\"},\"xpub_code\":{\"title\":\"XPUB CODE\",\"global\":true,\"value\":\"xpub6CKQ3xxWyBoFAF83izZCSFUorptEU9AF8TezhtWeMU5oefjX3sFSBw62Lr9iHXPkXmDQJJiHZeTRtD9Vzt8grAYRhvbz4nEvBu3QKELVzFK\"}}', '{\"BTC\":\"BTC\"}', 1, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:25:00'),
(14, 502, 'Block.io', 'Blockio', '5f6f19432bedf1601116483.jpg', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":false,\"value\":\"1658-8015-2e5e-9afb\"},\"api_pin\":{\"title\":\"API PIN\",\"global\":true,\"value\":\"75757575\"}}', '{\"BTC\":\"BTC\",\"LTC\":\"LTC\"}', 1, '{\"cron\":{\"title\": \"Cron URL\",\"value\":\"ipn.Blockio\"}}', NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:31:09'),
(15, 503, 'CoinPayments', 'Coinpayments', '5f6f1b6c02ecd1601117036.jpg', 1, '{\"public_key\":{\"title\":\"Public Key\",\"global\":true,\"value\":\"---------------\"},\"private_key\":{\"title\":\"Private Key\",\"global\":true,\"value\":\"------------\"},\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"93a1e014c4ad60a7980b4a7239673cb4\"}}', '{\"BTC\":\"Bitcoin\",\"BTC.LN\":\"Bitcoin (Lightning Network)\",\"LTC\":\"Litecoin\",\"CPS\":\"CPS Coin\",\"VLX\":\"Velas\",\"APL\":\"Apollo\",\"AYA\":\"Aryacoin\",\"BAD\":\"Badcoin\",\"BCD\":\"Bitcoin Diamond\",\"BCH\":\"Bitcoin Cash\",\"BCN\":\"Bytecoin\",\"BEAM\":\"BEAM\",\"BITB\":\"Bean Cash\",\"BLK\":\"BlackCoin\",\"BSV\":\"Bitcoin SV\",\"BTAD\":\"Bitcoin Adult\",\"BTG\":\"Bitcoin Gold\",\"BTT\":\"BitTorrent\",\"CLOAK\":\"CloakCoin\",\"CLUB\":\"ClubCoin\",\"CRW\":\"Crown\",\"CRYP\":\"CrypticCoin\",\"CRYT\":\"CryTrExCoin\",\"CURE\":\"CureCoin\",\"DASH\":\"DASH\",\"DCR\":\"Decred\",\"DEV\":\"DeviantCoin\",\"DGB\":\"DigiByte\",\"DOGE\":\"Dogecoin\",\"EBST\":\"eBoost\",\"EOS\":\"EOS\",\"ETC\":\"Ether Classic\",\"ETH\":\"Ethereum\",\"ETN\":\"Electroneum\",\"EUNO\":\"EUNO\",\"EXP\":\"EXP\",\"Expanse\":\"Expanse\",\"FLASH\":\"FLASH\",\"GAME\":\"GameCredits\",\"GLC\":\"Goldcoin\",\"GRS\":\"Groestlcoin\",\"KMD\":\"Komodo\",\"LOKI\":\"LOKI\",\"LSK\":\"LSK\",\"MAID\":\"MaidSafeCoin\",\"MUE\":\"MonetaryUnit\",\"NAV\":\"NAV Coin\",\"NEO\":\"NEO\",\"NMC\":\"Namecoin\",\"NVST\":\"NVO Token\",\"NXT\":\"NXT\",\"OMNI\":\"OMNI\",\"PINK\":\"PinkCoin\",\"PIVX\":\"PIVX\",\"POT\":\"PotCoin\",\"PPC\":\"Peercoin\",\"PROC\":\"ProCurrency\",\"PURA\":\"PURA\",\"QTUM\":\"QTUM\",\"RES\":\"Resistance\",\"RVN\":\"Ravencoin\",\"RVR\":\"RevolutionVR\",\"SBD\":\"Steem Dollars\",\"SMART\":\"SmartCash\",\"SOXAX\":\"SOXAX\",\"STEEM\":\"STEEM\",\"STRAT\":\"STRAT\",\"SYS\":\"Syscoin\",\"TPAY\":\"TokenPay\",\"TRIGGERS\":\"Triggers\",\"TRX\":\" TRON\",\"UBQ\":\"Ubiq\",\"UNIT\":\"UniversalCurrency\",\"USDT\":\"Tether USD (Omni Layer)\",\"VTC\":\"Vertcoin\",\"WAVES\":\"Waves\",\"XCP\":\"Counterparty\",\"XEM\":\"NEM\",\"XMR\":\"Monero\",\"XSN\":\"Stakenet\",\"XSR\":\"SucreCoin\",\"XVG\":\"VERGE\",\"XZC\":\"ZCoin\",\"ZEC\":\"ZCash\",\"ZEN\":\"Horizen\"}', 1, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:07:14'),
(16, 504, 'CoinPayments Fiat', 'CoinpaymentsFiat', '5f6f1b94e9b2b1601117076.jpg', 1, '{\"merchant_id\":{\"title\":\"Merchant ID\",\"global\":true,\"value\":\"6515561\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CLP\":\"CLP\",\"CNY\":\"CNY\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"KRW\":\"KRW\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:07:44'),
(17, 505, 'Coingate', 'Coingate', '5f6f1b5fe18ee1601117023.jpg', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"6354mwVCEw5kHzRJ6thbGo-N\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:49:30'),
(18, 506, 'Coinbase Commerce', 'CoinbaseCommerce', '5f6f1b4c774af1601117004.jpg', 1, '{\"api_key\":{\"title\":\"API Key\",\"global\":true,\"value\":\"c47cd7df-d8e8-424b-a20a\"},\"secret\":{\"title\":\"Webhook Shared Secret\",\"global\":true,\"value\":\"55871878-2c32-4f64-ab66\"}}', '{\"USD\":\"USD\",\"EUR\":\"EUR\",\"JPY\":\"JPY\",\"GBP\":\"GBP\",\"AUD\":\"AUD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CNY\":\"CNY\",\"SEK\":\"SEK\",\"NZD\":\"NZD\",\"MXN\":\"MXN\",\"SGD\":\"SGD\",\"HKD\":\"HKD\",\"NOK\":\"NOK\",\"KRW\":\"KRW\",\"TRY\":\"TRY\",\"RUB\":\"RUB\",\"INR\":\"INR\",\"BRL\":\"BRL\",\"ZAR\":\"ZAR\",\"AED\":\"AED\",\"AFN\":\"AFN\",\"ALL\":\"ALL\",\"AMD\":\"AMD\",\"ANG\":\"ANG\",\"AOA\":\"AOA\",\"ARS\":\"ARS\",\"AWG\":\"AWG\",\"AZN\":\"AZN\",\"BAM\":\"BAM\",\"BBD\":\"BBD\",\"BDT\":\"BDT\",\"BGN\":\"BGN\",\"BHD\":\"BHD\",\"BIF\":\"BIF\",\"BMD\":\"BMD\",\"BND\":\"BND\",\"BOB\":\"BOB\",\"BSD\":\"BSD\",\"BTN\":\"BTN\",\"BWP\":\"BWP\",\"BYN\":\"BYN\",\"BZD\":\"BZD\",\"CDF\":\"CDF\",\"CLF\":\"CLF\",\"CLP\":\"CLP\",\"COP\":\"COP\",\"CRC\":\"CRC\",\"CUC\":\"CUC\",\"CUP\":\"CUP\",\"CVE\":\"CVE\",\"CZK\":\"CZK\",\"DJF\":\"DJF\",\"DKK\":\"DKK\",\"DOP\":\"DOP\",\"DZD\":\"DZD\",\"EGP\":\"EGP\",\"ERN\":\"ERN\",\"ETB\":\"ETB\",\"FJD\":\"FJD\",\"FKP\":\"FKP\",\"GEL\":\"GEL\",\"GGP\":\"GGP\",\"GHS\":\"GHS\",\"GIP\":\"GIP\",\"GMD\":\"GMD\",\"GNF\":\"GNF\",\"GTQ\":\"GTQ\",\"GYD\":\"GYD\",\"HNL\":\"HNL\",\"HRK\":\"HRK\",\"HTG\":\"HTG\",\"HUF\":\"HUF\",\"IDR\":\"IDR\",\"ILS\":\"ILS\",\"IMP\":\"IMP\",\"IQD\":\"IQD\",\"IRR\":\"IRR\",\"ISK\":\"ISK\",\"JEP\":\"JEP\",\"JMD\":\"JMD\",\"JOD\":\"JOD\",\"KES\":\"KES\",\"KGS\":\"KGS\",\"KHR\":\"KHR\",\"KMF\":\"KMF\",\"KPW\":\"KPW\",\"KWD\":\"KWD\",\"KYD\":\"KYD\",\"KZT\":\"KZT\",\"LAK\":\"LAK\",\"LBP\":\"LBP\",\"LKR\":\"LKR\",\"LRD\":\"LRD\",\"LSL\":\"LSL\",\"LYD\":\"LYD\",\"MAD\":\"MAD\",\"MDL\":\"MDL\",\"MGA\":\"MGA\",\"MKD\":\"MKD\",\"MMK\":\"MMK\",\"MNT\":\"MNT\",\"MOP\":\"MOP\",\"MRO\":\"MRO\",\"MUR\":\"MUR\",\"MVR\":\"MVR\",\"MWK\":\"MWK\",\"MYR\":\"MYR\",\"MZN\":\"MZN\",\"NAD\":\"NAD\",\"NGN\":\"NGN\",\"NIO\":\"NIO\",\"NPR\":\"NPR\",\"OMR\":\"OMR\",\"PAB\":\"PAB\",\"PEN\":\"PEN\",\"PGK\":\"PGK\",\"PHP\":\"PHP\",\"PKR\":\"PKR\",\"PLN\":\"PLN\",\"PYG\":\"PYG\",\"QAR\":\"QAR\",\"RON\":\"RON\",\"RSD\":\"RSD\",\"RWF\":\"RWF\",\"SAR\":\"SAR\",\"SBD\":\"SBD\",\"SCR\":\"SCR\",\"SDG\":\"SDG\",\"SHP\":\"SHP\",\"SLL\":\"SLL\",\"SOS\":\"SOS\",\"SRD\":\"SRD\",\"SSP\":\"SSP\",\"STD\":\"STD\",\"SVC\":\"SVC\",\"SYP\":\"SYP\",\"SZL\":\"SZL\",\"THB\":\"THB\",\"TJS\":\"TJS\",\"TMT\":\"TMT\",\"TND\":\"TND\",\"TOP\":\"TOP\",\"TTD\":\"TTD\",\"TWD\":\"TWD\",\"TZS\":\"TZS\",\"UAH\":\"UAH\",\"UGX\":\"UGX\",\"UYU\":\"UYU\",\"UZS\":\"UZS\",\"VEF\":\"VEF\",\"VND\":\"VND\",\"VUV\":\"VUV\",\"WST\":\"WST\",\"XAF\":\"XAF\",\"XAG\":\"XAG\",\"XAU\":\"XAU\",\"XCD\":\"XCD\",\"XDR\":\"XDR\",\"XOF\":\"XOF\",\"XPD\":\"XPD\",\"XPF\":\"XPF\",\"XPT\":\"XPT\",\"YER\":\"YER\",\"ZMW\":\"ZMW\",\"ZWL\":\"ZWL\"}\r\n\r\n', 0, '{\"endpoint\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.CoinbaseCommerce\"}}', NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:02:47'),
(24, 113, 'Paypal Express', 'PaypalSdk', '5f6f1bec255c61601117164.jpg', 1, '{\"clientId\":{\"title\":\"Paypal Client ID\",\"global\":true,\"value\":\"Ae0-tixtSV7DvLwIh3Bmu7JvHrjh5EfGdXr_cEklKAVjjezRZ747BxKILiBdzlKKyp-W8W_T7CKH1Ken\"},\"clientSecret\":{\"title\":\"Client Secret\",\"global\":true,\"value\":\"EOhbvHZgFNO21soQJT1L9Q00M3rK6PIEsdiTgXRBt2gtGtxwRer5JvKnVUGNU5oE63fFnjnYY7hq3HBA\"}}', '{\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"HKD\":\"HKD\",\"HUF\":\"HUF\",\"INR\":\"INR\",\"ILS\":\"ILS\",\"JPY\":\"JPY\",\"MYR\":\"MYR\",\"MXN\":\"MXN\",\"TWD\":\"TWD\",\"NZD\":\"NZD\",\"NOK\":\"NOK\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"GBP\":\"GBP\",\"RUB\":\"RUB\",\"SGD\":\"SGD\",\"SEK\":\"SEK\",\"CHF\":\"CHF\",\"THB\":\"THB\",\"USD\":\"$\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-20 23:01:08'),
(25, 114, 'Stripe Checkout', 'StripeV3', '5f709684736321601214084.jpg', 1, '{\"secret_key\":{\"title\":\"Secret Key\",\"global\":true,\"value\":\"sk_test_51I6GGiCGv1sRiQlEi5v1or9eR0HVbuzdMd2rW4n3DxC8UKfz66R4X6n4yYkzvI2LeAIuRU9H99ZpY7XCNFC9xMs500vBjZGkKG\"},\"publishable_key\":{\"title\":\"PUBLISHABLE KEY\",\"global\":true,\"value\":\"pk_test_51I6GGiCGv1sRiQlEOisPKrjBqQqqcFsw8mXNaZ2H2baN6R01NulFS7dKFji1NRRxuchoUTEDdB7ujKcyKYSVc0z500eth7otOM\"},\"end_point\":{\"title\":\"End Point Secret\",\"global\":true,\"value\":\"whsec_lUmit1gtxwKTveLnSe88xCSDdnPOt8g5\"}}', '{\"USD\":\"USD\",\"AUD\":\"AUD\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"INR\":\"INR\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"SGD\":\"SGD\"}', 0, '{\"webhook\":{\"title\": \"Webhook Endpoint\",\"value\":\"ipn.StripeV3\"}}', NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 00:58:38'),
(27, 115, 'Mollie', 'Mollie', '5f6f1bb765ab11601117111.jpg', 1, '{\"mollie_email\":{\"title\":\"Mollie Email \",\"global\":true,\"value\":\"vi@gmail.com\"},\"api_key\":{\"title\":\"API KEY\",\"global\":true,\"value\":\"test_cucfwKTWfft9s337qsVfn5CC4vNkrn\"}}', '{\"AED\":\"AED\",\"AUD\":\"AUD\",\"BGN\":\"BGN\",\"BRL\":\"BRL\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"CZK\":\"CZK\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"HKD\":\"HKD\",\"HRK\":\"HRK\",\"HUF\":\"HUF\",\"ILS\":\"ILS\",\"ISK\":\"ISK\",\"JPY\":\"JPY\",\"MXN\":\"MXN\",\"MYR\":\"MYR\",\"NOK\":\"NOK\",\"NZD\":\"NZD\",\"PHP\":\"PHP\",\"PLN\":\"PLN\",\"RON\":\"RON\",\"RUB\":\"RUB\",\"SEK\":\"SEK\",\"SGD\":\"SGD\",\"THB\":\"THB\",\"TWD\":\"TWD\",\"USD\":\"USD\",\"ZAR\":\"ZAR\"}', 0, NULL, NULL, NULL, '2019-09-14 13:14:22', '2021-05-21 02:44:45'),
(30, 116, 'Cashmaal', 'Cashmaal', '60d1a0b7c98311624350903.png', 1, '{\"web_id\":{\"title\":\"Web Id\",\"global\":true,\"value\":\"3748\"},\"ipn_key\":{\"title\":\"IPN Key\",\"global\":true,\"value\":\"546254628759524554647987\"}}', '{\"PKR\":\"PKR\",\"USD\":\"USD\"}', 0, '{\"webhook\":{\"title\": \"IPN URL\",\"value\":\"ipn.Cashmaal\"}}', NULL, NULL, NULL, '2021-06-22 08:05:04'),
(36, 119, 'Mercado Pago', 'MercadoPago', '60f2ad85a82951626516869.png', 1, '{\"access_token\":{\"title\":\"Access Token\",\"global\":true,\"value\":\"3Vee5S2F\"}}', '{\"USD\":\"USD\",\"CAD\":\"CAD\",\"CHF\":\"CHF\",\"DKK\":\"DKK\",\"EUR\":\"EUR\",\"GBP\":\"GBP\",\"NOK\":\"NOK\",\"PLN\":\"PLN\",\"SEK\":\"SEK\",\"AUD\":\"AUD\",\"NZD\":\"NZD\"}', 0, NULL, NULL, NULL, NULL, '2021-07-17 09:44:29');

-- --------------------------------------------------------

--
-- Table structure for table `gateway_currencies`
--

CREATE TABLE `gateway_currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method_code` int(10) DEFAULT NULL,
  `gateway_alias` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `max_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) NOT NULL DEFAULT 0.00,
  `fixed_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_parameter` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

CREATE TABLE `general_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sitename` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cur_text` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'currency text',
  `cur_sym` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'currency symbol',
  `email_from` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_template` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_api` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_color` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mail_config` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'email configuration',
  `sms_config` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_profile` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email verification, 0 - dont check, 1 - check',
  `en` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'email notification, 0 - dont send, 1 - send',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'sms verication, 0 - dont check, 1 - check',
  `sn` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'sms notification, 0 - dont send, 1 - send',
  `force_ssl` tinyint(1) NOT NULL DEFAULT 0,
  `secure_password` tinyint(1) NOT NULL DEFAULT 0,
  `agree` tinyint(1) NOT NULL DEFAULT 0,
  `registration` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Off	, 1: On',
  `active_template` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sys_version` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`id`, `sitename`, `cur_text`, `cur_sym`, `email_from`, `email_template`, `sms_api`, `base_color`, `mail_config`, `sms_config`, `merchant_profile`, `ev`, `en`, `sv`, `sn`, `force_ssl`, `secure_password`, `agree`, `registration`, `active_template`, `sys_version`, `created_at`, `updated_at`) VALUES
(1, 'Viserbid', 'USD', '$', 'info@viserlab.com', '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\r\n  <!--[if !mso]><!-->\r\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\r\n  <!--<![endif]-->\r\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n  <title></title>\r\n  <style type=\"text/css\">\r\n.ReadMsgBody { width: 100%; background-color: #ffffff; }\r\n.ExternalClass { width: 100%; background-color: #ffffff; }\r\n.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }\r\nhtml { width: 100%; }\r\nbody { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }\r\ntable { border-spacing: 0; table-layout: fixed; margin: 0 auto;border-collapse: collapse; }\r\ntable table table { table-layout: auto; }\r\n.yshortcuts a { border-bottom: none !important; }\r\nimg:hover { opacity: 0.9 !important; }\r\na { color: #0087ff; text-decoration: none; }\r\n.textbutton a { font-family: \'open sans\', arial, sans-serif !important;}\r\n.btn-link a { color:#FFFFFF !important;}\r\n\r\n@media only screen and (max-width: 480px) {\r\nbody { width: auto !important; }\r\n*[class=\"table-inner\"] { width: 90% !important; text-align: center !important; }\r\n*[class=\"table-full\"] { width: 100% !important; text-align: center !important; }\r\n/* image */\r\nimg[class=\"img1\"] { width: 100% !important; height: auto !important; }\r\n}\r\n</style>\r\n\r\n\r\n\r\n  <table bgcolor=\"#414a51\" width=\"100%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n    <tbody><tr>\r\n      <td height=\"50\"></td>\r\n    </tr>\r\n    <tr>\r\n      <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n        <table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\r\n          <tbody><tr>\r\n            <td align=\"center\" width=\"600\">\r\n              <!--header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#0087ff\" style=\"border-top-left-radius:6px; border-top-right-radius:6px;text-align:center;vertical-align:top;font-size:0;\" align=\"center\">\r\n                    <table width=\"90%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#FFFFFF; font-size:16px; font-weight: bold;\">This is a System Generated Email</td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n              <!--end header-->\r\n              <table class=\"table-inner\" width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                <tbody><tr>\r\n                  <td bgcolor=\"#FFFFFF\" align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"35\"></td>\r\n                      </tr>\r\n                      <!--logo-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"vertical-align:top;font-size:0;\">\r\n                          <a href=\"#\">\r\n                            <img style=\"display:block; line-height:0px; font-size:0px; border:0px;\" src=\"https://i.imgur.com/Z1qtvtV.png\" alt=\"img\">\r\n                          </a>\r\n                        </td>\r\n                      </tr>\r\n                      <!--end logo-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n                      <!--headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"font-family: \'Open Sans\', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold;\">Hello {{fullname}} ({{username}})</td>\r\n                      </tr>\r\n                      <!--end headline-->\r\n                      <tr>\r\n                        <td align=\"center\" style=\"text-align:center;vertical-align:top;font-size:0;\">\r\n                          <table width=\"40\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\r\n                            <tbody><tr>\r\n                              <td height=\"20\" style=\" border-bottom:3px solid #0087ff;\"></td>\r\n                            </tr>\r\n                          </tbody></table>\r\n                        </td>\r\n                      </tr>\r\n                      <tr>\r\n                        <td height=\"20\"></td>\r\n                      </tr>\r\n                      <!--content-->\r\n                      <tr>\r\n                        <td align=\"left\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px;\">{{message}}</td>\r\n                      </tr>\r\n                      <!--end content-->\r\n                      <tr>\r\n                        <td height=\"40\"></td>\r\n                      </tr>\r\n              \r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n                <tr>\r\n                  <td height=\"45\" align=\"center\" bgcolor=\"#f4f4f4\" style=\"border-bottom-left-radius:6px;border-bottom-right-radius:6px;\">\r\n                    <table align=\"center\" width=\"90%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n                      <tbody><tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                      <!--preference-->\r\n                      <tr>\r\n                        <td class=\"preference-link\" align=\"center\" style=\"font-family: \'Open sans\', Arial, sans-serif; color:#95a5a6; font-size:14px;\">\r\n                          © 2021 <a href=\"#\">Website Name</a> . All Rights Reserved. \r\n                        </td>\r\n                      </tr>\r\n                      <!--end preference-->\r\n                      <tr>\r\n                        <td height=\"10\"></td>\r\n                      </tr>\r\n                    </tbody></table>\r\n                  </td>\r\n                </tr>\r\n              </tbody></table>\r\n            </td>\r\n          </tr>\r\n        </tbody></table>\r\n      </td>\r\n    </tr>\r\n    <tr>\r\n      <td height=\"60\"></td>\r\n    </tr>\r\n  </tbody></table>', 'hi {{name}}, {{message}}', 'c151cc', '{\"name\":\"php\"}', '{\"clickatell_api_key\":\"----------------------------\",\"infobip_username\":\"--------------\",\"infobip_password\":\"----------------------\",\"message_bird_api_key\":\"-------------------\",\"account_sid\":\"AC67afdacf2dacff5f163134883db92c24\",\"auth_token\":\"77726b242830fb28f52fb08c648dd7a6\",\"from\":\"+17739011523\",\"apiv2_key\":\"dfsfgdfgh\",\"name\":\"clickatell\"}', '{\"name\":\"Our Shop\",\"mobile\":\"88004054545\",\"address\":\"New Yoka\",\"image\":\"6224ad83e6f471646570883.jpg\",\"cover_image\":\"6224ad83f31601646570883.jpg\"}', 0, 1, 0, 1, 0, 0, 1, 1, 'basic', NULL, NULL, '2022-03-07 14:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text_align` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: left to right text align, 1: right to left text align',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: not default language, 1: default language',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchants`
--

CREATE TABLE `merchants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstname` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `balance` decimal(28,8) UNSIGNED NOT NULL DEFAULT 0.00000000,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_links` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avg_rating` decimal(5,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `total_rating` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `review_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `ev` tinyint(4) NOT NULL DEFAULT 0,
  `sv` tinyint(4) NOT NULL DEFAULT 0,
  `ver_code` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ver_code_send_at` datetime NOT NULL,
  `ts` tinyint(4) NOT NULL DEFAULT 0,
  `tv` tinyint(4) NOT NULL DEFAULT 1,
  `tsc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchant_password_resets`
--

CREATE TABLE `merchant_password_resets` (
  `email` varchar(40) DEFAULT NULL,
  `token` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempname` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'template name',
  `secs` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `name`, `slug`, `tempname`, `secs`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'HOME', 'home', 'templates.basic.', '[\"categories\",\"live_auction\",\"upcoming_auction\",\"recently_expired\",\"quick_banner\",\"winner\",\"counter\",\"how_to_bid\",\"testimonial\",\"blog\",\"faq\",\"sponsors\"]', 1, '2020-07-11 06:23:58', '2022-02-28 05:53:04'),
(2, 'About', 'about-us', 'templates.basic.', '[\"counter\",\"winner\",\"how_to_bid\",\"testimonial\",\"sponsors\"]', 0, '2020-07-11 06:35:35', '2022-02-24 05:46:15'),
(4, 'Blog', 'blog', 'templates.basic.', NULL, 1, '2020-10-22 01:14:43', '2022-03-07 11:03:50'),
(5, 'Contact', 'contact', 'templates.basic.', '[\"faq\"]', 1, '2020-10-22 01:14:53', '2022-03-07 11:04:08');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `merchant_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `total_bid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `started_at` datetime DEFAULT NULL,
  `expired_at` datetime DEFAULT NULL,
  `avg_rating` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_rating` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `review_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `image` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `long_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specification` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Pending, 1: Live',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `merchant_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_attachments`
--

CREATE TABLE `support_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `support_message_id` int(10) UNSIGNED NOT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_messages`
--

CREATE TABLE `support_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supportticket_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `admin_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) DEFAULT 0,
  `merchant_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0: Open, 1: Answered, 2: Replied, 3: Closed',
  `priority` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Low, 2 = medium, 3 = heigh',
  `last_reply` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `merchant_id` int(10) UNSIGNED DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `post_balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trx` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstname` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'contains full address',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: banned, 1: active',
  `ev` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: email unverified, 1: email verified',
  `sv` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: sms unverified, 1: sms verified',
  `ver_code` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'stores verification code',
  `ver_code_send_at` datetime DEFAULT NULL COMMENT 'verification send time',
  `ts` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: 2fa off, 1: 2fa on',
  `tv` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0: 2fa unverified, 1: 2fa verified',
  `tsc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_logins`
--

CREATE TABLE `user_logins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `merchant_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_ip` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `winners`
--

CREATE TABLE `winners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `bid_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `product_delivered` tinyint(1) UNSIGNED DEFAULT 0 COMMENT '0:Pending, 1:Send',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `method_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `merchant_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `currency` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `trx` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `final_amount` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `after_charge` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `withdraw_information` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=>success, 2=>pending, 3=>cancel,  ',
  `admin_feedback` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_methods`
--

CREATE TABLE `withdraw_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `min_limit` decimal(28,8) DEFAULT 0.00000000,
  `max_limit` decimal(28,8) NOT NULL DEFAULT 0.00000000,
  `delay` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fixed_charge` decimal(28,8) DEFAULT 0.00000000,
  `rate` decimal(28,8) DEFAULT 0.00000000,
  `percent_charge` decimal(5,2) DEFAULT NULL,
  `currency` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_data` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`,`username`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bids`
--
ALTER TABLE `bids`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_sms_templates`
--
ALTER TABLE `email_sms_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extensions`
--
ALTER TABLE `extensions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `frontends`
--
ALTER TABLE `frontends`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gateways`
--
ALTER TABLE `gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gateway_currencies`
--
ALTER TABLE `gateway_currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_settings`
--
ALTER TABLE `general_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merchants`
--
ALTER TABLE `merchants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_attachments`
--
ALTER TABLE `support_attachments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_messages`
--
ALTER TABLE `support_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`email`);

--
-- Indexes for table `user_logins`
--
ALTER TABLE `user_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `winners`
--
ALTER TABLE `winners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_password_resets`
--
ALTER TABLE `admin_password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `advertisements`
--
ALTER TABLE `advertisements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bids`
--
ALTER TABLE `bids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_sms_templates`
--
ALTER TABLE `email_sms_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `extensions`
--
ALTER TABLE `extensions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `frontends`
--
ALTER TABLE `frontends`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `gateways`
--
ALTER TABLE `gateways`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `gateway_currencies`
--
ALTER TABLE `gateway_currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_settings`
--
ALTER TABLE `general_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `merchants`
--
ALTER TABLE `merchants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_attachments`
--
ALTER TABLE `support_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_messages`
--
ALTER TABLE `support_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_logins`
--
ALTER TABLE `user_logins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `winners`
--
ALTER TABLE `winners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdraw_methods`
--
ALTER TABLE `withdraw_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
