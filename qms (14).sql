-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2026 at 01:25 AM
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
-- Database: `qms`
--

-- --------------------------------------------------------

--
-- Table structure for table `accdatatbl`
--

CREATE TABLE `accdatatbl` (
  `accID` int(11) NOT NULL,
  `fName` varchar(45) NOT NULL,
  `lName` varchar(45) NOT NULL,
  `fullName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `roleID` int(11) NOT NULL DEFAULT 4,
  `dateCreated` datetime DEFAULT current_timestamp(),
  `verificationStatus` enum('unverified','verified') NOT NULL DEFAULT 'unverified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accdatatbl`
--

INSERT INTO `accdatatbl` (`accID`, `fName`, `lName`, `fullName`, `email`, `password`, `roleID`, `dateCreated`, `verificationStatus`) VALUES
(44, 'test', 'last', 'test last', 'joaquind.rizal2@gmail.com', '$2y$10$9yFwDKVVpvve5nO87OFF7OKXi.re.Y16UXJwgo4Tz9C4YSccP/U3a', 4, '2025-04-24 13:27:04', 'verified'),
(45, 'Rizal', 'Joaquin', 'Rizakl Joaquin\r\n', 'joaquind.rizal@gmail.com', '$2y$10$DKLhGV/mfGoUApRG/PlcEeUmYxMCbF2JbS43AMxhk5AjK34eXN1jC', 1, '2025-04-24 14:32:58', 'verified'),
(50, 'Joaquin', 'Rizal', 'Joaquin Rizal', 'joaquind.rizal3@gmail.com', '$2y$10$Arhf09BW18bbjsWLiWFBt..A6L3lxMyhq6A0E0PALT/gbtSJJL94O', 3, '2025-04-29 08:16:04', 'verified'),
(51, 'Christian DDave', 'Trillanes', 'Christian Dave Trillanes - Director', 'cctrillanes@student.apc.edu.ph', '$2y$10$Vj3T4NcyFfMGNCz8Am/Ct.c.gVu05sxFyXaAbMDQzMdkKskySYMoG', 2, '2025-04-29 12:45:58', 'unverified'),
(54, 'Christian Dave', 'Trillanes - Staff', 'Christian Dave Trillanes - QAP', 'yllanex0415@gmail.com', '$2y$10$LwEXF7q6JLW8ZXZ/bA0c8ON1t8UD/ttRrA947jCatxNbcCS96a/Qu', 3, '2025-04-30 12:28:47', 'verified'),
(57, 'Not Christian', 'DOVE?', 'Not Christian DOVE? - STAFF', 'Cedee0415@gmail.com', '$2y$10$PPT5I1WYdUxa86hCwZMrXeeb0NElfGvkXzqVSUA7E5KI3ZM9TOeCO', 4, '2026-03-29 23:31:55', 'unverified'),
(58, 'Itsame', 'Dabe', 'Itsame Dabe - STAFF', 'davetrillanes145@gmail.com', '$2y$10$iatBy12MrwPnpzMlpBg80.1FSGYiFvPICwlcYH1FbVz.HlymiYzDu', 4, '2026-05-23 08:12:35', 'verified');

-- --------------------------------------------------------

--
-- Table structure for table `categorytbl`
--

CREATE TABLE `categorytbl` (
  `categoryID` int(11) NOT NULL,
  `categoryName` varchar(45) NOT NULL,
  `parentCategoryID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorytbl`
--

INSERT INTO `categorytbl` (`categoryID`, `categoryName`, `parentCategoryID`) VALUES
(85, 'SOE', NULL),
(86, 'SOM', NULL),
(87, 'SOMA', NULL),
(92, 'Folder 1.1', 85),
(93, 'Folder 1.2', 85),
(94, 'Folder 2.1', 86),
(101, 'Admin Units', NULL),
(102, 'Logistics', 101),
(103, 'ITRO', 101),
(104, 'Accounting and Finance', 101),
(105, 'Sales and Marketing (Admissions)', 101),
(106, 'Discipline Office', 101),
(107, 'Building and Maintenance Office (BMO)', 101),
(108, 'Clinic', 101),
(109, 'Guidance Office', 101),
(110, 'Registrars Office', 101),
(111, 'Research and Creative Works Office', 101),
(112, 'Career Services Office', 101),
(113, 'Student Services Offices', 101),
(114, 'Human Resources Office', 101),
(115, 'Library', 101);

-- --------------------------------------------------------

--
-- Table structure for table `dorgtbl`
--

CREATE TABLE `dorgtbl` (
  `dptID` int(11) NOT NULL,
  `dptName` varchar(45) NOT NULL,
  `dptParentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dpaagreehistbl`
--

CREATE TABLE `dpaagreehistbl` (
  `agreementID` int(11) NOT NULL,
  `accID` int(11) NOT NULL,
  `dpaID` int(11) NOT NULL,
  `dpaVersion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dpatbl`
--

CREATE TABLE `dpatbl` (
  `dpaID` int(11) NOT NULL,
  `dpaContents` tinytext NOT NULL,
  `dateUploaded` date NOT NULL,
  `dpaVersion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `empperdeptbl`
--

CREATE TABLE `empperdeptbl` (
  `employeeID` int(11) NOT NULL,
  `accID` int(11) NOT NULL,
  `dptID` int(11) NOT NULL,
  `departmentRole` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbackstatustbl`
--

CREATE TABLE `feedbackstatustbl` (
  `fbStatusID` int(11) NOT NULL,
  `fbStatusName` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacktbl`
--

CREATE TABLE `feedbacktbl` (
  `feedbackID` int(11) NOT NULL,
  `remarksOn` int(11) NOT NULL,
  `remarksBy` int(11) NOT NULL,
  `repliedBy` int(11) DEFAULT NULL,
  `fbType` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `replyContent` text DEFAULT NULL,
  `sessionID` int(11) NOT NULL,
  `dateSubmitted` date NOT NULL,
  `dateReplied` datetime DEFAULT NULL,
  `feedbackTblcol` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacktbl`
--

INSERT INTO `feedbacktbl` (`feedbackID`, `remarksOn`, `remarksBy`, `repliedBy`, `fbType`, `content`, `replyContent`, `sessionID`, `dateSubmitted`, `dateReplied`, `feedbackTblcol`) VALUES
(37, 234, 51, NULL, 1, 'Hello', NULL, 0, '2026-06-16', NULL, ''),
(38, 238, 54, NULL, 1, 'Reasons Why this is rejected:\nNo Date', NULL, 0, '2026-06-16', NULL, ''),
(39, 239, 58, NULL, 1, 'Reason this is not accepted:\nNot compatible to current Systems', NULL, 0, '2026-06-16', NULL, ''),
(40, 240, 54, 51, 2, 'Reason for Rejection:\nBudget', 'We have budget', 0, '2026-06-16', '2026-06-17 00:04:57', ''),
(41, 235, 51, NULL, 1, 'Hello', NULL, 0, '2026-06-18', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacktypetbl`
--

CREATE TABLE `feedbacktypetbl` (
  `fbTypeID` int(11) NOT NULL,
  `feedbackTypeName` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notiftbl`
--

CREATE TABLE `notiftbl` (
  `notifID` int(11) NOT NULL,
  `receivedBy` int(11) NOT NULL,
  `message` tinytext NOT NULL,
  `dateTimeSent` datetime NOT NULL DEFAULT current_timestamp(),
  `dateTimeRead` datetime NOT NULL,
  `notifStatus` tinyint(4) DEFAULT 0 COMMENT '0 = Unread, 1 = Read'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notiftbl`
--

INSERT INTO `notiftbl` (`notifID`, `receivedBy`, `message`, `dateTimeSent`, `dateTimeRead`, `notifStatus`) VALUES
(388, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-13 13:45:35', '0000-00-00 00:00:00', 1),
(389, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-13 13:45:47', '0000-00-00 00:00:00', 1),
(390, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-13 13:45:48', '0000-00-00 00:00:00', 1),
(391, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-13 13:49:28', '0000-00-00 00:00:00', 1),
(392, 51, 'Your policy \'LoI IA Visit_RCW_Signed (1)\' has been published!', '2026-06-13 13:51:40', '0000-00-00 00:00:00', 1),
(393, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-13 13:52:41', '0000-00-00 00:00:00', 1),
(394, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (2)\'.', '2026-06-13 13:59:52', '0000-00-00 00:00:00', 1),
(395, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (2)\'.', '2026-06-13 13:59:52', '0000-00-00 00:00:00', 1),
(396, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-13 14:00:15', '0000-00-00 00:00:00', 1),
(397, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-13 14:06:17', '0000-00-00 00:00:00', 1),
(398, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (3)\'.', '2026-06-13 14:06:30', '0000-00-00 00:00:00', 1),
(399, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (3)\'.', '2026-06-13 14:06:30', '0000-00-00 00:00:00', 1),
(400, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-13 14:07:02', '0000-00-00 00:00:00', 1),
(401, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-13 14:25:08', '0000-00-00 00:00:00', 1),
(402, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (4)\'.', '2026-06-13 14:25:19', '0000-00-00 00:00:00', 1),
(403, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (4)\'.', '2026-06-13 14:25:19', '0000-00-00 00:00:00', 1),
(404, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-13 14:25:30', '0000-00-00 00:00:00', 1),
(405, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-13 15:27:33', '0000-00-00 00:00:00', 1),
(406, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (5)\'.', '2026-06-13 15:27:50', '0000-00-00 00:00:00', 1),
(407, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (5)\'.', '2026-06-13 15:27:50', '0000-00-00 00:00:00', 1),
(408, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-13 15:28:00', '0000-00-00 00:00:00', 1),
(409, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-13 15:29:24', '0000-00-00 00:00:00', 1),
(410, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (6)\'.', '2026-06-13 15:30:00', '0000-00-00 00:00:00', 1),
(411, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (6)\'.', '2026-06-13 15:30:00', '0000-00-00 00:00:00', 1),
(412, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-13 15:30:21', '0000-00-00 00:00:00', 1),
(413, 54, 'Folder moved: Folder 3', '2026-06-13 15:38:08', '0000-00-00 00:00:00', 1),
(414, 54, 'Folder moved: Folder 3', '2026-06-13 15:38:11', '0000-00-00 00:00:00', 1),
(415, 51, 'New feedback on your policy \'LoI IA Visit_RCW_Signed (6)\': Ello', '2026-06-13 23:40:40', '0000-00-00 00:00:00', 1),
(416, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 14:05:13', '0000-00-00 00:00:00', 1),
(417, 58, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (7)\'.', '2026-06-14 14:05:36', '0000-00-00 00:00:00', 0),
(418, 51, 'You successfully assigned Itsame Dabe - STAFF to the document: \'LoI IA Visit_RCW_Signed (7)\'.', '2026-06-14 14:05:36', '0000-00-00 00:00:00', 1),
(419, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 14:06:05', '0000-00-00 00:00:00', 1),
(420, 51, 'Your policy \'LoI IA Visit_RCW_Signed (8)\' has been published!', '2026-06-14 14:45:36', '0000-00-00 00:00:00', 1),
(421, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 14:58:45', '0000-00-00 00:00:00', 1),
(422, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-14 15:05:41', '0000-00-00 00:00:00', 1),
(423, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-14 15:05:41', '0000-00-00 00:00:00', 1),
(424, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:06:02', '0000-00-00 00:00:00', 1),
(425, 54, 'Document moved: LoI IA Visit_RCW_Signed (', '2026-06-14 15:06:36', '0000-00-00 00:00:00', 1),
(426, 54, 'Document moved: LoI IA Visit_RCW_Signed (', '2026-06-14 15:06:38', '0000-00-00 00:00:00', 1),
(427, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Reviewed!', '2026-06-14 15:11:52', '0000-00-00 00:00:00', 1),
(428, 54, 'You have been assigned as a Verifier for the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5\'.', '2026-06-14 15:12:30', '0000-00-00 00:00:00', 1),
(429, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5\'.', '2026-06-14 15:12:30', '0000-00-00 00:00:00', 1),
(430, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Verified!', '2026-06-14 15:12:42', '0000-00-00 00:00:00', 1),
(431, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5\' has been published!', '2026-06-14 15:12:59', '0000-00-00 00:00:00', 1),
(432, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 15:14:08', '0000-00-00 00:00:00', 1),
(433, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (2)\'.', '2026-06-14 15:14:23', '0000-00-00 00:00:00', 1),
(434, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (2)\'.', '2026-06-14 15:14:23', '0000-00-00 00:00:00', 1),
(435, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:14:32', '0000-00-00 00:00:00', 1),
(436, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 15:16:28', '0000-00-00 00:00:00', 1),
(437, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.2)\'.', '2026-06-14 15:16:41', '0000-00-00 00:00:00', 1),
(438, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1.2)\'.', '2026-06-14 15:16:41', '0000-00-00 00:00:00', 1),
(439, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:16:56', '0000-00-00 00:00:00', 1),
(440, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 15:18:36', '0000-00-00 00:00:00', 1),
(441, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.3)\'.', '2026-06-14 15:18:43', '0000-00-00 00:00:00', 1),
(442, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1.3)\'.', '2026-06-14 15:18:43', '0000-00-00 00:00:00', 1),
(443, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:19:05', '0000-00-00 00:00:00', 1),
(444, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 15:29:49', '0000-00-00 00:00:00', 1),
(445, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-14 15:30:03', '0000-00-00 00:00:00', 1),
(446, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-14 15:30:03', '0000-00-00 00:00:00', 1),
(447, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:30:23', '0000-00-00 00:00:00', 1),
(448, 54, 'Document moved: LoI IA Visit_RCW_Signed (', '2026-06-14 15:30:44', '0000-00-00 00:00:00', 1),
(449, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 15:34:48', '0000-00-00 00:00:00', 1),
(450, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.1)\'.', '2026-06-14 15:35:32', '0000-00-00 00:00:00', 1),
(451, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1.1)\'.', '2026-06-14 15:35:32', '0000-00-00 00:00:00', 1),
(452, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:35:45', '0000-00-00 00:00:00', 1),
(453, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 15:40:02', '0000-00-00 00:00:00', 1),
(454, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.2)\'.', '2026-06-14 15:40:09', '0000-00-00 00:00:00', 1),
(455, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1.2)\'.', '2026-06-14 15:40:09', '0000-00-00 00:00:00', 1),
(456, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:40:14', '0000-00-00 00:00:00', 1),
(457, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 15:43:00', '0000-00-00 00:00:00', 1),
(458, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.3)\'.', '2026-06-14 15:43:10', '0000-00-00 00:00:00', 1),
(459, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1.3)\'.', '2026-06-14 15:43:10', '0000-00-00 00:00:00', 1),
(460, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:43:19', '0000-00-00 00:00:00', 1),
(461, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 15:45:37', '0000-00-00 00:00:00', 1),
(462, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (2)\'.', '2026-06-14 15:45:44', '0000-00-00 00:00:00', 1),
(463, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (2)\'.', '2026-06-14 15:45:44', '0000-00-00 00:00:00', 1),
(464, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:46:49', '0000-00-00 00:00:00', 1),
(465, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 15:58:30', '0000-00-00 00:00:00', 1),
(466, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (2.1)\'.', '2026-06-14 15:58:37', '0000-00-00 00:00:00', 1),
(467, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (2.1)\'.', '2026-06-14 15:58:37', '0000-00-00 00:00:00', 1),
(468, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 15:58:43', '0000-00-00 00:00:00', 1),
(469, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 16:44:03', '0000-00-00 00:00:00', 1),
(470, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (2.2)\'.', '2026-06-14 16:44:18', '0000-00-00 00:00:00', 1),
(471, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (2.2)\'.', '2026-06-14 16:44:18', '0000-00-00 00:00:00', 1),
(472, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 16:44:39', '0000-00-00 00:00:00', 1),
(473, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 17:03:19', '0000-00-00 00:00:00', 1),
(474, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-14 17:03:35', '0000-00-00 00:00:00', 1),
(475, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-14 17:03:35', '0000-00-00 00:00:00', 1),
(476, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 17:03:41', '0000-00-00 00:00:00', 1),
(477, 54, 'Document moved: LoI IA Visit_RCW_Signed (', '2026-06-14 17:04:02', '0000-00-00 00:00:00', 1),
(478, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Reviewed!', '2026-06-14 17:04:18', '0000-00-00 00:00:00', 1),
(479, 54, 'You have been assigned as a Verifier for the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5\'.', '2026-06-14 17:04:39', '0000-00-00 00:00:00', 1),
(480, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5\'.', '2026-06-14 17:04:39', '0000-00-00 00:00:00', 1),
(481, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Verified!', '2026-06-14 17:04:49', '0000-00-00 00:00:00', 1),
(482, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5\' has been published!', '2026-06-14 17:05:07', '0000-00-00 00:00:00', 1),
(483, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 17:07:25', '0000-00-00 00:00:00', 1),
(484, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.1)\'.', '2026-06-14 17:10:10', '0000-00-00 00:00:00', 1),
(485, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1.1)\'.', '2026-06-14 17:10:10', '0000-00-00 00:00:00', 1),
(486, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 17:10:18', '0000-00-00 00:00:00', 1),
(487, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-14 17:11:16', '0000-00-00 00:00:00', 1),
(488, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.2)\'.', '2026-06-14 17:11:25', '0000-00-00 00:00:00', 1),
(489, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1.2)\'.', '2026-06-14 17:11:25', '0000-00-00 00:00:00', 1),
(490, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-14 17:12:17', '0000-00-00 00:00:00', 1),
(491, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Reviewed!', '2026-06-14 17:51:06', '0000-00-00 00:00:00', 1),
(492, 54, 'You have been assigned as a Verifier for the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5.1\'.', '2026-06-14 17:51:33', '0000-00-00 00:00:00', 1),
(493, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5.1\'.', '2026-06-14 17:51:33', '0000-00-00 00:00:00', 1),
(494, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Verified!', '2026-06-14 17:51:48', '0000-00-00 00:00:00', 1),
(495, 51, 'Your policy \'SAMPLE Change Reques...\' was Reviewed!', '2026-06-14 21:03:48', '0000-00-00 00:00:00', 1),
(496, 54, 'You have been assigned as a Verifier for the document: \'SAMPLE Change Request Form\'.', '2026-06-14 21:04:06', '0000-00-00 00:00:00', 1),
(497, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'SAMPLE Change Request Form\'.', '2026-06-14 21:04:06', '0000-00-00 00:00:00', 1),
(498, 51, 'Your policy \'bgagtrfwav...\' was Reviewed!', '2026-06-14 21:08:03', '0000-00-00 00:00:00', 1),
(499, 51, 'Your policy \'SAMPLE Change Reques...\' was Verified!', '2026-06-14 21:08:13', '0000-00-00 00:00:00', 1),
(500, 54, 'You have been assigned as a Verifier for the document: \'bgagtrfwav\'.', '2026-06-14 21:08:52', '0000-00-00 00:00:00', 1),
(501, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'bgagtrfwav\'.', '2026-06-14 21:08:52', '0000-00-00 00:00:00', 1),
(502, 54, 'You have been assigned as a Verifier for the document: \'bgagtrfwav\'.', '2026-06-14 21:12:07', '0000-00-00 00:00:00', 1),
(503, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'bgagtrfwav\'.', '2026-06-14 21:12:07', '0000-00-00 00:00:00', 1),
(504, 51, 'Your policy \'bgagtrfwav...\' was Verified!', '2026-06-14 23:28:44', '0000-00-00 00:00:00', 1),
(505, 51, 'Your policy \'SAMPLE Change Request Form\' has been published!', '2026-06-14 23:29:04', '0000-00-00 00:00:00', 1),
(506, 50, 'Renamed folder: \'Folder 1\' to \'SOE\'', '2026-06-15 12:11:22', '0000-00-00 00:00:00', 0),
(507, 54, 'Renamed folder: \'Folder 1\' to \'SOE\'', '2026-06-15 12:11:22', '0000-00-00 00:00:00', 1),
(508, 50, 'Renamed folder: \'Folder 2\' to \'ASDA\'', '2026-06-15 12:21:00', '0000-00-00 00:00:00', 0),
(509, 54, 'Renamed folder: \'Folder 2\' to \'ASDA\'', '2026-06-15 12:21:00', '0000-00-00 00:00:00', 1),
(510, 50, 'Renamed folder: \'Folder 3\' to \'ASDFAAW\'', '2026-06-15 12:21:02', '0000-00-00 00:00:00', 0),
(511, 54, 'Renamed folder: \'Folder 3\' to \'ASDFAAW\'', '2026-06-15 12:21:02', '0000-00-00 00:00:00', 1),
(512, 51, 'New feedback on your policy \'LoI IA Visit_RCW_Signed (1.2)\': Hello', '2026-06-15 14:24:44', '0000-00-00 00:00:00', 1),
(513, 50, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1)', '2026-06-16 16:25:03', '0000-00-00 00:00:00', 0),
(514, 54, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1)', '2026-06-16 16:25:03', '0000-00-00 00:00:00', 1),
(515, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-16 16:26:32', '0000-00-00 00:00:00', 1),
(516, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-16 16:26:49', '0000-00-00 00:00:00', 1),
(517, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1)\'.', '2026-06-16 16:26:49', '0000-00-00 00:00:00', 1),
(518, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-16 16:26:58', '0000-00-00 00:00:00', 1),
(519, 51, 'Your policy \'LoI IA Visit_RCW_Signed (1)\' has been published!', '2026-06-16 16:33:18', '0000-00-00 00:00:00', 1),
(520, 51, 'New feedback on your policy \'LoI IA Visit_RCW_Signed (1)\': Hello', '2026-06-16 16:34:22', '0000-00-00 00:00:00', 1),
(521, 50, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (2)', '2026-06-16 16:35:12', '0000-00-00 00:00:00', 0),
(522, 54, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (2)', '2026-06-16 16:35:12', '0000-00-00 00:00:00', 1),
(523, 50, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5', '2026-06-16 16:35:20', '0000-00-00 00:00:00', 0),
(524, 54, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5', '2026-06-16 16:35:20', '0000-00-00 00:00:00', 1),
(525, 50, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1)', '2026-06-16 16:37:29', '0000-00-00 00:00:00', 0),
(526, 54, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1)', '2026-06-16 16:37:29', '0000-00-00 00:00:00', 1),
(527, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Reviewed!', '2026-06-16 16:37:56', '0000-00-00 00:00:00', 1),
(528, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Reviewed!', '2026-06-16 16:38:03', '0000-00-00 00:00:00', 1),
(529, 54, 'You have been assigned as a Verifier for the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1)\'.', '2026-06-16 16:38:36', '0000-00-00 00:00:00', 1),
(530, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1)\'.', '2026-06-16 16:38:36', '0000-00-00 00:00:00', 1),
(531, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Verified!', '2026-06-16 16:39:14', '0000-00-00 00:00:00', 1),
(532, 50, 'Renamed folder: \'ASDA\' to \'SOM\'', '2026-06-16 16:40:42', '0000-00-00 00:00:00', 0),
(533, 54, 'Renamed folder: \'ASDA\' to \'SOM\'', '2026-06-16 16:40:42', '0000-00-00 00:00:00', 1),
(534, 50, 'Renamed folder: \'ASDFAAW\' to \'SOMA\'', '2026-06-16 16:40:48', '0000-00-00 00:00:00', 0),
(535, 54, 'Renamed folder: \'ASDFAAW\' to \'SOMA\'', '2026-06-16 16:40:48', '0000-00-00 00:00:00', 1),
(536, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-16 16:41:04', '0000-00-00 00:00:00', 1),
(537, 54, 'You have been assigned as a Verifier for the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5\'.', '2026-06-16 16:41:21', '0000-00-00 00:00:00', 1),
(538, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5\'.', '2026-06-16 16:41:21', '0000-00-00 00:00:00', 1),
(539, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (2)\'.', '2026-06-16 16:41:29', '0000-00-00 00:00:00', 1),
(540, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (2)\'.', '2026-06-16 16:41:29', '0000-00-00 00:00:00', 1),
(541, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1)\' has been published!', '2026-06-16 16:41:37', '0000-00-00 00:00:00', 1),
(542, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-16 16:44:38', '0000-00-00 00:00:00', 1),
(543, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Verified!', '2026-06-16 16:44:51', '0000-00-00 00:00:00', 1),
(544, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5\' has been published!', '2026-06-16 17:06:07', '0000-00-00 00:00:00', 1),
(545, 51, 'Your policy \'LoI IA Visit_RCW_Signed (2)\' has been published!', '2026-06-16 17:06:28', '0000-00-00 00:00:00', 1),
(546, 50, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1.2)', '2026-06-16 17:24:33', '0000-00-00 00:00:00', 0),
(547, 54, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1.2)', '2026-06-16 17:24:33', '0000-00-00 00:00:00', 1),
(548, 50, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1.3)', '2026-06-16 17:24:54', '0000-00-00 00:00:00', 0),
(549, 54, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1.3)', '2026-06-16 17:24:54', '0000-00-00 00:00:00', 1),
(550, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-16 17:25:43', '0000-00-00 00:00:00', 1),
(551, 51, 'Your document \'LoI IA Visit_RCW_Signed (1.2)\' was returned. Reason: Reasons Why this is rejected:\nNo Date', '2026-06-16 17:26:08', '0000-00-00 00:00:00', 1),
(552, 58, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.3)\'.', '2026-06-16 17:26:41', '0000-00-00 00:00:00', 0),
(553, 51, 'You successfully assigned Itsame Dabe - STAFF to the document: \'LoI IA Visit_RCW_Signed (1.3)\'.', '2026-06-16 17:26:41', '0000-00-00 00:00:00', 1),
(554, 51, 'Your document \'LoI IA Visit_RCW_Signed (1.3)\' was returned. Reason: Reason this is not accepted:\nNot compatible to current Systems', '2026-06-16 17:27:14', '0000-00-00 00:00:00', 1),
(555, 50, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 2', '2026-06-16 17:35:04', '0000-00-00 00:00:00', 0),
(556, 54, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 2', '2026-06-16 17:35:04', '0000-00-00 00:00:00', 1),
(557, 51, 'Your document \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 2\' was returned. Reason: Reason for Rejection:\nBudget', '2026-06-16 17:35:39', '0000-00-00 00:00:00', 1),
(558, 50, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1.1)', '2026-06-16 18:17:07', '0000-00-00 00:00:00', 0),
(559, 54, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1.1)', '2026-06-16 18:17:07', '0000-00-00 00:00:00', 1),
(560, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-16 18:17:30', '0000-00-00 00:00:00', 1),
(561, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.1)\'.', '2026-06-16 18:17:43', '0000-00-00 00:00:00', 1),
(562, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1.1)\'.', '2026-06-16 18:17:43', '0000-00-00 00:00:00', 1),
(563, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-16 18:17:55', '0000-00-00 00:00:00', 1),
(564, 50, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1.4)', '2026-06-16 18:19:11', '0000-00-00 00:00:00', 0),
(565, 54, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1.4)', '2026-06-16 18:19:11', '0000-00-00 00:00:00', 1),
(566, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Reviewed!', '2026-06-16 18:19:27', '0000-00-00 00:00:00', 1),
(567, 54, 'You have been assigned as a Verifier for the document: \'LoI IA Visit_RCW_Signed (1.4)\'.', '2026-06-16 18:20:35', '0000-00-00 00:00:00', 1),
(568, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'LoI IA Visit_RCW_Signed (1.4)\'.', '2026-06-16 18:20:35', '0000-00-00 00:00:00', 1),
(569, 51, 'Your policy \'LoI IA Visit_RCW_Sig...\' was Verified!', '2026-06-16 18:23:11', '0000-00-00 00:00:00', 1),
(570, 50, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.1)', '2026-06-16 18:29:30', '0000-00-00 00:00:00', 0),
(571, 54, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.1)', '2026-06-16 18:29:30', '0000-00-00 00:00:00', 1),
(572, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Reviewed!', '2026-06-16 18:29:57', '0000-00-00 00:00:00', 1),
(573, 54, 'You have been assigned as a Verifier for the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.1)\'.', '2026-06-16 18:30:52', '0000-00-00 00:00:00', 0),
(574, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.1)\'.', '2026-06-16 18:30:52', '0000-00-00 00:00:00', 1),
(575, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Verified!', '2026-06-16 18:31:05', '0000-00-00 00:00:00', 1),
(576, 50, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.2)', '2026-06-16 18:34:39', '0000-00-00 00:00:00', 0),
(577, 54, 'A new policy is pending your review: SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.2)', '2026-06-16 18:34:39', '0000-00-00 00:00:00', 0),
(578, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Reviewed!', '2026-06-16 18:34:47', '0000-00-00 00:00:00', 1),
(579, 54, 'You have been assigned as a Verifier for the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.2)\'.', '2026-06-16 18:34:56', '0000-00-00 00:00:00', 0),
(580, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.2)\'.', '2026-06-16 18:34:56', '0000-00-00 00:00:00', 1),
(581, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Verified!', '2026-06-16 18:35:06', '0000-00-00 00:00:00', 1),
(582, 54, 'You have been assigned as a Verifier for the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.2)\'.', '2026-06-16 18:35:16', '0000-00-00 00:00:00', 0),
(583, 51, 'You successfully assigned Christian Dave Trillanes - QAP to the document: \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.2)\'.', '2026-06-16 18:35:16', '0000-00-00 00:00:00', 1),
(584, 51, 'Your policy \'SAMPLE_1.0 OPE-GEN-E...\' was Approved!', '2026-06-16 18:35:44', '0000-00-00 00:00:00', 1),
(585, 54, 'Your feedback on \'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 2\' has been addressed.', '2026-06-17 00:04:57', '0000-00-00 00:00:00', 0),
(586, 51, 'New feedback on your policy \'LoI IA Visit_RCW_Signed (2)\': Hello', '2026-06-18 12:14:10', '0000-00-00 00:00:00', 1),
(587, 50, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1)3', '2026-06-18 13:59:17', '0000-00-00 00:00:00', 0),
(588, 54, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (1)3', '2026-06-18 13:59:17', '0000-00-00 00:00:00', 0),
(589, 50, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (2.1)', '2026-06-18 15:50:25', '0000-00-00 00:00:00', 1),
(590, 54, 'A new policy is pending your review: LoI IA Visit_RCW_Signed (2.1)', '2026-06-18 15:50:25', '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `otptbl`
--

CREATE TABLE `otptbl` (
  `OTPID` int(11) NOT NULL,
  `requestBy` int(11) NOT NULL,
  `PIN` char(6) NOT NULL,
  `isUsedFor` int(11) NOT NULL COMMENT '0 = ''Account Verification'', 1 = ''Password Reset''',
  `otpCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `otpExpires` timestamp NOT NULL DEFAULT (current_timestamp() + interval 10 minute),
  `otpStatus` enum('unused','used','expired') NOT NULL DEFAULT 'unused'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otptbl`
--

INSERT INTO `otptbl` (`OTPID`, `requestBy`, `PIN`, `isUsedFor`, `otpCreated`, `otpExpires`, `otpStatus`) VALUES
(38, 44, '364401', 0, '2025-04-24 05:27:04', '2025-04-24 05:37:04', 'used'),
(39, 45, '550858', 0, '2025-04-24 06:32:58', '2025-04-24 06:42:58', 'used'),
(40, 45, '794597', 1, '2025-04-24 07:39:18', '2025-04-24 07:49:18', 'expired'),
(41, 45, '622409', 1, '2025-04-24 07:43:46', '2025-04-24 07:53:46', 'expired'),
(42, 45, '665646', 1, '2025-04-24 07:47:06', '2025-04-24 07:57:06', 'used'),
(43, 45, '659394', 1, '2025-04-24 07:48:57', '2025-04-24 07:58:57', 'used'),
(44, 45, '572676', 1, '2025-04-25 04:54:30', '2025-04-25 05:04:30', 'expired'),
(45, 45, '560483', 1, '2025-04-25 04:54:37', '2025-04-25 05:04:37', 'used'),
(47, 45, '987589', 1, '2025-04-25 11:59:42', '2025-04-25 12:09:42', 'used'),
(48, 45, '750862', 1, '2025-04-25 13:15:25', '2025-04-25 13:25:25', 'used'),
(49, 45, '978666', 1, '2025-04-26 02:38:51', '2025-04-26 02:48:51', 'used'),
(50, 45, '174849', 1, '2025-04-26 04:05:32', '2025-04-26 04:15:32', 'used'),
(51, 45, '693145', 1, '2025-04-26 04:16:45', '2025-04-26 04:26:45', 'used'),
(54, 44, '488397', 1, '2025-04-27 14:35:34', '2025-04-27 14:45:34', 'used'),
(55, 44, '792251', 1, '2025-04-27 14:37:34', '2025-04-27 14:47:34', 'used'),
(57, 50, '156097', 0, '2025-04-29 00:16:04', '2025-04-29 00:26:04', 'used'),
(58, 50, '556103', 1, '2025-04-29 00:44:07', '2025-04-29 00:54:07', 'used'),
(59, 50, '864706', 1, '2025-04-29 01:27:06', '2025-04-29 01:37:06', 'used'),
(60, 45, '103699', 1, '2025-04-29 04:04:21', '2025-04-29 04:14:21', 'used'),
(61, 45, '695133', 1, '2025-04-29 04:08:53', '2025-04-29 04:18:53', 'unused'),
(62, 45, '345245', 1, '2025-04-29 04:08:59', '2025-04-29 04:18:59', 'used'),
(63, 51, '688279', 0, '2025-04-29 04:45:58', '2025-04-29 04:55:58', 'unused'),
(66, 54, '855559', 0, '2025-04-30 04:28:47', '2025-04-30 04:38:47', 'used'),
(67, 57, '801347', 0, '2026-03-29 15:31:55', '2026-03-29 15:41:55', 'unused'),
(68, 58, '634270', 0, '2026-05-23 00:12:35', '2026-05-23 00:22:35', 'used'),
(69, 45, '258898', 1, '2026-05-26 06:24:47', '2026-05-26 06:34:47', 'used'),
(70, 50, '732577', 1, '2026-05-26 06:27:32', '2026-05-26 06:37:32', 'unused'),
(71, 50, '781301', 1, '2026-05-26 06:28:28', '2026-05-26 06:38:28', 'used');

-- --------------------------------------------------------

--
-- Table structure for table `policylogstbl`
--

CREATE TABLE `policylogstbl` (
  `policyLogID` int(11) NOT NULL,
  `policyID` int(11) DEFAULT NULL,
  `accID` int(11) DEFAULT NULL,
  `statusID` int(11) DEFAULT NULL,
  `logDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `policyrevisiontbl`
--

CREATE TABLE `policyrevisiontbl` (
  `revisionID` int(11) NOT NULL,
  `originalPolicyID` int(11) NOT NULL COMMENT 'The master/original policy this revision belongs to → policytbl.policyID',
  `revisedPolicyID` int(11) NOT NULL COMMENT 'The new policytbl row created for this revision → policytbl.policyID',
  `versionNo` varchar(20) NOT NULL DEFAULT '1.0' COMMENT 'Version label e.g. 1.1, 2.0',
  `revisionType` enum('minor','major') NOT NULL DEFAULT 'minor',
  `revisionFormPath` varchar(500) DEFAULT NULL COMMENT 'Path to the uploaded Change Request / Revision Form PDF',
  `changesDescription` text DEFAULT NULL COMMENT 'Free-text summary of what changed',
  `submittedBy` int(11) NOT NULL COMMENT '→ accdatatbl.accID',
  `dateSubmitted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Links every revision submission to its original policy and stores the revision form path';

-- --------------------------------------------------------

--
-- Table structure for table `policystatus`
--

CREATE TABLE `policystatus` (
  `policyStatusID` int(11) NOT NULL,
  `policyStatusName` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policystatus`
--

INSERT INTO `policystatus` (`policyStatusID`, `policyStatusName`) VALUES
(1, 'Pending'),
(2, 'Reviewed'),
(3, 'Verified'),
(4, 'Approved'),
(5, 'Uploaded'),
(6, 'Rejected'),
(7, 'Archived');

-- --------------------------------------------------------

--
-- Table structure for table `policytbl`
--

CREATE TABLE `policytbl` (
  `policyID` int(11) NOT NULL,
  `title` varchar(45) NOT NULL,
  `contentPath` varchar(255) NOT NULL,
  `categoryID` int(11),
  `versionNo` varchar(20) DEFAULT NULL,
  `policyAuthor` int(11) NOT NULL,
  `dateSubmitted` datetime NOT NULL DEFAULT current_timestamp(),
  `policyReviewer` int(11) DEFAULT NULL,
  `dateReviewed` datetime DEFAULT NULL,
  `policyVerifier` int(11) DEFAULT NULL,
  `dateVerified` datetime DEFAULT NULL,
  `policyApprover` int(11) DEFAULT NULL,
  `dateApproved` datetime DEFAULT NULL,
  `dateUploaded` datetime DEFAULT NULL,
  `policyStatusID` int(11) NOT NULL DEFAULT 1,
  `templateID` int(11) DEFAULT NULL,
  `reviewedBy` int(11) DEFAULT NULL,
  `verifiedBy` int(11) DEFAULT NULL,
  `approvedBy` int(11) DEFAULT NULL,
  `requestChangeContentPath` varchar(255) DEFAULT NULL,
  `originalPolicyID` int(11) DEFAULT NULL,
  `dateRejection` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policytbl`
--

INSERT INTO `policytbl` (`policyID`, `title`, `contentPath`, `categoryID`, `versionNo`, `policyAuthor`, `dateSubmitted`, `policyReviewer`, `dateReviewed`, `policyVerifier`, `dateVerified`, `policyApprover`, `dateApproved`, `dateUploaded`, `policyStatusID`, `templateID`, `reviewedBy`, `verifiedBy`, `approvedBy`, `requestChangeContentPath`, `originalPolicyID`, `dateRejection`) VALUES
(234, 'LoI IA Visit_RCW_Signed (1)', '/qms_optiqual/files/1781598303_LoI_IA_Visit_RCW_Signed_(1).pdf', 92, '1.0', 51, '2026-06-16 16:25:03', 54, '2026-06-16 16:26:32', 54, '2026-06-16 16:26:58', 51, '2026-06-16 16:33:06', '2026-06-16 16:33:18', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(235, 'LoI IA Visit_RCW_Signed (2)', '/qms_optiqual/files/1781598912_LoI_IA_Visit_RCW_Signed_(1).pdf', 92, '1.0', 51, '2026-06-16 16:35:12', 54, '2026-06-16 16:41:04', 54, '2026-06-16 16:44:38', 51, '2026-06-16 16:45:04', '2026-06-16 17:06:28', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(236, 'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5', '/qms_optiqual/files/1781598920_SAMPLE_1.0_OPE-GEN-EMP-QPMINTRO_v5.pdf', 93, '1.0', 51, '2026-06-16 16:35:20', 54, '2026-06-16 16:38:03', 54, '2026-06-16 16:44:51', 51, '2026-06-16 16:45:00', '2026-06-16 17:06:07', 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(237, 'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1)', '/qms_optiqual/files/1781599049_SAMPLE_1.0_OPE-GEN-EMP-QPMINTRO_v5.pdf', 93, '1.0', 51, '2026-06-16 16:37:29', 54, '2026-06-16 16:37:56', 54, '2026-06-16 16:39:14', 51, '2026-06-16 16:41:10', '2026-06-16 16:41:37', 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(238, 'LoI IA Visit_RCW_Signed (1.2)', '/qms_optiqual/files/1781601873_LoI_IA_Visit_RCW_Signed_(1).pdf', NULL, '1.0', 51, '2026-06-16 17:24:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(239, 'LoI IA Visit_RCW_Signed (1.3)', '/qms_optiqual/files/1781601894_LoI_IA_Visit_RCW_Signed_(1).pdf', NULL, '1.0', 51, '2026-06-16 17:24:54', NULL, '2026-06-16 17:25:43', NULL, NULL, NULL, NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(240, 'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 2', '/qms_optiqual/files/1781602504_SAMPLE_1.0_OPE-GEN-EMP-QPMINTRO_v5.pdf', NULL, '1.0', 51, '2026-06-16 17:35:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(241, 'LoI IA Visit_RCW_Signed (1.1)', '/qms_optiqual/files/1781605027_LoI_IA_Visit_RCW_Signed_(1).pdf', 92, '1.1', 51, '2026-06-16 18:17:07', 54, '2026-06-16 18:17:30', 54, '2026-06-16 18:17:55', 51, '2026-06-16 18:18:19', '2026-06-16 18:18:19', 7, NULL, NULL, NULL, NULL, '/qms_optiqual/files/logs/LOG_1781605027_SAMPLE_Change_Request_Form.pdf', 234, NULL),
(242, 'LoI IA Visit_RCW_Signed (1.4)', '/qms_optiqual/files/1781605150_LoI_IA_Visit_RCW_Signed_(1).pdf', 92, '1.2', 51, '2026-06-16 18:19:10', 54, '2026-06-16 18:19:27', 54, '2026-06-16 18:23:11', 51, '2026-06-16 18:23:25', '2026-06-16 18:23:25', 5, NULL, NULL, NULL, NULL, '/qms_optiqual/files/logs/LOG_1781605150_SAMPLE_Change_Request_Form.pdf', 234, NULL),
(243, 'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.1)', '/qms_optiqual/files/1781605770_SAMPLE_1.0_OPE-GEN-EMP-QPMINTRO_v5.pdf', 93, '1.1', 51, '2026-06-16 18:29:30', 54, '2026-06-16 18:29:57', 54, '2026-06-16 18:31:05', 51, '2026-06-16 18:32:17', '2026-06-16 18:32:17', 7, NULL, NULL, NULL, NULL, '/qms_optiqual/files/logs/LOG_1781605770_SAMPLE_Change_Request_Form.pdf', 237, NULL),
(244, 'SAMPLE_1.0 OPE-GEN-EMP-QPMINTRO_v5 (1.2)', '/qms_optiqual/files/1781606079_SAMPLE_1.0_OPE-GEN-EMP-QPMINTRO_v5.pdf', 93, '1.2', 51, '2026-06-16 18:34:39', 54, '2026-06-16 18:34:47', 54, '2026-06-16 18:35:06', 54, '2026-06-16 18:35:44', '2026-06-16 18:35:44', 5, NULL, NULL, NULL, NULL, '/qms_optiqual/files/logs/LOG_1781606079_SAMPLE_Change_Request_Form.pdf', 237, NULL),
(245, 'LoI IA Visit_RCW_Signed (1)3', '/qms_optiqual/files/1781762357_LoI_IA_Visit_RCW_Signed_(1).pdf', NULL, '1.0', 51, '2026-06-18 13:59:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(246, 'LoI IA Visit_RCW_Signed (2.1)', '/qms_optiqual/files/1781769025_LoI_IA_Visit_RCW_Signed_(1).pdf', NULL, '1.1', 51, '2026-06-18 15:50:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '/qms_optiqual/files/logs/LOG_1781769025_SAMPLE_Change_Request_Form.pdf', 235, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `polviewtbl`
--

CREATE TABLE `polviewtbl` (
  `logID` int(11) NOT NULL,
  `policyID` int(11) NOT NULL,
  `viewedBy` int(11) NOT NULL,
  `dateViewed` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `revisionhistorytbl`
--

CREATE TABLE `revisionhistorytbl` (
  `revisionID` int(11) NOT NULL,
  `originalPolicyID` int(11) DEFAULT NULL,
  `currentPolicyID` int(11) DEFAULT NULL,
  `versionNo` varchar(20) DEFAULT NULL,
  `revisionType` varchar(20) DEFAULT NULL,
  `revisionFormPath` varchar(255) DEFAULT NULL,
  `changeDescription` text DEFAULT NULL,
  `revisedBy` int(11) DEFAULT NULL,
  `dateRevised` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `revisionhistorytbl`
--

INSERT INTO `revisionhistorytbl` (`revisionID`, `originalPolicyID`, `currentPolicyID`, `versionNo`, `revisionType`, `revisionFormPath`, `changeDescription`, `revisedBy`, `dateRevised`) VALUES
(19, 197, 198, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1780467335_SAMPLE_Change_Request_Form.pdf', 'First Revision', 51, '2026-06-03'),
(20, 197, 199, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1780467460_SAMPLE_Change_Request_Form.pdf', '2nd Revision', 51, '2026-06-03'),
(21, 206, 207, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781329946_SAMPLE_Change_Request_Form.pdf', 'Revision 1', 51, '2026-06-13'),
(22, 206, 208, '1.1', 'minor', NULL, 'Revision 2', 51, '2026-06-13'),
(23, 206, 209, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781331890_SAMPLE_Change_Request_Form.pdf', 'Minor Revision', 51, '2026-06-13'),
(24, 206, 210, '2.0', 'major', '/qms_optiqual/files/logs/LOG_1781335642_SAMPLE_Change_Request_Form.pdf', '21e13', 51, '2026-06-13'),
(25, 206, 211, '2.1', 'minor', '/qms_optiqual/files/logs/LOG_1781335745_SAMPLE_Change_Request_Form.pdf', 'wqeewqe', 51, '2026-06-13'),
(26, 206, 212, '2.1', 'minor', '/qms_optiqual/files/logs/LOG_1781417093_SAMPLE_Change_Request_Form.pdf', 'qqwertyui1234567890', 51, '2026-06-14'),
(27, 206, 213, '2.1', 'minor', '/qms_optiqual/files/logs/LOG_1781419209_SAMPLE_Change_Request_Form.pdf', '12345678', 51, '2026-06-14'),
(28, 214, 216, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781421237_SAMPLE_Change_Request_Form.pdf', '2', 51, '2026-06-14'),
(29, 214, 217, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781421376_SAMPLE_Change_Request_Form.pdf', '3', 51, '2026-06-14'),
(30, 214, 218, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781421503_SAMPLE_Change_Request_Form.pdf', '3', 51, '2026-06-14'),
(31, 219, 220, '1.1', 'minor', NULL, '1.1', 51, '2026-06-14'),
(32, 219, 221, '1.2', 'minor', '/qms_optiqual/files/logs/LOG_1781422651_SAMPLE_Change_Request_Form.pdf', '3', 51, '2026-06-14'),
(33, 219, 222, '1.3', 'minor', '/qms_optiqual/files/logs/LOG_1781422973_SAMPLE_Change_Request_Form.pdf', '4', 51, '2026-06-14'),
(34, 219, 223, '2.0', 'major', '/qms_optiqual/files/logs/LOG_1781423129_SAMPLE_Change_Request_Form.pdf', '7', 51, '2026-06-14'),
(35, 219, 224, '2.1', 'minor', '/qms_optiqual/files/logs/LOG_1781423904_SAMPLE_Change_Request_Form.pdf', '567', 51, '2026-06-14'),
(36, 219, 225, '3.0', 'major', '/qms_optiqual/files/logs/LOG_1781426634_SAMPLE_Change_Request_Form.pdf', '123456789', 51, '2026-06-14'),
(37, 226, 228, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781428039_SAMPLE_Change_Request_Form.pdf', '1.1', 51, '2026-06-14'),
(38, 226, 229, '1.2', 'minor', '/qms_optiqual/files/logs/LOG_1781428270_SAMPLE_Change_Request_Form.pdf', '1.2', 51, '2026-06-14'),
(39, 227, 230, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781430659_SAMPLE_Change_Request_Form.pdf', '12345678', 51, '2026-06-14'),
(40, 234, 241, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781605027_SAMPLE_Change_Request_Form.pdf', 'Revision 1.1', 51, '2026-06-16'),
(41, 234, 242, '1.2', 'minor', '/qms_optiqual/files/logs/LOG_1781605150_SAMPLE_Change_Request_Form.pdf', 'Revision 1.2', 51, '2026-06-16'),
(42, 237, 243, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781605770_SAMPLE_Change_Request_Form.pdf', 'Revision 1.1', 51, '2026-06-16'),
(43, 237, 244, '1.2', 'minor', '/qms_optiqual/files/logs/LOG_1781606079_SAMPLE_Change_Request_Form.pdf', 'Revision 1.2', 51, '2026-06-16'),
(44, 235, 246, '1.1', 'minor', '/qms_optiqual/files/logs/LOG_1781769025_SAMPLE_Change_Request_Form.pdf', 'Revision 2.1', 51, '2026-06-18');

-- --------------------------------------------------------

--
-- Table structure for table `rtypetbl`
--

CREATE TABLE `rtypetbl` (
  `roleID` int(11) NOT NULL,
  `roleName` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rtypetbl`
--

INSERT INTO `rtypetbl` (`roleID`, `roleName`) VALUES
(1, 'admin'),
(2, 'qa_director'),
(3, 'qa_personnel'),
(4, 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `tasktbl`
--

CREATE TABLE `tasktbl` (
  `taskID` int(11) NOT NULL,
  `taskTypeID` int(11) DEFAULT 1,
  `requestChangeContentPath` varchar(255) DEFAULT NULL,
  `policyAssigned` int(11) NOT NULL,
  `assignedBy` int(11) DEFAULT NULL,
  `assignedTo` int(11) DEFAULT 54,
  `taskStatus` int(1) NOT NULL DEFAULT 0 COMMENT '0 = Pending\r\n1 = Complete\r\n2 = Rejected',
  `dateCreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasktbl`
--

INSERT INTO `tasktbl` (`taskID`, `taskTypeID`, `requestChangeContentPath`, `policyAssigned`, `assignedBy`, `assignedTo`, `taskStatus`, `dateCreated`) VALUES
(325, 2, NULL, 245, 51, 50, 0, '2026-06-18 13:59:17'),
(326, 2, NULL, 245, 51, 54, 0, '2026-06-18 13:59:17'),
(327, 2, NULL, 246, 51, 50, 0, '2026-06-18 15:50:25'),
(328, 2, NULL, 246, 51, 54, 0, '2026-06-18 15:50:25'),
(329, 2, NULL, 235, 51, 54, 0, '2026-06-18 16:00:00'),
(330, 2, NULL, 236, 51, 50, 0, '2026-06-18 16:15:30'),
(331, 2, NULL, 241, 51, 58, 0, '2026-06-18 17:05:00'),
(332, 2, NULL, 242, 51, 54, 0, '2026-06-18 18:22:11'),
(333, 1, NULL, 234, 54, 51, 1, '2026-06-15 08:30:00'),
(334, 1, NULL, 235, 50, 51, 1, '2026-06-15 09:45:12'),
(335, 1, NULL, 236, 58, 54, 1, '2026-06-16 10:00:00'),
(336, 1, NULL, 237, 54, 50, 1, '2026-06-16 11:12:45'),
(337, 5, '/qms_optiqual/files/logs/LOG_1781605027_SAMPLE_Change_Request_Form.pdf', 238, 54, 51, 2, '2026-06-16 14:20:00'),
(338, 5, '/qms_optiqual/files/logs/LOG_1781605150_SAMPLE_Change_Request_Form.pdf', 239, 50, 51, 2, '2026-06-16 15:35:10'),
(339, 5, '/qms_optiqual/files/logs/LOG_1781606079_SAMPLE_Change_Request_Form.pdf', 240, 58, 51, 2, '2026-06-17 09:10:22'),
(340, 4, NULL, 234, 51, 54, 1, '2026-06-13 13:51:40'),
(341, 4, NULL, 235, 51, 54, 0, '2026-06-16 17:06:28'),
(342, 4, NULL, 236, 51, 50, 1, '2026-06-16 17:06:07'),
(343, 3, NULL, 241, 54, 51, 1, '2026-06-17 11:00:00'),
(344, 3, NULL, 242, 50, 51, 0, '2026-06-18 10:15:00'),
(345, 3, NULL, 243, 58, 51, 0, '2026-06-18 11:30:55'),
(346, 6, NULL, 244, 51, 54, 1, '2026-06-14 15:12:30'),
(347, 2, NULL, 237, 51, 58, 2, '2026-06-15 16:41:21'),
(348, 1, NULL, 243, 44, 50, 0, '2026-06-18 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tasktypetbl`
--

CREATE TABLE `tasktypetbl` (
  `taskTypeID` int(11) NOT NULL,
  `taskTypeName` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasktypetbl`
--

INSERT INTO `tasktypetbl` (`taskTypeID`, `taskTypeName`) VALUES
(1, 'Review'),
(2, 'Verification'),
(3, 'Approval'),
(4, 'Upload'),
(5, 'Request for Revision'),
(6, 'Revision');

-- --------------------------------------------------------

--
-- Table structure for table `templatestbl`
--

CREATE TABLE `templatestbl` (
  `templateID` int(11) NOT NULL,
  `title` varchar(45) NOT NULL,
  `contentPath` varchar(45) NOT NULL,
  `templateTypeID` int(11) NOT NULL,
  `dateUplaoded` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `templatestypetbl`
--

CREATE TABLE `templatestypetbl` (
  `templatesTypeID` int(11) NOT NULL,
  `templateTypeName` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accdatatbl`
--
ALTER TABLE `accdatatbl`
  ADD PRIMARY KEY (`accID`,`roleID`),
  ADD KEY `roleID_idx` (`roleID`);

--
-- Indexes for table `categorytbl`
--
ALTER TABLE `categorytbl`
  ADD PRIMARY KEY (`categoryID`),
  ADD KEY `parentCategoryID_idx` (`parentCategoryID`);

--
-- Indexes for table `dorgtbl`
--
ALTER TABLE `dorgtbl`
  ADD PRIMARY KEY (`dptID`),
  ADD KEY `dptParentID_idx` (`dptParentID`);

--
-- Indexes for table `dpaagreehistbl`
--
ALTER TABLE `dpaagreehistbl`
  ADD PRIMARY KEY (`agreementID`),
  ADD KEY `accID_idx` (`accID`),
  ADD KEY `dpaID_idx` (`dpaID`);

--
-- Indexes for table `dpatbl`
--
ALTER TABLE `dpatbl`
  ADD PRIMARY KEY (`dpaID`);

--
-- Indexes for table `empperdeptbl`
--
ALTER TABLE `empperdeptbl`
  ADD PRIMARY KEY (`employeeID`),
  ADD KEY `accID_idx` (`accID`),
  ADD KEY `dptID_idx` (`dptID`);

--
-- Indexes for table `feedbackstatustbl`
--
ALTER TABLE `feedbackstatustbl`
  ADD PRIMARY KEY (`fbStatusID`);

--
-- Indexes for table `feedbacktbl`
--
ALTER TABLE `feedbacktbl`
  ADD PRIMARY KEY (`feedbackID`),
  ADD KEY `remarksOn_idx` (`remarksOn`),
  ADD KEY `remarksBy_idx` (`remarksBy`),
  ADD KEY `fbType_idx` (`fbType`);

--
-- Indexes for table `feedbacktypetbl`
--
ALTER TABLE `feedbacktypetbl`
  ADD PRIMARY KEY (`fbTypeID`);

--
-- Indexes for table `notiftbl`
--
ALTER TABLE `notiftbl`
  ADD PRIMARY KEY (`notifID`),
  ADD KEY `receivedBy_idx` (`receivedBy`);

--
-- Indexes for table `otptbl`
--
ALTER TABLE `otptbl`
  ADD PRIMARY KEY (`OTPID`),
  ADD KEY `requestBy` (`requestBy`);

--
-- Indexes for table `policylogstbl`
--
ALTER TABLE `policylogstbl`
  ADD PRIMARY KEY (`policyLogID`),
  ADD KEY `policyID_idx` (`policyID`),
  ADD KEY `accID_idx` (`accID`),
  ADD KEY `statusID_idx` (`statusID`);

--
-- Indexes for table `policyrevisiontbl`
--
ALTER TABLE `policyrevisiontbl`
  ADD PRIMARY KEY (`revisionID`),
  ADD KEY `idx_original` (`originalPolicyID`),
  ADD KEY `idx_revised` (`revisedPolicyID`),
  ADD KEY `idx_submitted` (`submittedBy`);

--
-- Indexes for table `policystatus`
--
ALTER TABLE `policystatus`
  ADD PRIMARY KEY (`policyStatusID`);

--
-- Indexes for table `policytbl`
--
ALTER TABLE `policytbl`
  ADD PRIMARY KEY (`policyID`),
  ADD KEY `categoryID_idx` (`categoryID`),
  ADD KEY `policyAuthor_idx` (`policyAuthor`),
  ADD KEY `policyReviewer_idx` (`policyReviewer`),
  ADD KEY `policyVerifier_idx` (`policyVerifier`),
  ADD KEY `policyApprover_idx` (`policyApprover`),
  ADD KEY `policyStatusID_idx` (`policyStatusID`),
  ADD KEY `templateID_idx` (`templateID`);

--
-- Indexes for table `polviewtbl`
--
ALTER TABLE `polviewtbl`
  ADD PRIMARY KEY (`logID`),
  ADD KEY `policyID_idx` (`policyID`),
  ADD KEY `viewedBy_idx` (`viewedBy`);

--
-- Indexes for table `revisionhistorytbl`
--
ALTER TABLE `revisionhistorytbl`
  ADD PRIMARY KEY (`revisionID`);

--
-- Indexes for table `rtypetbl`
--
ALTER TABLE `rtypetbl`
  ADD PRIMARY KEY (`roleID`);

--
-- Indexes for table `tasktbl`
--
ALTER TABLE `tasktbl`
  ADD PRIMARY KEY (`taskID`),
  ADD KEY `taskTypeID_idx` (`taskTypeID`),
  ADD KEY `policyAsssigned_idx` (`policyAssigned`),
  ADD KEY `assignedBy_idx` (`assignedBy`),
  ADD KEY `assignedTo_idx` (`assignedTo`);

--
-- Indexes for table `tasktypetbl`
--
ALTER TABLE `tasktypetbl`
  ADD PRIMARY KEY (`taskTypeID`);

--
-- Indexes for table `templatestbl`
--
ALTER TABLE `templatestbl`
  ADD PRIMARY KEY (`templateID`);

--
-- Indexes for table `templatestypetbl`
--
ALTER TABLE `templatestypetbl`
  ADD PRIMARY KEY (`templatesTypeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accdatatbl`
--
ALTER TABLE `accdatatbl`
  MODIFY `accID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `categorytbl`
--
ALTER TABLE `categorytbl`
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `dorgtbl`
--
ALTER TABLE `dorgtbl`
  MODIFY `dptID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `dpaagreehistbl`
--
ALTER TABLE `dpaagreehistbl`
  MODIFY `agreementID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dpatbl`
--
ALTER TABLE `dpatbl`
  MODIFY `dpaID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `empperdeptbl`
--
ALTER TABLE `empperdeptbl`
  MODIFY `employeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedbackstatustbl`
--
ALTER TABLE `feedbackstatustbl`
  MODIFY `fbStatusID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedbacktbl`
--
ALTER TABLE `feedbacktbl`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `feedbacktypetbl`
--
ALTER TABLE `feedbacktypetbl`
  MODIFY `fbTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notiftbl`
--
ALTER TABLE `notiftbl`
  MODIFY `notifID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=591;

--
-- AUTO_INCREMENT for table `otptbl`
--
ALTER TABLE `otptbl`
  MODIFY `OTPID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `policylogstbl`
--
ALTER TABLE `policylogstbl`
  MODIFY `policyLogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `policyrevisiontbl`
--
ALTER TABLE `policyrevisiontbl`
  MODIFY `revisionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `policystatus`
--
ALTER TABLE `policystatus`
  MODIFY `policyStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `policytbl`
--
ALTER TABLE `policytbl`
  MODIFY `policyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `revisionhistorytbl`
--
ALTER TABLE `revisionhistorytbl`
  MODIFY `revisionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `rtypetbl`
--
ALTER TABLE `rtypetbl`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tasktbl`
--
ALTER TABLE `tasktbl`
  MODIFY `taskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=349;

--
-- AUTO_INCREMENT for table `tasktypetbl`
--
ALTER TABLE `tasktypetbl`
  MODIFY `taskTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `templatestbl`
--
ALTER TABLE `templatestbl`
  MODIFY `templateID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `templatestypetbl`
--
ALTER TABLE `templatestypetbl`
  MODIFY `templatesTypeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accdatatbl`
--
ALTER TABLE `accdatatbl`
  ADD CONSTRAINT `roleID` FOREIGN KEY (`roleID`) REFERENCES `rtypetbl` (`roleID`);

--
-- Constraints for table `categorytbl`
--
ALTER TABLE `categorytbl`
  ADD CONSTRAINT `parentCategoryID` FOREIGN KEY (`parentCategoryID`) REFERENCES `categorytbl` (`categoryID`);

--
-- Constraints for table `dorgtbl`
--
ALTER TABLE `dorgtbl`
  ADD CONSTRAINT `dptParentID` FOREIGN KEY (`dptParentID`) REFERENCES `dorgtbl` (`dptID`);

--
-- Constraints for table `dpaagreehistbl`
--
ALTER TABLE `dpaagreehistbl`
  ADD CONSTRAINT `accID` FOREIGN KEY (`accID`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `dpaID` FOREIGN KEY (`dpaID`) REFERENCES `dpatbl` (`dpaID`);

--
-- Constraints for table `empperdeptbl`
--
ALTER TABLE `empperdeptbl`
  ADD CONSTRAINT `EMPaccID` FOREIGN KEY (`accID`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `EMPdptID` FOREIGN KEY (`dptID`) REFERENCES `dorgtbl` (`dptID`);

--
-- Constraints for table `feedbacktbl`
--
ALTER TABLE `feedbacktbl`
  ADD CONSTRAINT `fbType` FOREIGN KEY (`fbType`) REFERENCES `feedbacktypetbl` (`fbTypeID`),
  ADD CONSTRAINT `remarksBy` FOREIGN KEY (`remarksBy`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `remarksOn` FOREIGN KEY (`remarksOn`) REFERENCES `policytbl` (`policyID`);

--
-- Constraints for table `notiftbl`
--
ALTER TABLE `notiftbl`
  ADD CONSTRAINT `receivedBy` FOREIGN KEY (`receivedBy`) REFERENCES `accdatatbl` (`accID`);

--
-- Constraints for table `otptbl`
--
ALTER TABLE `otptbl`
  ADD CONSTRAINT `otptbl_ibfk_1` FOREIGN KEY (`requestBy`) REFERENCES `accdatatbl` (`accID`) ON DELETE CASCADE;

--
-- Constraints for table `policylogstbl`
--
ALTER TABLE `policylogstbl`
  ADD CONSTRAINT `PLaccID` FOREIGN KEY (`accID`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `PLpolicyID` FOREIGN KEY (`policyID`) REFERENCES `policytbl` (`policyID`),
  ADD CONSTRAINT `PLstatusID` FOREIGN KEY (`statusID`) REFERENCES `policystatus` (`policyStatusID`);

--
-- Constraints for table `policytbl`
--
ALTER TABLE `policytbl`
  ADD CONSTRAINT `categoryID` FOREIGN KEY (`categoryID`) REFERENCES `categorytbl` (`categoryID`),
  ADD CONSTRAINT `policyApprover` FOREIGN KEY (`policyApprover`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `policyAuthor` FOREIGN KEY (`policyAuthor`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `policyReviewer` FOREIGN KEY (`policyReviewer`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `policyStatusID` FOREIGN KEY (`policyStatusID`) REFERENCES `policystatus` (`policyStatusID`),
  ADD CONSTRAINT `policyVerifier` FOREIGN KEY (`policyVerifier`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `templateID` FOREIGN KEY (`templateID`) REFERENCES `templatestbl` (`templateID`);

--
-- Constraints for table `polviewtbl`
--
ALTER TABLE `polviewtbl`
  ADD CONSTRAINT `policyID` FOREIGN KEY (`policyID`) REFERENCES `policytbl` (`policyID`),
  ADD CONSTRAINT `viewedBy` FOREIGN KEY (`viewedBy`) REFERENCES `accdatatbl` (`accID`);

--
-- Constraints for table `tasktbl`
--
ALTER TABLE `tasktbl`
  ADD CONSTRAINT `assignedBy` FOREIGN KEY (`assignedBy`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `assignedTo` FOREIGN KEY (`assignedTo`) REFERENCES `accdatatbl` (`accID`),
  ADD CONSTRAINT `policyAsssigned` FOREIGN KEY (`policyAssigned`) REFERENCES `policytbl` (`policyID`),
  ADD CONSTRAINT `taskTypeID` FOREIGN KEY (`taskTypeID`) REFERENCES `tasktypetbl` (`taskTypeID`);

--
-- Constraints for table `templatestbl`
--
ALTER TABLE `templatestbl`
  ADD CONSTRAINT `templateTypeID` FOREIGN KEY (`templateID`) REFERENCES `templatestypetbl` (`templatesTypeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
