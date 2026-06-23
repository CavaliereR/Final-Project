-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2026 at 05:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `onlinequizdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `questionID` int(11) NOT NULL,
  `quizID` int(11) DEFAULT NULL,
  `questionText` text DEFAULT NULL,
  `choiceA` varchar(255) DEFAULT NULL,
  `choiceB` varchar(255) DEFAULT NULL,
  `choiceC` varchar(255) DEFAULT NULL,
  `choiceD` varchar(255) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `question_type` enum('mcq','file') DEFAULT 'mcq',
  `is_file_question` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`questionID`, `quizID`, `questionText`, `choiceA`, `choiceB`, `choiceC`, `choiceD`, `answer`, `question_type`, `is_file_question`) VALUES
(1, 1, 'who is sponge bob', 'me', 'you', 'friend', 'him', 'her', 'mcq', 0),
(2, 1, 'who is sponge bob', 'me', 'you', 'friend', 'him', 'her', 'mcq', 0),
(3, 2, 'Hawk', 'choo', '21', 'john', 'Tuah', 'Tuah', 'mcq', 0),
(4, 2, '', '', '', '', '', '', 'mcq', 0),
(5, 2, 'hawk', 'tuah', 'vine boom', 'hiyah', 'dorya', 'tuah', 'mcq', 0),
(6, 2, 'nuh uh', '2', 'yuh uh', 'a', 'c', 'yuh uh', 'mcq', 0),
(7, 2, 'Sarah Connor?', '', '', '', '', '', 'file', 0);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quizID` int(11) NOT NULL,
  `quizTitle` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `timeLimit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quizID`, `quizTitle`, `description`, `timeLimit`) VALUES
(1, 'Spongebob', 'it is about sponge bob', 10),
(2, 'Math', 'Do some math', 3);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `resultID` int(11) NOT NULL,
  `studentID` int(11) DEFAULT NULL,
  `quizID` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `dateTaken` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`resultID`, `studentID`, `quizID`, `score`, `dateTaken`) VALUES
(1, 5, 2, 1, '2026-06-23 10:22:37'),
(2, 5, 2, 4, '2026-06-23 10:24:03'),
(3, 6, 2, 3, '2026-06-23 10:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submissionID` int(11) NOT NULL,
  `studentID` int(11) NOT NULL,
  `questionID` int(11) NOT NULL,
  `quizID` int(11) NOT NULL,
  `filePath` varchar(255) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Teacher','Student') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `fullname`, `email`, `password`, `role`) VALUES
(1, 'Dela', 'mama@gmail.com', '$2y$10$HJ.41xr.LpFPOz1qeOXppuGvbw2mA6LrT7DZktloQaNfqYdh4iVqK', 'Teacher'),
(3, 'user1', '12345@gmail.com', '12345', 'Teacher'),
(4, 'Robb Jason Sedilla', 'robbjason28@gmail.com', '$2y$10$jV2znS2OJpE/SP4SauM0eOLvKr/M2iUEs/lLvzQhP7Jl32jNGgbXy', 'Teacher'),
(5, 'john kahner', 'john@gmail.com', '$2y$10$Q6zgDMdGtDcSC2F2nlpsVOAoXrLIUaiwcEzS8l5W1QnfCLCYsm786', 'Student'),
(6, 'user2', 'user@gmail.com', '$2y$10$U1dXVw8k6SVPt8bpPGpzC.pI2GmPQ4vW5ps68T3ClUxR0TWxOrY3K', 'Student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`questionID`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quizID`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`resultID`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submissionID`),
  ADD KEY `studentID` (`studentID`),
  ADD KEY `questionID` (`questionID`),
  ADD KEY `quizID` (`quizID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `questionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quizID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `resultID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submissionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
