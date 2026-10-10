# COLLEGE RESOURCE MANAGEMENT SYSTEM - FULL REPORT
COLLEGE RESOURCE MANAGEMENT SYSTEM
PROJECT DOCUMENTATION
Submitted by
M. SUGANTHI
Register No.: 241IT039
Under the Guidance of
Dr. S. Sampath MCA, M.Phil, Ph.D.,
Associate Professor & Head, Department of Computer Science
A PROJECT REPORT
Submitted in partial fulfillment of the requirements for the award of the degree of
BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY
DEPARTMENT OF COMPUTER SCIENCE
P.K.R. ARTS COLLEGE FOR WOMEN
(Autonomous), Gobichettipalayam – 638476
Affiliated to Bharathiar University
OCTOBER 2026
ACADEMIC YEAR 2026–2027
DECLARATION
I hereby declare that the project work entitled “COLLEGE RESOURCE MANAGEMENT SYSTEM”, submitted to the Department of Computer Science, P.K.R. Arts College for Women (Autonomous), Gobichettipalayam, is the record of the project work carried out by me during the academic year 2026–2027 under the guidance of my project guide, in partial fulfillment of the requirements for the award of the degree of Bachelor of Science in Information Technology.
I further declare that this project report has not been submitted, either in full or in part, for the award of any other degree or diploma.
Place: Gobichettipalayam
Date: __________________
Signature of the Candidate
M. Suganthi
Register No.: 241IT039
CERTIFICATE
This is to certify that the project work entitled “COLLEGE RESOURCE MANAGEMENT SYSTEM” is a record of work done by M. SUGANTHI, Reg.No: 241IT039 submitted to Department of Computer Science, P.K.R. Arts College for Women (Autonomous), Gobichettipalayam, under PART III in Semester–V for the completion of degree of Bachelor of Science in Information Technology.
Signature of the Guide
(Dr. S. Sampath MCA, M.Phil., Ph.D.)
Dr. O. P. Umamaheswari

Head of the Department
Dr. P. M. Gomathi M.C.A., M.Phil., Ph.D.

Dean of the Department
College seal:

Submitted to viva–voce to be held on:
Internal Examiner                                                    External Examiner
ACKNOWLEDGEMENT
I would like to express my sincere thanks to Thiru. P. N. Venkatachalam, Correspondent and Secretary of P.K.R. Arts College for Women (Autonomous), for providing the necessary facilities to complete this project.
I take this opportunity to express my sincere thanks to the Principal and the management of the college for their support and encouragement.
Dr. S. Sampath MCA, M.Phil, Ph.D.
I also thank the staff members of the Department of Computer Science, my family members and friends for their support and encouragement in completing this project.
Finally, I thank my family and friends for their encouragement and support in completing this project successfully.
ABSTRACT
The College Resource Management System is a web-based application designed to help manage the resources available in a college through a centralized platform. College resources such as departments, classrooms, computer laboratories, meeting rooms, seminar halls and equipment can be organized and maintained using the system.
The application provides resource booking features in which a resource can be booked by selecting the department, resource name, booking date, start time, end time and the name of the person making the booking. The system checks the booking information and displays a notification when a booking is successful or when the selected resource is already booked for the same time period.
The system also supports maintenance record management and resource requests. Maintenance details include the resource name, issue description, cost, status and maintenance date. Resource requests include the department, resource name, quantity and purpose. The administrator can approve, reject or delete requests.
The system is developed using HTML, CSS, JavaScript, PHP and MySQL, with XAMPP used as the local development environment. The application aims to reduce manual work, improve resource organization and make college resource booking and tracking easier.

## Keywords
College resources, resource booking, availability checking, maintenance management, resource requests, PHP, MySQL.
TABLE OF CONTENTS
CHAPTER 1
INTRODUCTION

## 1.1 Project Description
The College Resource Management System (CRMS) is a web-based application developed to organize and manage college resources in a centralized manner. Colleges use different resources for academic, administrative and event-related activities. These resources include classrooms, computer labs, meeting rooms, seminar halls and equipment.
Managing resource details and bookings manually can take time and may lead to confusion about availability. This system provides an organized interface for viewing and managing resources. The dashboard displays summary information about resources and provides navigation to the different management sections.
The booking module records the department, resource name, date, start time, end time and booked-by name. It checks whether the same resource has already been booked for the selected date and time and displays an appropriate notification. The maintenance module stores issue and repair details, while the resource request module allows requests to be reviewed by the administrator.

## 1.2 Objectives of the Project
To maintain college resource information in a centralized system.
To manage departments, classrooms, computer labs, meeting rooms, seminar halls and equipment.
To record bookings with date and time details.
To check resource availability and help prevent conflicting bookings.
To display notifications for successful or unavailable bookings.
To maintain resource maintenance details and status.
To manage resource requests and allow the administrator to approve, reject or delete them.
To reduce manual record keeping and make resource information easier to access.
CHAPTER 2
SYSTEM STUDY

## 2.1 Existing System
In a traditional college environment, information about classrooms, laboratories, seminar halls and equipment may be maintained in registers, spreadsheets or separate records. Users may need to contact the responsible person to check whether a resource is available. Maintenance details and requests may also be recorded separately.

## 2.2 Limitations of the Existing System
Checking resource availability may require manual communication.
Booking information can be difficult to track when records are kept separately.
Conflicting bookings may occur if records are not updated on time.
Maintenance history may be scattered across different records.
Resource requests can be delayed or difficult to monitor.
Manual data entry can lead to missing or incorrect information.

## 2.3 Proposed System
The proposed College Resource Management System provides a centralized web interface for managing resource information, bookings, maintenance records and resource requests. The administrator can navigate between modules from the dashboard and maintain the information in the database.
For bookings, the user enters the required resource and time details. The system checks the booking information and shows a notification based on availability. The maintenance and resource request sections help keep related records organized.

## 2.4 Advantages of the Proposed System
Centralized resource information.
Easier navigation through a dashboard.
Booking records include date and time details.
Availability checking helps reduce booking conflicts.
Notifications provide feedback about booking results.
Maintenance records are easier to track.
Resource requests can be approved, rejected or deleted by the administrator.
Reduced dependence on separate manual records.
CHAPTER 3
SYSTEM SPECIFICATION

## 3.1 Hardware Specification

## 3.2 Software Specification

## 3.3 Technology Specification

### 3.3.1 HTML
HTML is used to define the structure of the web pages, including headings, forms, tables, buttons and navigation elements.

### 3.3.2 CSS
CSS is used to control the appearance of the application, including colours, spacing, layouts, fonts and dashboard cards.

### 3.3.3 JavaScript
JavaScript can be used for client-side interactions and interface behaviour.

### 3.3.4 PHP
PHP is used for server-side processing, receiving form values and communicating with the database.

### 3.3.5 MySQL
MySQL is used to store and manage the resource, booking, maintenance and request information.

### 3.3.6 XAMPP and phpMyAdmin
XAMPP provides a local Apache, PHP and MySQL environment. phpMyAdmin provides a browser-based interface for managing the MySQL database.
CHAPTER 4
SYSTEM DESIGN

## 4.1 System Architecture
The application follows a simple web application architecture. The administrator interacts with the user interface through a web browser. HTML, CSS and JavaScript provide the interface, PHP processes requests on the server, and MySQL stores the data. XAMPP is used to run the application locally during development.
Administrator
↓
Web Browser / User Interface (HTML, CSS, JavaScript)
↓
PHP Application Logic
↓
MySQL Database

## 4.2 System Flow
Start the application and open the dashboard.
Select the required management section.
View or maintain department and resource details.
For a booking, select the department and resource and enter date and time.
The system checks the booking information and displays a success or unavailable notification.
Record maintenance information when a resource requires repair or service.
Review resource requests and approve, reject or delete them.
Store and retrieve the relevant information from the database.

## 4.3 Module Description

### 4.3.1 Dashboard
The dashboard acts as the main navigation page. It displays summary cards for resource categories and provides links to the management modules.

### 4.3.2 Department Management
This module is used to view and maintain department-related information required by the resource management system.

### 4.3.3 Resource Management
The system provides sections for classrooms, computer labs, meeting rooms, seminar halls and equipment. These sections help the administrator organize the resources available in the college.

### 4.3.4 Booking Management
The booking form includes Department, Resource Name, Booking Date, Start Time, End Time and Booked By. The system checks booking availability and displays a notification for the booking result.

### 4.3.5 Maintenance Management
The maintenance module records Resource Name, Issue Description, Cost, Status and Maintenance Date. These details help track reported issues and maintenance progress.

### 4.3.6 Resource Request Management
A resource request contains Department, Resource Name, Quantity and Purpose. The administrator can approve, reject or delete a request based on the college's requirements.

## 4.4 Data Flow Diagram (DFD)
The following is a high-level text representation of the data flow. The exact data flow can be updated to match the final implementation.
Administrator
↓ Resource / Booking / Maintenance / Request Details
COLLEGE RESOURCE MANAGEMENT SYSTEM
↓ Store and retrieve records
MySQL Database

## 4.5 ER Diagram (Conceptual)
The conceptual data model contains the main entities handled by the application. The actual relationships and table names must be confirmed from the final database structure.
Figure 4.5: Entity Relationship Diagram of the College Resource Management System

## 4.6 Database Design
The MySQL database stores the information required by the application. Resource records support the management screens, booking records support date and time availability checks, maintenance records store issue and repair information, and resource request records store the details submitted for administrator review.
Note: Insert the final table names, column names, primary keys and foreign keys from phpMyAdmin after confirming the actual database schema. This report does not assume unverified table names.
4.7 SYSTEM DESIGN DIAGRAMS
Figure 4.1: System Architecture Diagram
Figure 4.2: DFD Level 0 (Context Diagram)
Figure 4.3: DFD Level 1
Figure 4.4: Conceptual ER Diagram
Note: This is a conceptual ER diagram based on the visible application modules. Verify entity names and fields against the actual MySQL tables in phpMyAdmin.
Figure 4.5: Booking Process Flowchart
Figure 4.6: Use Case Diagram
CHAPTER 5
IMPLEMENTATION AND TESTING

## 5.1 Implementation
The system is developed as a web application using HTML, CSS, JavaScript, PHP and MySQL. Project files are edited in Visual Studio Code and run locally through XAMPP. phpMyAdmin is used to create and manage the database.
The dashboard provides navigation to the resource management modules. The booking module accepts the required booking details and provides a notification according to the availability check. The maintenance module stores issue-related details, while the resource request module supports administrator review and status updates.

## 5.2 Testing Methodology
Testing should be performed by entering valid and invalid data in each module and checking whether the application responds correctly. Database records should also be checked in phpMyAdmin to confirm that submitted information is stored or updated as expected.

## 5.3 Test Cases

## 5.4 Test Results
Complete this section after testing the application. For each test case, record whether the actual result matches the expected result. Do not mark a test as Pass until it has been run and verified in the application.
Overall test status: ____________________
Tested by: ____________________________
Date: _________________________________
CHAPTER 6
CONCLUSION AND FUTURE ENHANCEMENT

## 6.1 Conclusion
The College Resource Management System provides a centralized way to organize college resource details, manage bookings, record maintenance information and review resource requests. The dashboard makes the modules easier to access, while the booking availability check and notifications help users understand the booking result.
The application uses HTML, CSS, JavaScript, PHP and MySQL and is developed in a local XAMPP environment. After final testing and verification of the database, the system can support more organized resource management and reduce dependence on separate manual records.

## 6.2 Future Enhancement
Develop a mobile-friendly version or a dedicated mobile application.
Add user authentication and role-based access if required by the college.
Provide printable or downloadable booking and maintenance reports.
Add search, filtering and date-based booking views.
Provide email or in-app notifications if the required services are configured.
Add resource usage reports and maintenance history summaries.
CHAPTER 7
BIBLIOGRAPHY
The following documentation and learning resources may be consulted during development:
PHP Documentation. https://www.php.net/docs.php
MySQL Documentation. https://dev.mysql.com/doc/
MDN Web Docs – HTML. https://developer.mozilla.org/en-US/docs/Web/HTML
MDN Web Docs – CSS. https://developer.mozilla.org/en-US/docs/Web/CSS
MDN Web Docs – JavaScript. https://developer.mozilla.org/en-US/docs/Web/JavaScript
Apache Friends – XAMPP. https://www.apachefriends.org/
Visual Studio Code Documentation. https://code.visualstudio.com/docs
W3Schools – HTML, CSS, JavaScript, PHP and SQL tutorials. https://www.w3schools.com/
ANNEXURE A
SOURCE CODE
The following selected code excerpts illustrate the main functions of the College Resource Management System. They are short excerpts prepared from the code visible in the submitted screenshots. Refer to the actual project files for the complete implementation.

## A.1 Suggested source files
Database connection file (actual filename): ____________________
Dashboard file: dashboard.php
Resource management files: ____________________
Booking management file: ____________________
Maintenance management file: ____________________
Resource request management file: ____________________
A.1 db.php — Database Connection
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college_resource";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
die("Connection failed: " . mysqli_connect_error());
}
?>
Explanation: This file connects the PHP application to the MySQL database and reports a connection error if the connection fails.
A.2 dashboard.php — Dashboard and Resource Counts
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
header("Location: login.php");
exit();
}
include 'db.php';
function getResourceCount($conn, $category) {
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM resources WHERE type = ?");
if (!$stmt) {
return 0;
}
$stmt->bind_param("s", $category);
$stmt->execute();
}
Explanation: This excerpt checks the administrator session, includes the database connection, and begins a function used to count resources for the dashboard.
A.3 departments.php — Department Management
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
header("Location: login.php");
exit();
}
include 'db.php';
// Ensure departments table exists with matching database structure
$conn->query("CREATE TABLE IF NOT EXISTS departments (
id INT AUTO_INCREMENT PRIMARY KEY,
dept_code VARCHAR(20),
dept_name VARCHAR(100) NOT NULL
)");
// Handle Add Department
if (isset($_POST['add_dept'])) {
$dept_name = trim($_POST['dept_name']);
Explanation: This excerpt checks administrator access, creates the departments table when needed, and starts the department-add operation.
A.4 resources.php — Resource Management
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
header("Location: login.php");
exit();
}
include 'db.php';
$type = isset($_GET['type']) ? $_GET['type'] : 'Classroom';
// Ensure departments table exists with matching structure
$conn->query("CREATE TABLE IF NOT EXISTS departments (
id INT AUTO_INCREMENT PRIMARY KEY,
dept_code VARCHAR(20),
dept_name VARCHAR(100) NOT NULL
)");
// Ensure columns exist in resources table
Explanation: This excerpt selects a resource type and prepares the database structure used by the resource management page.
A.5 bookings.php — Booking Management
<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
header("Location: login.php");
exit();
}
if ($_SESSION['admin_logged_in'] !== true) {
header("Location: login.php");
exit();
}
include 'db.php';
$conn->query("CREATE TABLE IF NOT EXISTS departments (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
$conn->query("CREATE TABLE IF NOT EXISTS resources (
Explanation: This excerpt checks the admin session, connects to the database, and begins ensuring the tables required for bookings exist.
A.6 check_availability.php — Resource Availability
<?php
include('db.php');
$resources = [];
$selected_type = $_POST['resource_type'] ?? 'classrooms';
$check_date = $_POST['check_date'] ?? '';
if ($check_date) {
// Dynamically select table based on user choice
$table = "classrooms";
if ($selected_type == 'labs') $table = 'computer_labs';
elseif ($selected_type == 'meeting_rooms') $table = 'meeting_rooms';
elseif ($selected_type == 'seminar_halls') $table = 'seminar_halls';
$query = "SELECT * FROM $table";
$result = mysqli_query($conn, $query);
}
?>
Explanation: This excerpt reads the selected resource type and date, then chooses the related resource table for availability information.
A.7 maintenance.php — Maintenance Management
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
header("Location: login.php");
exit();
}
include 'db.php';
// Maintenance records are handled through the database connection.
// The page manages resource issues and maintenance information.
?>
Explanation: The screenshot of this file was not clear enough to transcribe every line reliably. This short description identifies its role; replace this excerpt with the exact first 15 code lines from the actual file before final submission.
A.8 resource_requests.php — Resource Requests
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
header("Location: login.php");
exit();
}
include 'db.php';
// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS resource_requests (
id INT AUTO_INCREMENT PRIMARY KEY,
department_name VARCHAR(100) NOT NULL,
resource_name VARCHAR(100) NOT NULL,
quantity INT DEFAULT 1,
purpose TEXT NOT NULL,
status VARCHAR(50) DEFAULT 'Pending',
Explanation: This excerpt creates the resource request table with department, resource, quantity, purpose, and request status fields.
A.9 equipment.php — Equipment Management
<?php
include "db.php";
$result = $conn->query("SELECT * FROM equipment ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Equipment Management | College RMS</title>
<style>
* {
box-sizing: border-box;
font-family: Arial, sans-serif;
}
Explanation: This excerpt retrieves equipment records from the database and begins the HTML/CSS for the equipment management page.
A.10 approve_booking.php — Booking Status Update
<?php
include "db.php";
if (isset($_GET['id']) && isset($_GET['status'])) {
$id = intval($_GET['id']);
$status = $_GET['status'];
if ($status == "Approved" || $status == "Rejected") {
$stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();
$stmt->close();
}
}
header("Location: bookings.php");
exit;
?>
Explanation: This file updates a booking status to Approved or Rejected and returns to the bookings page.
A.11 login.php — Administrator Login
<?php
session_start();
include 'db.php';
// Redirect an already logged-in administrator
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
header("Location: dashboard.php");
exit();
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$username = trim($_POST['username']);
$password = trim($_POST['password']);
// Check the configured administrator credentials
Explanation: This excerpt starts the session, loads the database connection, redirects an existing session, and handles submitted login credentials. The screenshot displayed a default password; do not expose it in the printed report.
ANNEXURE B: FINAL OUTPUT SCREENSHOTS
Figure B.1: College RMS Dashboard
Figure B.2: Booking Management
Figure B.3: Resource Requests Management
