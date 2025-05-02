-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 11:17 AM
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
(45, 'test', 'last', 'test last', 'joaquind.rizal@gmail.com', '$2y$10$8RA2df9djSuPhL9hzvfGSeCvj49g7ODPJqisrWI81V44FXV2i5iqC', 4, '2025-04-24 14:32:58', 'verified'),
(50, 'test', 'last', '', 'joaquind.rizal3@gmail.com', '$2y$10$yjT8Y/6O9SO.vrcG8Hggo.9K9iooGgpohyhV.ujem9jQGb/VZm3TW', 4, '2025-04-29 08:16:04', 'verified'),
(51, 'Christian Dave', 'Trillanes', '', 'cctrillanes@student.apc.edu.ph', '$2y$10$Vj3T4NcyFfMGNCz8Am/Ct.c.gVu05sxFyXaAbMDQzMdkKskySYMoG', 2, '2025-04-29 12:45:58', 'unverified'),
(54, 'Christian Dave', 'Trillanes', 'Christian Dave Trillanes', 'yllanex0415@gmail.com', '$2y$10$LwEXF7q6JLW8ZXZ/bA0c8ON1t8UD/ttRrA947jCatxNbcCS96a/Qu', 4, '2025-04-30 12:28:47', 'verified');

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
(1, 'Trial Parent 1', NULL),
(2, 'Trial Parent 2', NULL),
(3, 'Trial Parent 3', NULL),
(4, 'Trial Parent 4', NULL),
(5, 'Trial Parent 5', NULL),
(6, 'Child 1.1', 1),
(7, 'Child 1.2', 1),
(8, 'Child 2.1', 2),
(9, 'Child 2.2', 2),
(10, 'Child 3.1', 3),
(11, 'Child 3.2', 3),
(12, 'Child 4.1', 4),
(13, 'Child 4.2', 4),
(14, 'Child 5.1', 5),
(15, 'Child 5.2', 5);

-- --------------------------------------------------------

--
-- Table structure for table `dorgtbl`
--

CREATE TABLE `dorgtbl` (
  `dptID` int(11) NOT NULL,
  `dptName` varchar(45) NOT NULL,
  `dptParentID` int(11) NOT NULL
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
  `fbType` int(11) NOT NULL,
  `content` tinytext NOT NULL,
  `sessionID` int(11) NOT NULL,
  `dateSubmitted` date NOT NULL,
  `feedbackTblcol` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(66, 54, '855559', 0, '2025-04-30 04:28:47', '2025-04-30 04:38:47', 'used');

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
(5, 'Uploaded');

-- --------------------------------------------------------

--
-- Table structure for table `policytbl`
--

CREATE TABLE `policytbl` (
  `policyID` int(11) NOT NULL,
  `title` varchar(45) NOT NULL,
  `contentPath` varchar(255) NOT NULL,
  `categoryID` int(11) DEFAULT NULL,
  `versionNo` int(11) DEFAULT NULL,
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
  `templateID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `policytbl`
--

INSERT INTO `policytbl` (`policyID`, `title`, `contentPath`, `categoryID`, `versionNo`, `policyAuthor`, `dateSubmitted`, `policyReviewer`, `dateReviewed`, `policyVerifier`, `dateVerified`, `policyApprover`, `dateApproved`, `dateUploaded`, `policyStatusID`, `templateID`) VALUES
(1, 'test policy', 'files/testPolicy.pdf', 7, NULL, 45, '2025-04-26 18:30:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(10, 'Caiphas Cain CI', '../../uploads/Caiphas Cain Caves of Ice.pdf', 6, NULL, 51, '2025-04-30 10:36:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL),
(14, 'Caiphas Cain TH', '', 6, 1, 51, '2025-05-02 15:41:31', 51, '2025-04-23 00:00:00', 54, '2025-05-02 15:40:57', 51, '2025-04-23 00:00:00', '2025-04-23 00:00:00', 2, NULL);

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
  `taskTypeID` int(11) NOT NULL,
  `requestChangeContentPath` varchar(255) DEFAULT NULL,
  `policyAssigned` int(11) NOT NULL,
  `assignedBy` int(11) NOT NULL,
  `assignedTo` int(11) NOT NULL,
  `taskStatus` int(1) NOT NULL DEFAULT 0 COMMENT '0 = Pending\r\n1 = Complete\r\n2 = Rejected',
  `dateCreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasktbl`
--

INSERT INTO `tasktbl` (`taskID`, `taskTypeID`, `requestChangeContentPath`, `policyAssigned`, `assignedBy`, `assignedTo`, `taskStatus`, `dateCreated`) VALUES
(0, 1, NULL, 1, 44, 45, 0, '2025-04-26 19:27:49'),
(1, 4, NULL, 1, 44, 45, 0, '2025-04-28 10:08:51'),
(3, 2, NULL, 10, 51, 54, 2, '2025-04-30 12:40:43');

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
  MODIFY `accID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `categorytbl`
--
ALTER TABLE `categorytbl`
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
-- AUTO_INCREMENT for table `feedbackstatustbl`
--
ALTER TABLE `feedbackstatustbl`
  MODIFY `fbStatusID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedbacktbl`
--
ALTER TABLE `feedbacktbl`
  MODIFY `feedbackID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedbacktypetbl`
--
ALTER TABLE `feedbacktypetbl`
  MODIFY `fbTypeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `otptbl`
--
ALTER TABLE `otptbl`
  MODIFY `OTPID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `policylogstbl`
--
ALTER TABLE `policylogstbl`
  MODIFY `policyLogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `policystatus`
--
ALTER TABLE `policystatus`
  MODIFY `policyStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `policytbl`
--
ALTER TABLE `policytbl`
  MODIFY `policyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `rtypetbl`
--
ALTER TABLE `rtypetbl`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `update_expired_otps` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-03-26 16:23:54' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE otptbl 
SET otpStatus = 'expired' 
WHERE otpExpires < NOW() 
AND otpStatus = 'unused'$$

CREATE DEFINER=`root`@`localhost` EVENT `delete_unverified_accounts` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-04-02 12:54:50' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DELETE FROM accdatatbl
    WHERE verificationStatus = 'unverified' AND dateCreated <= NOW() - INTERVAL 10 MINUTE;
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
