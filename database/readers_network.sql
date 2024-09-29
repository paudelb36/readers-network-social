-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2024 at 03:42 AM
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
-- Database: `readers_network`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `ProfilePicture` varchar(255) DEFAULT NULL,
  `JoinDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastLogin` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `IsSuperAdmin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `Username`, `PasswordHash`, `FirstName`, `LastName`, `Email`, `ProfilePicture`, `JoinDate`, `LastLogin`, `IsSuperAdmin`) VALUES
(1, 'SocialAdmin', '123456', '', '', 'admin@admin.com', NULL, '2024-08-22 21:39:35', '2024-08-23 07:18:09', 0),
(2, 'SuperAdmin', '$2y$10$dBujzUVm9H6NkcbsuJ1z4eoekZeYBx1zpigmbO.Axw0UGjkdB64rW', '', '', 'admin1@gmail.com', 'profile7.jpg', '2024-08-23 07:19:00', '2024-09-25 09:24:28', 0);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `BookID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Author` varchar(100) DEFAULT NULL,
  `ISBN` varchar(13) DEFAULT NULL,
  `PublicationYear` int(11) DEFAULT NULL,
  `Genre` varchar(50) DEFAULT NULL,
  `Image` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `AverageRating` decimal(3,2) DEFAULT 0.00,
  `TotalReviews` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`BookID`, `Title`, `Author`, `ISBN`, `PublicationYear`, `Genre`, `Image`, `Description`, `AverageRating`, `TotalReviews`) VALUES
(5, 'Rich dad poor dad', 'Robert Kiyoshi', '465454', 2020, 'Self help', '../uploads/reviews/66c4bfe668cf0-LL-THE-3-430x430.png', '', 0.00, 0),
(6, 'The Intelligent Investor', 'Benjamin Graham', '465454', 2020, 'Self help', '../uploads/reviews/66c72e9f6959d-1679313391-430x430.png', '', 0.00, 0),
(7, 'Ikigai', 'Francesc Miralles and Hector Garcia', 'fsdfdgd', 2020, 'Self help', '../uploads/reviews/66c7aa9eb61fd-LL-THE-4-430x430.png', '', 0.00, 0),
(8, 'Atomic Habits', 'James Clear', '465454', 2020, 'Self help', '../uploads/reviews/66ca9a4067580-atomiccc-430x430.png', '', 0.00, 0),
(9, 'The Song Of Achilles', 'Madeline Miller', '465454', 2012, 'Mythical', '../uploads/reviews/66ca9f82b9d67-the-subtle-4-430x430.png', '', 0.00, 0),
(10, 'Harry Potter and the Deathly Hallows', 'J. K. Rowling', 'UOM:390760026', 2007, 'Juvenile Fiction', '../uploads/downloads/66f0feb54a5ad-Harry_Potter_and_the_Deathly_Hallows.jpg', '', 0.00, 0),
(11, 'The Psychology of Money', 'Morgan Housel', 'UOM:390760026', 2020, 'Self help', '../uploads/reviews/66f247d4063b1-The Psychology of Money.jpeg', '', 0.00, 0),
(12, 'Muna-Madan (à¤®à¥à¤¨à¤¾-à¤®à¤¦à¤¨)', 'Laxmi Prasad Devkota', '9937942004', 2020, 'Fiction', '../uploads/downloads/66f2553ddf85a-Muna-Madan______________-__________.jpg', '', 3.00, 0),
(13, 'One piece', 'Eiichiro Oda', '8822603605', 2016, 'Comics & Graphic Novels', '../uploads/downloads/66f3084a08f95-One_piece.jpg', '', 4.50, 0),
(14, 'Harry Potter and the Chamber of Secrets', 'J. K. Rowling', '1551922444', 2012, 'Juvenile Fiction', '../uploads/reviews/66f3a3cee0a3a-1680341041-430x430.png', NULL, 0.00, 0),
(15, 'Harry Potter and the Order of the Phoenix', 'J. K. Rowling', '9781408855935', 2014, 'Fiction', '../uploads/downloads/66f4edcae8448-Harry_Potter_and_the_Order_of_the_Phoenix.jpg', '', 0.00, 0),
(16, 'Harry Potter and the Prisoner of Azkaban', 'J. K. Rowling', '9781408855676', 2014, 'Juvenile Fiction', '../uploads/downloads/66f4f1b1137a8-Harry_Potter_and_the_Prisoner_of_Azkaban.jpg', '', 3.00, 0),
(17, 'The Boy in the Striped Pajamas (Deluxe Illustrated Edition)', 'John Boyne', '9781524766535', 2017, 'Young Adult Fiction', '../uploads/downloads/66f4f39eef666-The_Boy_in_the_Striped_Pajamas__Deluxe_Illustrated_Edition_.jpg', '', 0.00, 0),
(18, '20th century boys', 'Naoki Urasawa', '3899211553', 2002, 'Comics & Graphic Novels', '../uploads/downloads/66f6b9175a9ca-20th_century_boys.jpg', '', 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `CommentID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Content` mediumtext NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `ReviewID` int(11) NOT NULL,
  `ParentCommentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`CommentID`, `UserID`, `Content`, `CreatedAt`, `ReviewID`, `ParentCommentID`) VALUES
(9, 7, 'fef', '2024-09-23 15:33:18', 15, NULL),
(10, 7, 'dfsd', '2024-09-23 15:33:29', 15, NULL),
(11, 7, 'sdfds', '2024-09-23 15:33:35', 15, NULL),
(12, 7, 'gdsfds', '2024-09-23 15:33:38', 8, NULL),
(13, 7, 'gdaf', '2024-09-23 15:33:40', 8, NULL),
(15, 3, 'nice', '2024-09-24 05:57:54', 7, NULL),
(16, 3, 'nice', '2024-09-24 06:03:50', 17, NULL),
(17, 7, 'great', '2024-09-24 16:44:04', 4, NULL),
(18, 7, 'fd', '2024-09-24 19:12:48', 18, NULL),
(19, 7, 'great', '2024-09-24 19:16:01', 18, NULL),
(20, 10, 'ds', '2024-09-24 19:35:08', 18, NULL),
(21, 6, 'nice', '2024-09-25 10:37:22', 19, NULL),
(23, 10, 'great book', '2024-09-28 16:38:51', 21, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `commentvotes`
--

CREATE TABLE `commentvotes` (
  `VoteID` int(11) NOT NULL,
  `CommentID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `VoteValue` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friendrequests`
--

CREATE TABLE `friendrequests` (
  `RequesterID` int(11) NOT NULL,
  `RequestedID` int(11) NOT NULL,
  `Status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `friendrequests`
--

INSERT INTO `friendrequests` (`RequesterID`, `RequestedID`, `Status`) VALUES
(3, 6, 'Accepted'),
(5, 6, 'Accepted'),
(6, 7, 'Rejected'),
(6, 9, 'Pending'),
(7, 6, 'Rejected'),
(10, 3, 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `UserID` int(11) NOT NULL,
  `FriendID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`UserID`, `FriendID`) VALUES
(3, 6),
(3, 7),
(3, 9),
(3, 10),
(5, 6),
(5, 7),
(6, 3),
(6, 5),
(7, 3),
(7, 5),
(7, 9),
(9, 3),
(9, 7),
(10, 3);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `LikeID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `ReviewID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`LikeID`, `UserID`, `CreatedAt`, `ReviewID`) VALUES
(65, 7, '2024-09-23 17:08:23', 8),
(66, 7, '2024-09-23 17:08:24', 7),
(88, 9, '2024-09-24 09:31:39', 15),
(89, 9, '2024-09-24 09:31:45', 6),
(93, 5, '2024-09-24 09:35:29', 4),
(97, 6, '2024-09-24 09:37:09', 6),
(103, 3, '2024-09-24 11:42:45', 8),
(109, 3, '2024-09-24 11:48:59', 17),
(110, 3, '2024-09-24 11:49:20', 15),
(127, 7, '2024-09-25 07:39:17', 4),
(128, 7, '2024-09-25 07:39:18', 5),
(129, 7, '2024-09-25 07:39:20', 6),
(131, 10, '2024-09-25 07:40:18', 8),
(145, 10, '2024-09-27 07:46:11', 18),
(159, 10, '2024-09-28 22:01:58', 21),
(160, 10, '2024-09-28 22:02:01', 19),
(162, 10, '2024-09-28 22:02:06', 15),
(163, 10, '2024-09-28 22:02:07', 17),
(164, 10, '2024-09-28 22:02:09', 7),
(165, 10, '2024-09-28 22:02:11', 6),
(166, 10, '2024-09-28 22:02:14', 5),
(167, 10, '2024-09-28 22:02:15', 4),
(168, 10, '2024-09-28 22:24:32', 20),
(171, 3, '2024-09-28 22:42:13', 20),
(172, 3, '2024-09-28 22:42:14', 19),
(173, 3, '2024-09-28 22:42:15', 18),
(174, 3, '2024-09-28 22:42:19', 7),
(175, 3, '2024-09-28 22:42:20', 6),
(176, 3, '2024-09-28 22:42:21', 5),
(177, 3, '2024-09-28 22:42:22', 4),
(178, 3, '2024-09-29 02:35:16', 21);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `NotificationID` int(11) NOT NULL,
  `Content` text DEFAULT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `ActorID` int(11) NOT NULL,
  `Type` enum('friend_request','reaction','comment','reviews') NOT NULL,
  `RecipientID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`NotificationID`, `Content`, `IsRead`, `CreatedAt`, `ActorID`, `Type`, `RecipientID`) VALUES
(2, 'Bibliophile sent you a friend request.', 1, '2024-09-22 23:36:32', 7, 'friend_request', 6),
(5, 'Bookworm sent you a friend request.', 1, '2024-09-24 09:29:36', 5, 'friend_request', 6),
(9, 'Your friend has posted a new review for the book \'The Boy in the Striped Pajamas (Deluxe Illustrated Edition)\'.', 0, '2024-09-26 11:24:43', 3, 'reviews', 5),
(10, 'Your friend has posted a new review for the book \'The Boy in the Striped Pajamas (Deluxe Illustrated Edition)\'.', 0, '2024-09-26 11:24:43', 3, 'reviews', 6),
(11, 'Your friend has posted a new review for the book \'The Boy in the Striped Pajamas (Deluxe Illustrated Edition)\'.', 0, '2024-09-26 11:24:43', 3, 'reviews', 7),
(12, 'Your friend has posted a new review for the book \'The Boy in the Striped Pajamas (Deluxe Illustrated Edition)\'.', 0, '2024-09-26 11:24:43', 3, 'reviews', 9),
(15, 'great book', 1, '2024-09-28 22:23:51', 10, 'comment', 3),
(16, NULL, 1, '2024-09-28 22:24:32', 10, 'reaction', 3),
(17, NULL, 0, '2024-09-28 22:42:14', 3, 'reaction', 6),
(18, NULL, 0, '2024-09-28 22:42:15', 3, 'reaction', 7),
(19, NULL, 0, '2024-09-28 22:42:19', 3, 'reaction', 6),
(20, NULL, 0, '2024-09-28 22:42:20', 3, 'reaction', 9),
(21, NULL, 0, '2024-09-28 22:42:21', 3, 'reaction', 7);

-- --------------------------------------------------------

--
-- Table structure for table `opinions`
--

CREATE TABLE `opinions` (
  `OpinionID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ReviewID` int(11) NOT NULL,
  `OpinionText` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `opinions`
--

INSERT INTO `opinions` (`OpinionID`, `UserID`, `ReviewID`, `OpinionText`, `CreatedAt`) VALUES
(1, 10, 18, 'gffg', '2024-09-25 02:13:18'),
(2, 10, 17, 'Great reads', '2024-09-25 04:43:30'),
(3, 9, 18, 'good manga', '2024-09-25 04:43:59'),
(4, 6, 18, 'nice', '2024-09-25 05:17:42'),
(5, 9, 5, 'great reads', '2024-09-25 19:22:19'),
(6, 9, 17, 'ok', '2024-09-26 02:04:41'),
(7, 10, 21, 'ok', '2024-09-26 08:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `RatingID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `BookID` int(11) NOT NULL,
  `Rating` decimal(3,2) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`RatingID`, `UserID`, `BookID`, `Rating`, `CreatedAt`) VALUES
(1, 10, 13, 5.00, '2024-09-25 02:13:18'),
(4, 10, 12, 4.00, '2024-09-25 04:43:30'),
(5, 9, 13, 4.00, '2024-09-25 04:43:59'),
(6, 6, 13, 2.00, '2024-09-25 05:17:42'),
(7, 9, 6, 5.00, '2024-09-25 19:22:19'),
(8, 9, 12, 2.00, '2024-09-26 02:04:41'),
(12, 10, 16, 3.00, '2024-09-26 08:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `readinglist`
--

CREATE TABLE `readinglist` (
  `ReadingListID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `BookID` int(11) DEFAULT NULL,
  `Status` enum('Want to Read','Currently Reading','Read') NOT NULL,
  `DateAdded` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `readinglist`
--

INSERT INTO `readinglist` (`ReadingListID`, `UserID`, `BookID`, `Status`, `DateAdded`) VALUES
(1, 10, 18, 'Read', '2024-09-28 13:40:10'),
(5, 10, 17, 'Currently Reading', '2024-09-28 15:03:56'),
(6, 10, 12, 'Want to Read', '2024-09-28 21:12:42');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `ReportID` int(11) NOT NULL,
  `ReporterID` int(11) DEFAULT NULL,
  `ReportedUserID` int(11) DEFAULT NULL,
  `ReportedPostID` int(11) DEFAULT NULL,
  `ReportedCommentID` int(11) DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `Status` enum('Pending','Reviewed','Resolved') DEFAULT 'Pending',
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `ResolvedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`ReportID`, `ReporterID`, `ReportedUserID`, `ReportedPostID`, `ReportedCommentID`, `Reason`, `Status`, `CreatedAt`, `ResolvedAt`) VALUES
(1, 6, 6, 19, NULL, 'Spam', 'Pending', '2024-09-25 15:27:42', NULL),
(6, 6, 6, NULL, NULL, 'Spam', 'Pending', '2024-09-25 15:46:37', NULL),
(7, 6, NULL, 19, NULL, 'Spam', 'Pending', '2024-09-25 15:46:56', NULL),
(13, 6, 3, 4, NULL, 'Spam', 'Resolved', '2024-09-25 16:33:24', NULL),
(14, 6, 3, 4, NULL, 'Spam', 'Pending', '2024-09-25 16:33:27', NULL),
(20, 9, 6, 19, NULL, 'Spam', 'Pending', '2024-09-26 06:54:40', NULL),
(22, 9, 3, 4, NULL, 'Spam', 'Pending', '2024-09-26 06:59:44', NULL),
(23, 9, 6, 7, NULL, 'Inappropriate Content', 'Pending', '2024-09-26 07:13:51', NULL),
(27, 9, 6, 19, NULL, 'Spam', 'Pending', '2024-09-26 07:22:05', NULL),
(29, 9, 7, 18, NULL, 'Spam', 'Pending', '2024-09-26 07:23:28', NULL),
(30, 9, 5, 8, NULL, 'Spam', 'Pending', '2024-09-26 07:27:06', NULL),
(31, 3, 6, 19, NULL, 'Spam', 'Pending', '2024-09-29 01:37:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `BookID` int(11) NOT NULL,
  `ReviewText` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `Title` varchar(255) NOT NULL,
  `Author` varchar(255) NOT NULL,
  `ISBN` varchar(20) NOT NULL,
  `PublicationYear` int(11) NOT NULL,
  `Genre` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `Status` enum('visible','hidden') DEFAULT 'visible'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `UserID`, `BookID`, `ReviewText`, `CreatedAt`, `Title`, `Author`, `ISBN`, `PublicationYear`, `Genre`, `Description`, `Image`, `Status`) VALUES
(4, 3, 5, 'good book', '2024-08-20 21:55:14', 'Rich dad poor dad', 'Robert Kiyoshi', '465454', 2020, 'Self help', '', '../uploads/reviews/66c4bfe668cf0-LL-THE-3-430x430.png', 'visible'),
(5, 7, 6, 'The Intelligent Investor is a timeless classic that has influenced countless investors. It provides a solid foundation for understanding the principles of value investing, emphasizing the importance of buying stocks at prices below their intrinsic worth.', '2024-08-22 18:12:11', 'The Intelligent Investor', 'Benjamin Graham', '465454', 2020, 'Self help', '', '../uploads/reviews/66c72e9f6959d-1679313391-430x430.png', 'visible'),
(6, 9, 7, 'good books', '2024-08-23 03:01:14', 'Ikigai', 'Francesc Miralles and Hector Garcia', 'fsdfdgd', 2020, 'Self help', '', '../uploads/reviews/66c7aa9eb61fd-LL-THE-4-430x430.png', 'visible'),
(7, 6, 8, 'Great self help book.', '2024-08-25 08:28:12', 'Atomic Habits', 'James Clear', '465454', 2020, 'Self help', '', '../uploads/reviews/66ca9a4067580-atomiccc-430x430.png', 'visible'),
(8, 5, 9, 'Great Book', '2024-08-25 08:50:38', 'The Song Of Achilles', 'Madeline Miller', '465454', 2012, 'Mythical', '', '../uploads/reviews/66ca9f82b9d67-the-subtle-4-430x430.png', 'visible'),
(15, 9, 10, 'dgsdafdsf', '2024-09-23 11:22:57', 'Harry Potter and the Deathly Hallows', 'J. K. Rowling', 'UOM:39076002651854', 2007, 'Juvenile Fiction', '', '../uploads/downloads/66f0feb54a5ad-Harry_Potter_and_the_Deathly_Hallows.jpg', 'visible'),
(17, 3, 12, 'Great book, good read.', '2024-09-24 11:44:26', 'Muna-Madan (à¤®à¥à¤¨à¤¾-à¤®à¤¦à¤¨)', 'Laxmi Prasad Devkota', '9937942004', 2020, 'Fiction', '', '../uploads/downloads/66f2553ddf85a-Muna-Madan______________-__________.jpg', 'visible'),
(18, 7, 13, 'Great manga', '2024-09-25 00:28:23', 'One piece', 'Eiichiro Oda', '8822603605', 2016, 'Comics & Graphic Novels', '', '../uploads/downloads/66f3084a08f95-One_piece.jpg', 'visible'),
(19, 6, 14, 'Nice', '2024-09-25 11:31:54', 'Harry Potter and the Chamber of Secrets', 'J. K. Rowling', '1551922444', 2012, 'Juvenile Fiction', NULL, '../uploads/reviews/66f3a3cee0a3a-1680341041-430x430.png', 'visible'),
(20, 3, 15, 'great book', '2024-09-26 10:59:51', 'Harry Potter and the Order of the Phoenix', 'J. K. Rowling', '9781408855935', 2014, 'Fiction', '', '../uploads/downloads/66f4edcae8448-Harry_Potter_and_the_Order_of_the_Phoenix.jpg', 'visible'),
(21, 3, 16, 'nice', '2024-09-26 11:16:29', 'Harry Potter and the Prisoner of Azkaban', 'J. K. Rowling', '9781408855676', 2014, 'Juvenile Fiction', '', '../uploads/downloads/66f4f1b1137a8-Harry_Potter_and_the_Prisoner_of_Azkaban.jpg', 'visible');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `ProfilePicture` varchar(255) DEFAULT NULL,
  `Bio` text DEFAULT NULL,
  `JoinDate` datetime DEFAULT current_timestamp(),
  `LastLogin` datetime DEFAULT NULL,
  `IsAdmin` tinyint(1) DEFAULT 0,
  `IsSuspended` tinyint(1) DEFAULT 0,
  `SuspensionEndDate` datetime DEFAULT NULL,
  `IsBanned` tinyint(1) DEFAULT 0,
  `Location` varchar(255) DEFAULT NULL,
  `FavoriteGenres` varchar(255) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `ProfilePicture`, `Bio`, `JoinDate`, `LastLogin`, `IsAdmin`, `IsSuspended`, `SuspensionEndDate`, `IsBanned`, `Location`, `FavoriteGenres`, `DateOfBirth`) VALUES
(3, 'john doe', 'johndoe@gmail.com', '$2y$10$kR430hpNxH8cg9Z3EmAmZOYsYypHjGEe.imUFY0e5BI2OPDJ/ezIG', 'john', 'doe', '1724554174_profile2.jpg', 'I enjoy historical fiction and mysteries.', '2024-08-20 00:35:19', NULL, 0, 0, NULL, 0, 'USA', 'horror, mystery, comedy', '1998-07-29'),
(5, 'Bookworm', 'bookworm@example.com', '$2y$10$o9nA1Njn0G6vVhfsxmFgre2k9sUV/oWrmtBiYMjo/5PKBUq2ZZAmO', 'Emily', 'Clark', '1724554888_profile6.jpg', 'I\'m a fan of horror and suspense novels.', '2024-08-22 16:33:50', NULL, 0, 0, NULL, 0, '', 'Horror, Suspense', '1999-02-18'),
(6, 'BookNerd', 'booknerd@example.com', '$2y$10$D/b.XbjSt8kirVQsZOZw6.uvgoFw1PG9tCfdWmSR848qP4KQe/3bS', 'Henry', 'Thomas', '1724349129_profile3.jpg', 'I\'m a fan of mystery and detective novels.', '2024-08-22 17:29:14', NULL, 0, 0, NULL, 0, 'Cairo, Egypt', 'Mystery, Detective', '1997-06-27'),
(7, 'Grace', 'bibliophile@example.com', '$2y$10$05NagviN5v/jNYPJq1dGZ.9imXuAK79E//3K.Ir93yh.oN6gRiSJG', 'Grace', 'Taylor', '1724348799_profile4.jpg', 'I enjoy reading classics and literary fiction', '2024-08-22 17:34:54', NULL, 0, 0, NULL, 0, 'USA', 'Fantasy,Science Fiction,Thriller,Romance,Historical Fiction', '2000-10-24'),
(9, 'David', 'DavidLee@example.com', '$2y$10$43pcQN7I802ewntNAEeB9.qP.7jQJRsOOo/VCmSyLbrMUA4rymoRe', 'David', 'Lee', '1724361273_profile5.jpg', '', '2024-08-22 23:51:29', NULL, 0, 0, NULL, 0, '', 'horror, mystery, comedy', '1988-02-25'),
(10, 'bibek12', 'bibek@gmail.com', '$2y$10$vVt87cha9IFxW3nIXagNzud50ONW9ySmoGQzdhDvLydDRPAfJsToS', 'Bibek', 'Paudel', 'image3.jpg', '', '2024-09-24 20:02:09', NULL, 0, 0, NULL, 0, '', 'Fantasy,Science Fiction,Romance,Thriller,Mystery,Non-fiction,Horror,Historical Fiction', '0000-00-00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Username_2` (`Username`,`Email`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`BookID`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `fk_ParentComment` (`ParentCommentID`),
  ADD KEY `comments_ibfk_2` (`ReviewID`);

--
-- Indexes for table `commentvotes`
--
ALTER TABLE `commentvotes`
  ADD PRIMARY KEY (`VoteID`),
  ADD UNIQUE KEY `CommentID` (`CommentID`,`UserID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `friendrequests`
--
ALTER TABLE `friendrequests`
  ADD PRIMARY KEY (`RequesterID`,`RequestedID`),
  ADD KEY `RequestedID` (`RequestedID`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`UserID`,`FriendID`),
  ADD KEY `FriendID` (`FriendID`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`LikeID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `likes_ibfk_2` (`ReviewID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`NotificationID`),
  ADD KEY `fk_recipient` (`RecipientID`);

--
-- Indexes for table `opinions`
--
ALTER TABLE `opinions`
  ADD PRIMARY KEY (`OpinionID`),
  ADD KEY `FK_OpinionUserID` (`UserID`),
  ADD KEY `FK_OpinionReviewID` (`ReviewID`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`RatingID`),
  ADD UNIQUE KEY `UserID` (`UserID`,`BookID`),
  ADD KEY `FK_BookID` (`BookID`);

--
-- Indexes for table `readinglist`
--
ALTER TABLE `readinglist`
  ADD PRIMARY KEY (`ReadingListID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `BookID` (`BookID`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`ReportID`),
  ADD KEY `ReporterID` (`ReporterID`),
  ADD KEY `ReportedUserID` (`ReportedUserID`),
  ADD KEY `ReportedPostID` (`ReportedPostID`),
  ADD KEY `ReportedCommentID` (`ReportedCommentID`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `BookID` (`BookID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `CommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `commentvotes`
--
ALTER TABLE `commentvotes`
  MODIFY `VoteID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `LikeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `opinions`
--
ALTER TABLE `opinions`
  MODIFY `OpinionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `RatingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `readinglist`
--
ALTER TABLE `readinglist`
  MODIFY `ReadingListID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`ReviewID`) REFERENCES `reviews` (`ReviewID`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `fk_ParentComment` FOREIGN KEY (`ParentCommentID`) REFERENCES `comments` (`CommentID`);

--
-- Constraints for table `commentvotes`
--
ALTER TABLE `commentvotes`
  ADD CONSTRAINT `commentvotes_ibfk_1` FOREIGN KEY (`CommentID`) REFERENCES `comments` (`CommentID`),
  ADD CONSTRAINT `commentvotes_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `friendrequests`
--
ALTER TABLE `friendrequests`
  ADD CONSTRAINT `friendrequests_ibfk_1` FOREIGN KEY (`RequesterID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `friendrequests_ibfk_2` FOREIGN KEY (`RequestedID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`FriendID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`ReviewID`) REFERENCES `reviews` (`ReviewID`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_recipient` FOREIGN KEY (`RecipientID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `opinions`
--
ALTER TABLE `opinions`
  ADD CONSTRAINT `FK_OpinionReviewID` FOREIGN KEY (`ReviewID`) REFERENCES `reviews` (`ReviewID`),
  ADD CONSTRAINT `FK_OpinionUserID` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `FK_BookID` FOREIGN KEY (`BookID`) REFERENCES `books` (`BookID`),
  ADD CONSTRAINT `FK_UserID` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `readinglist`
--
ALTER TABLE `readinglist`
  ADD CONSTRAINT `readinglist_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `readinglist_ibfk_2` FOREIGN KEY (`BookID`) REFERENCES `books` (`BookID`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`ReportedUserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `reports_ibfk_4` FOREIGN KEY (`ReportedCommentID`) REFERENCES `comments` (`CommentID`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
