-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2026 at 07:06 PM
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
-- Database: `college_resource`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `department` varchar(20) NOT NULL,
  `resource_name` varchar(150) NOT NULL,
  `booking_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `booked_by` varchar(100) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `department`, `resource_name`, `booking_date`, `status`, `booked_by`, `start_time`, `end_time`) VALUES
(2, 'Computer Science', 'iot wires', '2026-10-08', 'Pending', '241it039', '10:00:00', '11:00:00'),
(3, 'Computer Science', 'SPEAKER', '2026-10-06', 'Pending', '241IT038', '11:00:00', '12:00:00'),
(4, 'Computer Science', 'PG LAB-1', '2026-10-07', 'Pending', '3 B.SC IT', '09:30:00', '12:30:00'),
(5, 'Information Technolo', 'SENSOR IOT', '2026-10-09', 'Pending', '241IT028', '12:00:00', '02:00:00'),
(6, 'Computer Science', '3B.SC IT(PG)', '2026-10-09', 'Pending', '241it039', '19:09:00', '12:00:00'),
(7, 'Information Technolo', 'SENSOR IOT', '2026-10-09', 'Pending', '241it039', '12:00:00', '02:00:00'),
(8, 'Computer Science', '2B.C.A(PG)', '2026-10-09', 'Pending', '241it022', '10:00:00', '12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

CREATE TABLE `classrooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `class_name` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `year` varchar(50) DEFAULT NULL,
  `block_name` varchar(100) DEFAULT NULL,
  `capacity` int(11) DEFAULT 0,
  `status` varchar(30) DEFAULT 'Available',
  `category` varchar(100) DEFAULT 'Classroom'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`id`, `room_name`, `class_name`, `department`, `year`, `block_name`, `capacity`, `status`, `category`) VALUES
(1, 'A101', 'B.Sc Computer Science', 'Computer Science', '1st Year', 'A Block', 60, 'Available', 'Classroom'),
(2, 'A102', 'B.Sc Information Technology', 'Information Technology', '2nd Year', 'A Block', 60, 'Available', 'Classroom'),
(3, 'B201', 'BCA', 'Computer Applications', '3rd Year', 'B Block', 50, 'Available', 'Classroom');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `dept_code` varchar(20) NOT NULL,
  `dept_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `dept_code`, `dept_name`) VALUES
(10, 'TAM', 'Tamil'),
(38, 'ENG', 'English'),
(39, 'MAT', 'Mathematics'),
(40, 'PHY', 'Physics'),
(41, 'CS', 'Computer Science'),
(44, 'COM', 'Commerce'),
(46, '', 'management'),
(47, '', 'SPORTS'),
(48, '', 'placement');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `equipment_name` varchar(150) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `status` varchar(30) DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `issue_description` text NOT NULL,
  `cost` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'Pending',
  `maintenance_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `resource_name`, `issue_description`, `cost`, `status`, `maintenance_date`, `created_at`) VALUES
(1, 'iot wires', 'not  working', 0.00, 'In Progress', '2026-10-08', '2026-10-08 13:40:51'),
(2, 'MIKE', 'DAMAGED', 0.00, 'Pending', '2026-10-05', '2026-10-08 15:55:31'),
(3, '1B.SC IT(PG)', 'not worked', 0.00, 'Completed', '2026-10-03', '2026-10-09 00:21:00');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) DEFAULT 'Unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `resource_name` varchar(150) NOT NULL,
  `resource_type` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Available',
  `category` varchar(100) DEFAULT 'General',
  `quantity` int(11) DEFAULT 0,
  `capacity` int(11) DEFAULT 0,
  `resource_code` varchar(50) DEFAULT NULL,
  `floor_number` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `resource_name`, `resource_type`, `department`, `location`, `status`, `category`, `quantity`, `capacity`, `resource_code`, `floor_number`) VALUES
(13, 'MIKE', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(14, '1B.SC IT(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '12', '2nd Floor'),
(15, '2B.SC IT(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '15', '3rd Floor'),
(16, '3B.SC IT(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '2', 'Ground Floor'),
(17, '1B.C.A(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '12', '2nd Floor'),
(18, '2B.C.A(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '14', '3rd Floor'),
(19, '3B.C.A(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '20', '3rd Floor'),
(20, '1B.SC CS(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 50, '10', '2nd Floor'),
(21, '2B.SC CS(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 50, '16', '3rd Floor'),
(22, '3B.SC CS(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '19', '3rd Floor'),
(23, '1AI&ML(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '11', '2nd Floor'),
(24, '2AI&ML(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '17', '2nd Floor'),
(25, '3AI&ML(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '4', 'Ground Floor'),
(26, '1CYBER(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '13', '2nd Floor'),
(27, '2CYBER(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 40, '13', '3rd Floor'),
(29, '1BA TAMIL(PG)', NULL, 'Tamil', NULL, 'Available', 'Classroom', 0, 30, '7', '1st Floor'),
(30, '2BA TAMIL(PG)', NULL, 'Tamil', NULL, 'Available', 'Classroom', 0, 40, '6', '1st Floor'),
(31, '3BA TAMIL(PG)', NULL, 'Tamil', NULL, 'Available', 'Classroom', 0, 40, '5', '1st Floor'),
(32, '1MA TAMIL(PG)', NULL, 'Tamil', NULL, 'Available', 'Classroom', 0, 40, '8', '1st Floor'),
(33, '2MA TAMIL(PG)', NULL, 'Tamil', NULL, 'Available', 'Classroom', 0, 40, '9', '1st Floor'),
(34, '1M.C.A(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 50, '3', 'Ground Floor'),
(35, '2M.C.A(PG)', NULL, 'Computer Science', NULL, 'Available', 'Classroom', 0, 50, '1', 'Ground Floor'),
(36, '1BA ENG(UG)', NULL, 'English', NULL, 'Available', 'Classroom', 0, 30, '19', '2nd Floor'),
(37, '2BA ENG(UG)', NULL, 'English', NULL, 'Available', 'Classroom', 0, 40, '20', '2nd Floor'),
(38, '3BA ENG(UG)', NULL, 'English', NULL, 'Available', 'Classroom', 0, 40, '21', '2nd Floor'),
(39, '1MA ENG(UG)', NULL, 'English', NULL, 'Available', 'Classroom', 0, 40, '21', '2nd Floor'),
(40, '2MA ENG(UG)', NULL, 'English', NULL, 'Available', 'Classroom', 0, 40, '22', '2nd Floor'),
(41, '1B.B.A(UG)', NULL, 'management', NULL, 'Available', 'Classroom', 0, 40, '7', '2nd Floor'),
(42, '2B.B.A(UG)', NULL, 'management', NULL, 'Available', 'Classroom', 0, 40, '5', '2nd Floor'),
(43, '3B.B.A(UG)', NULL, 'management', NULL, 'Available', 'Classroom', 0, 40, '6', '2nd Floor'),
(45, '2M.B,A(PG)', NULL, 'management', NULL, 'Available', 'Classroom', 0, 60, '101', '2nd Floor'),
(46, '1M.B.A(PG)', NULL, 'management', NULL, 'Available', 'Classroom', 0, 60, '102', '2nd Floor'),
(47, '1B.COM(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '25', '3rd Floor'),
(48, '2B.COM(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '35', '3rd Floor'),
(49, '3B.COM(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '26', '3rd Floor'),
(50, '1B.COM IT(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '24', '3rd Floor'),
(51, '2B.COM IT(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '23', '3rd Floor'),
(52, '3B.COM IT(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '36', '3rd Floor'),
(53, '1B.COM CA(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '34', '3rd Floor'),
(54, '2B.COM CA(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '27', '3rd Floor'),
(55, '3B.COM CA(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '30', '3rd Floor'),
(56, '1B.COM PA(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '29', '3rd Floor'),
(57, '2B.COM PA(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '33', '3rd Floor'),
(58, '3B.COM PA(UG)', NULL, 'Commerce', NULL, 'Available', 'Classroom', 0, 40, '28', '3rd Floor'),
(61, '1B,SC MATH(UG)', NULL, 'Mathematics', NULL, 'Available', 'Classroom', 0, 40, '18', '1st Floor'),
(62, '2,B SC MATH(UG)', NULL, 'Mathematics', NULL, 'Available', 'Classroom', 0, 40, '17', '1st Floor'),
(63, '3B.SC MATH(UG)', NULL, 'Mathematics', NULL, 'Available', 'Classroom', 0, 40, '16', '1st Floor'),
(64, '1M.SC MATH(UG)', NULL, 'Mathematics', NULL, 'Available', 'Classroom', 0, 40, '14', '1st Floor'),
(65, '2M.SC MATH(UG)', NULL, 'Mathematics', NULL, 'Available', 'Classroom', 0, 40, '15', '1st Floor'),
(66, '1B.SC PHY(UG)', NULL, 'Physics', NULL, 'Available', 'Classroom', 0, 40, '10', '1st Floor'),
(67, '2B.SC PHY(UG)', NULL, 'Physics', NULL, 'Available', 'Classroom', 0, 40, '11', '1st Floor'),
(68, '3B.SC PHY(UG)', NULL, 'Physics', NULL, 'Available', 'Classroom', 0, 40, '9', '1st Floor'),
(69, '1M.SC PHY(UG)', NULL, 'Physics', NULL, 'Available', 'Classroom', 0, 40, '12', '1st Floor'),
(70, '2M.SC PHY(UG)', NULL, 'Physics', NULL, 'Available', 'Classroom', 0, 40, '5', '1st Floor'),
(71, 'UG LAB', NULL, 'Commerce', NULL, 'Available', 'Computer Lab', 0, 60, '08', 'Ground Floor'),
(72, 'PG LAB-1', NULL, 'Computer Science', NULL, 'Available', 'Computer Lab', 0, 60, '2', '1st Floor'),
(73, 'PG LAB-2', NULL, 'Computer Science', NULL, 'Available', 'Computer Lab', 0, 60, '2', '1st Floor'),
(74, 'PG LAB-3', NULL, 'Computer Science', NULL, 'Available', 'Computer Lab', 0, 60, '111', '2nd Floor'),
(75, 'LIB LAB', NULL, 'Computer Science', NULL, 'Available', 'Computer Lab', 0, 60, '4', '3rd Floor'),
(76, 'PHY LAB(UG)', NULL, 'Physics', NULL, 'Available', 'Computer Lab', 0, 60, '4', '2nd Floor'),
(77, 'TAILORING LAB(PG)', NULL, 'Computer Science', NULL, 'Available', 'Computer Lab', 0, 30, '5', 'Ground Floor'),
(78, 'BOARD ROOM(UG)', NULL, 'management', NULL, 'Available', 'Meeting Room', 0, 60, '6', 'Ground Floor'),
(79, 'HUB ROOM(UG)', NULL, 'Commerce', NULL, 'Available', 'Meeting Room', 0, 30, '7', 'Ground Floor'),
(80, 'INSTRUCTION ROOM(UG)', NULL, 'Commerce', NULL, 'Available', 'Meeting Room', 0, 50, '10', '2nd Floor'),
(81, 'STORE ROOM(UG)', NULL, 'English', NULL, 'Available', 'Meeting Room', 0, 20, '1', 'Ground Floor'),
(82, 'OFFICE ROOM(UG)', NULL, 'Commerce', NULL, 'Available', 'Meeting Room', 0, 60, '3', 'Ground Floor'),
(83, 'CONTOLL SECTION(PG)', NULL, 'Computer Science', NULL, 'Available', 'Meeting Room', 0, 60, '103', 'Ground Floor'),
(84, 'DATA CENTRE(UG)', NULL, 'Commerce', NULL, 'Available', 'Meeting Room', 0, 60, '5', '1st Floor'),
(85, 'SPORTS ROOM(UG)', NULL, 'Commerce', NULL, 'Available', 'Meeting Room', 0, 20, '6', 'Ground Floor'),
(86, 'SPORTS GEM(PG)', NULL, 'Computer Science', NULL, 'Available', 'Meeting Room', 0, 30, '35', '3rd Floor'),
(87, 'NSS&YRC (PG)', NULL, 'Computer Science', NULL, 'Available', 'Meeting Room', 0, 10, '4', 'Ground Floor'),
(88, 'CULUTRAL ROOM(PG)', NULL, 'Computer Science', NULL, 'Available', 'Meeting Room', 0, 60, '18', '3rd Floor'),
(90, 'M.B.A SEMI(PG)', NULL, 'management', NULL, 'Available', 'Seminar Hall', 0, 100, '1', 'Ground Floor'),
(91, 'UG CONFERENCE HALL', NULL, 'Commerce', NULL, 'Available', 'Seminar Hall', 0, 200, '37', '3rd Floor'),
(92, 'PG SEMINAR HALL', NULL, 'Computer Science', NULL, 'Available', 'Seminar Hall', 0, 60, '2', '1st Floor'),
(93, 'MONITORS', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(94, 'SWITCHES', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 5, 0, NULL, NULL),
(95, 'ROUTERS', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(96, 'IOT KITS WIRES', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 20, 0, NULL, NULL),
(97, 'CTYPE IOT', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 12, 0, NULL, NULL),
(98, 'SMART BOARD', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(99, 'MOTHER BOARD(IOT)', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 12, 0, NULL, NULL),
(100, 'JUMBER  WIRE', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 12, 0, NULL, NULL),
(101, 'SENSOR IOT', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 12, 0, NULL, NULL),
(102, 'ESP32 IOT', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 12, 0, NULL, NULL),
(103, 'OLED LIGHTS IOT', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(104, 'SCREW GAUGES', NULL, 'Physics', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(105, 'LENSES', NULL, 'Physics', NULL, 'Available', 'Equipment', 15, 0, NULL, NULL),
(106, 'MULTIMETER', NULL, 'Physics', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(107, 'RESISTANCE BOX', NULL, 'Physics', NULL, 'Available', 'Equipment', 15, 0, NULL, NULL),
(108, 'GRAPH BOARD', NULL, 'Mathematics', NULL, 'Available', 'Equipment', 5, 0, NULL, NULL),
(109, 'CALCULATOR', NULL, 'Mathematics', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(110, 'AUDIO SYSTEM', NULL, 'English', NULL, 'Available', 'Equipment', 5, 0, NULL, NULL),
(111, 'SPEAKER', NULL, 'management', NULL, 'Available', 'Equipment', 5, 0, NULL, NULL),
(112, 'KEYBORAD', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(113, 'PROJECTOR', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 5, 0, NULL, NULL),
(114, 'MICRO PHONE', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 2, 0, NULL, NULL),
(115, 'FOOTBALL', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(116, 'BASKET BALL', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(117, 'VOLLEYBALLS', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(118, 'KABBADI ROBES', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 20, 0, NULL, NULL),
(119, 'SCORING BOARD', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 12, 0, NULL, NULL),
(120, 'DUMBELLS', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 5, 0, NULL, NULL),
(121, 'WEIGHT PLATES', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(122, 'POWER RACK', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(123, 'MATES', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(124, 'YOGA MATES', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 5, 0, NULL, NULL),
(125, 'GRIPS', NULL, 'SPORTS', NULL, 'Available', 'Equipment', 12, 0, NULL, NULL),
(126, 'CPU', NULL, 'Computer Science', NULL, 'Available', 'Equipment', 10, 0, NULL, NULL),
(127, 'PRINTER', NULL, 'management', NULL, 'Available', 'Equipment', 5, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `resource_requests`
--

CREATE TABLE `resource_requests` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `resource_name` varchar(100) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `purpose` text NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resource_requests`
--

INSERT INTO `resource_requests` (`id`, `department_name`, `resource_name`, `quantity`, `purpose`, `status`, `created_at`) VALUES
(1, 'Computer Science', 'jumber wires', 1, 'lab uses', 'Approved', '2026-10-08 14:14:27'),
(2, 'Computer Science', 'jumber wires', 1, 'PROJECT', 'Pending', '2026-10-08 16:01:21'),
(3, 'Computer Science', 'keyboard', 1, 'practial uses', 'Rejected', '2026-10-09 00:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `seminar_halls`
--

CREATE TABLE `seminar_halls` (
  `id` int(11) NOT NULL,
  `hall_name` varchar(100) NOT NULL,
  `capacity` int(11) DEFAULT 0,
  `location` varchar(150) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `department`, `email`, `phone`) VALUES
(2, 'suganthi', 'B,Sc Information Technology', 'sugiviji22@gmail.com', '9025102189'),
(3, '', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resource_requests`
--
ALTER TABLE `resource_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seminar_halls`
--
ALTER TABLE `seminar_halls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `resource_requests`
--
ALTER TABLE `resource_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seminar_halls`
--
ALTER TABLE `seminar_halls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
