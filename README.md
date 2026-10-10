# College Resource Management System

## Overview
This project is a web-based **College Resource Management System** built using PHP, MySQL, HTML, CSS, and JavaScript. It provides functionalities to manage and book various college resources efficiently, including classrooms, computer labs, meeting rooms, seminar halls, and equipment. 

## Features
*   **User Management**: Staff and student roles for accessing resources.
*   **Resource Booking**: Book classrooms, computer labs, seminar halls, meeting rooms, and equipment.
*   **Availability Checker**: Real-time checking to prevent double-booking.
*   **Maintenance Logs**: Track and manage maintenance schedules for different resources.
*   **Department Segregation**: Dedicated pages for different departments (BCA, CS, English, Maths, Physics, Tamil, etc.).
*   **Notifications**: Ajax-based notifications, including voice alerts (`voice_notification.js`).
*   **UI/UX Enhancements**: Support for dark mode (`dark_mode.css`, `dark_mode.js`) and responsive mobile views (`mobile_view.css`, `mobile_view.js`).
*   **Reporting**: Detailed reports on resource usage and bookings.

## Project Structure
Below is a high-level overview of the main files in the project:

### Core Files
*   `index.php` / `login.php` / `logout.php` - Authentication & Entry Point
*   `dashboard.php` - Main overview dashboard
*   `db.php` - Database connection settings
*   `sidebar.php` - Common navigation component
*   `college_resource (2).sql` - Database schema and initial data

### Resource Management (CRUD)
*   `resources.php` - Overview of all resources
*   **Classrooms**: `classroom.php`, `add_classrooms.php`, `edit_classroom.php`, `delete_classroom.php`
*   **Computer Labs**: `computer_labs.php`, `add_computer_lab.php`, `edit_computer_lab.php`, `delete_computer_lab.php`
*   **Seminar Halls**: `seminar_halls.php`, `add_seminar_halls.php`, `edit_seminar_hall.php`, `delete_seminar_halls.php`
*   **Meeting Rooms**: `meeting_rooms.php`, `add_meeting_room.php`, `edit_meeting_room.php`, `delete_meeting_room.php`
*   **Equipment**: `equipment.php`, `add_equipment.php`, `edit_equipment.php`, `delete_equipment.php`

### Booking & Requests
*   `bookings.php` - View all bookings
*   `add_booking.php` / `edit_booking.php` / `delete_booking.php` - Booking operations
*   `check_availability.php` - Verify if a resource is free
*   `booking_slip.php` - Generate a slip/receipt for approved bookings
*   `resource_requests.php`, `add_resource_request.php` - Requesting unlisted resources or special requests

### User & Staff Management
*   `staff.php`, `add_staff.php`
*   `students.php`, `add_student.php`, `it_students.php`
*   `departments.php`, `edit_department.php`

### Departments
*   `bca.php`, `cs.php`, `english.php`, `maths.php`, `physics.php`, `tamil.php`

### Maintenance
*   `maintenance.php`, `add_maintenance.php`, `edit_maintenance.php`

### Assets & Scripts
*   `dark_mode.css`, `dark_mode.js`
*   `mobile_view.css`, `mobile_view.js`
*   `voice_notification.js`
*   `ajax_search.php`
*   `uploads/` - Directory for uploaded files/documents

## Setup Instructions
1.  **Environment Setup**: Install XAMPP/WAMP/LAMP server.
2.  **Move Files**: Place the project folder into your `htdocs` (or `www`) directory.
3.  **Database Configuration**:
    *   Open phpMyAdmin (usually `http://localhost/phpmyadmin`).
    *   Create a new database named `college_resource`.
    *   Import the provided SQL file: `college_resource (2).sql`.
4.  **Database Connection**: Ensure `db.php` has the correct database credentials (username usually `root`, password empty by default).
5.  **Run Application**: Access the application through your browser at `http://localhost/college_resource`.

## Documentation
Additional project documentation and diagrams can be found in `College_RMS_Final_With_All_Diagrams.docx` and `Full_Extracted_Report.md`.
