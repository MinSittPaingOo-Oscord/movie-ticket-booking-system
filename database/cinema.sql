-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2026 at 12:38 AM
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
-- Database: `cinema1`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `bookingID` int(11) NOT NULL,
  `bookingStatus` varchar(50) DEFAULT NULL,
  `customerID` int(11) DEFAULT NULL,
  `movieID` int(11) DEFAULT NULL,
  `seatID` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `datetimeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`bookingID`, `bookingStatus`, `customerID`, `movieID`, `seatID`, `price`, `datetimeID`) VALUES
(1, 'allowed', 1, 1, 5, 250, 1),
(2, 'allowed', 1, 1, 6, 250, 1),
(3, 'allowed', 2, 2, 10, 220, 2),
(4, 'allowed', 3, 3, 20, 230, 3);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customerID` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customerID`, `fullName`, `email`, `phoneNumber`) VALUES
(1, 'John Smith', 'john@gmail.com', '0812345678'),
(2, 'Emma Watson', 'emma@gmail.com', '0823456789'),
(3, 'Michael Lee', 'michael@gmail.com', '0834567890');

-- --------------------------------------------------------

--
-- Table structure for table `datetime`
--

CREATE TABLE `datetime` (
  `datetimeID` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `datetime`
--

INSERT INTO `datetime` (`datetimeID`, `date`, `time`) VALUES
(1, '2026-07-01', '10:00:00'),
(2, '2026-07-01', '13:30:00'),
(3, '2026-07-01', '17:00:00'),
(4, '2026-07-02', '10:00:00'),
(5, '2026-07-02', '15:30:00'),
(6, '2026-07-02', '19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `languageID` int(11) NOT NULL,
  `languageName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`languageID`, `languageName`) VALUES
(1, 'English'),
(2, 'Thai'),
(3, 'Chinese');

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

CREATE TABLE `movie` (
  `movieID` int(11) NOT NULL,
  `movieName` varchar(255) NOT NULL,
  `rating` varchar(10) DEFAULT NULL,
  `image` text DEFAULT NULL,
  `screen` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `sub_title` varchar(255) DEFAULT NULL,
  `Trailer` varchar(200) DEFAULT NULL,
  `categoryID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie`
--

INSERT INTO `movie` (`movieID`, `movieName`, `rating`, `image`, `screen`, `price`, `duration`, `sub_title`, `Trailer`, `categoryID`) VALUES
(1, 'Avatar', 'PG-13', 'avatar.jpg', 'Screen 1', 250.00, '03:12:00', 'Return to Pandora', 'https://youtu.be/d9MyW72ELq0', 2),
(2, 'Mission Impossible', 'PG-13', 'missionimpossible.jpg', 'Screen 2', 220.00, '02:43:00', 'The Final Reckoning', 'https://youtu.be/fsQgc9pCyDU', 1),
(3, 'Jurassic World', 'PG-13', 'jurassicworld.jpg', 'Screen 3', 230.00, '02:14:00', 'Rebirth', 'https://youtu.be/jan5CFWs9ic', 2),
(4, 'Superman', 'PG-13', 'superman.jpg', 'Screen 1', 240.00, '02:10:00', 'Legacy', 'https://youtu.be/Ox8ZLF6cGM0', 1),
(5, 'Lilo & Stitch', 'PG', 'liloandstitch.jpg', 'Screen 2', 200.00, '01:50:00', 'Live Action', 'https://youtu.be/VWqJifMMgZE', 3);

-- --------------------------------------------------------

--
-- Table structure for table `moviecategory`
--

CREATE TABLE `moviecategory` (
  `categoryID` int(11) NOT NULL,
  `categoryName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moviecategory`
--

INSERT INTO `moviecategory` (`categoryID`, `categoryName`) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Animation'),
(4, 'Comedy');

-- --------------------------------------------------------

--
-- Table structure for table `moviexdatetime`
--

CREATE TABLE `moviexdatetime` (
  `moviexdatetimeID` int(11) NOT NULL,
  `movieID` int(11) NOT NULL,
  `datetimeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moviexdatetime`
--

INSERT INTO `moviexdatetime` (`moviexdatetimeID`, `movieID`, `datetimeID`) VALUES
(1, 1, 1),
(2, 1, 5),
(3, 2, 2),
(4, 2, 6),
(5, 3, 3),
(6, 3, 4),
(7, 4, 1),
(8, 4, 6),
(9, 5, 2),
(10, 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `moviexlanguage`
--

CREATE TABLE `moviexlanguage` (
  `movieLanguageID` int(11) NOT NULL,
  `movieID` int(11) NOT NULL,
  `languageID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moviexlanguage`
--

INSERT INTO `moviexlanguage` (`movieLanguageID`, `movieID`, `languageID`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 1, 2),
(7, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `seat`
--

CREATE TABLE `seat` (
  `seatID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seat`
--

INSERT INTO `seat` (`seatID`) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(9),
(10),
(11),
(12),
(13),
(14),
(15),
(16),
(17),
(18),
(19),
(20),
(21),
(22),
(23),
(24),
(25),
(26),
(27),
(28),
(29),
(30),
(31),
(32),
(33),
(34),
(35),
(36),
(37),
(38),
(39),
(40);

-- --------------------------------------------------------

--
-- Table structure for table `upcomingmovie`
--

CREATE TABLE `upcomingmovie` (
  `movieID` int(11) NOT NULL,
  `movieName` varchar(50) DEFAULT NULL,
  `comingStatus` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upcomingmovie`
--

INSERT INTO `upcomingmovie` (`movieID`, `movieName`, `comingStatus`) VALUES
(6, 'Frozen 3', 'Coming July 2026'),
(7, 'Spider-Man 4', 'Coming August 2026'),
(8, 'The Batman 2', 'Coming September 2026');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`bookingID`),
  ADD KEY `customerID` (`customerID`),
  ADD KEY `movieID` (`movieID`),
  ADD KEY `seatID` (`seatID`),
  ADD KEY `datetimeID` (`datetimeID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customerID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `datetime`
--
ALTER TABLE `datetime`
  ADD PRIMARY KEY (`datetimeID`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`languageID`);

--
-- Indexes for table `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`movieID`),
  ADD KEY `categoryID` (`categoryID`);

--
-- Indexes for table `moviecategory`
--
ALTER TABLE `moviecategory`
  ADD PRIMARY KEY (`categoryID`);

--
-- Indexes for table `moviexdatetime`
--
ALTER TABLE `moviexdatetime`
  ADD PRIMARY KEY (`moviexdatetimeID`),
  ADD KEY `movieID` (`movieID`),
  ADD KEY `datetimeID` (`datetimeID`);

--
-- Indexes for table `moviexlanguage`
--
ALTER TABLE `moviexlanguage`
  ADD PRIMARY KEY (`movieLanguageID`),
  ADD KEY `movieID` (`movieID`),
  ADD KEY `languageID` (`languageID`);

--
-- Indexes for table `seat`
--
ALTER TABLE `seat`
  ADD PRIMARY KEY (`seatID`);

--
-- Indexes for table `upcomingmovie`
--
ALTER TABLE `upcomingmovie`
  ADD PRIMARY KEY (`movieID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `bookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `datetime`
--
ALTER TABLE `datetime`
  MODIFY `datetimeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `languageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `movie`
--
ALTER TABLE `movie`
  MODIFY `movieID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `moviecategory`
--
ALTER TABLE `moviecategory`
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `moviexdatetime`
--
ALTER TABLE `moviexdatetime`
  MODIFY `moviexdatetimeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `moviexlanguage`
--
ALTER TABLE `moviexlanguage`
  MODIFY `movieLanguageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seat`
--
ALTER TABLE `seat`
  MODIFY `seatID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`movieID`) REFERENCES `movie` (`movieID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`seatID`) REFERENCES `seat` (`seatID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_ibfk_4` FOREIGN KEY (`datetimeID`) REFERENCES `datetime` (`datetimeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `movie`
--
ALTER TABLE `movie`
  ADD CONSTRAINT `movie_ibfk_1` FOREIGN KEY (`categoryID`) REFERENCES `moviecategory` (`categoryID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `moviexdatetime`
--
ALTER TABLE `moviexdatetime`
  ADD CONSTRAINT `moviexdatetime_ibfk_1` FOREIGN KEY (`movieID`) REFERENCES `movie` (`movieID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `moviexdatetime_ibfk_2` FOREIGN KEY (`datetimeID`) REFERENCES `datetime` (`datetimeID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `moviexlanguage`
--
ALTER TABLE `moviexlanguage`
  ADD CONSTRAINT `moviexlanguage_ibfk_1` FOREIGN KEY (`movieID`) REFERENCES `movie` (`movieID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `moviexlanguage_ibfk_2` FOREIGN KEY (`languageID`) REFERENCES `language` (`languageID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
