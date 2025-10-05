# Secure PHP User Authentication System

**Version:** 1.0.0
**Last Updated:** 4th October 2025
**Author:** Luke Rudderham-Cozier

---

## Overview

This project is a secure, production-ready User Authentication System built with PHP and MySQL. It provides a robust foundation for any web application requiring user registration and login functionality. The system is designed with a security-first approach, implementing modern best practices to protect against common web vulnerabilities. It is specifically configured for deployment on AWS using Elastic Beanstalk and RDS.

---

## Features

-   **User Account Management:**
    -   Secure User Registration with password strength validation.
    -   User Login with support for either username or email.
    -   Secure User Logout and session destruction.
-   **Security:**
    -   **Password Hashing:** Uses PHP's native `password_hash()` and `password_verify()` (current BCRYPT algorithm).
    -   **SQL Injection Prevention:** All database queries are executed using PDO prepared statements.
    -   **Cross-Site Scripting (XSS) Prevention:** All user output is properly escaped using `htmlspecialchars()`.
    -   **Cross-Site Request Forgery (CSRF) Protection:** Forms are protected by CSRF tokens stored in the user's session.
    -   **Brute-Force Protection:**
        -   Locks user accounts temporarily after 5 consecutive failed login attempts.
        -   Blocks IP addresses with excessive failed login attempts.
-   **Database & Auditing:**
    -   Detailed logging of login attempts (successful and failed).
    -   Audit trail for key user actions (registration, login success/failure).
-   **Frontend:**
    -   Clean, modern UI with a dark/light theme switcher.
    -   Client-side validation hints for password strength and matching.

---

## Technology Stack

-   **Backend:** PHP 8+
-   **Database:** MySQL 8+
-   **Frontend:** HTML5, CSS3, JavaScript
-   **Deployment Environment:** AWS Elastic Beanstalk (Amazon Linux 2)
-   **Database Hosting:** AWS RDS for MySQL

---

## Project Structure

├── config/
│   └── database.php        # Database connection and query logic
├── index.php               # Root file, redirects to login
├── login.php               # Login form page
├── login_handle.php        # Handles login logic
├── register.php            # Registration form page
├── register_handle.php     # Handles registration logic
├── success.php             # User dashboard after successful login
├── logout.php              # Handles user logout
├── styles.css              # Main stylesheet
└── themeswitcher.js        # Logic for dark/light mode toggle

## Setup and Deployment

### 1. Database Schema

Before running the application, you need to set up the MySQL database with the following tables.

```sql
--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `account_locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `login_attempts`
--
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_agent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `user_audit_log`
--
CREATE TABLE `user_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
2. Local Development Setup
Prerequisites: A local server environment like XAMPP, MAMP, or WAMP with PHP and MySQL.

Clone the repository: git clone <your-repo-url>

Create Database: Create a new MySQL database (e.g., user_auth_system).

Import Schema: Run the SQL commands from the "Database Schema" section above to create the necessary tables.

Configure database.php: For local development, you can temporarily hardcode your local database credentials in config/database.php or use a local environment variable solution.

3. AWS Production Deployment (Elastic Beanstalk)
Create RDS Database:

In the AWS Console, navigate to RDS and create a new MySQL database.

Set a secure master username and password.

Important: Ensure the "Publicly accessible" setting is No.

Note the DB endpoint, username, and password.

Create Elastic Beanstalk Environment:

Navigate to Elastic Beanstalk and create a new application.

Create a new Web server environment with the "PHP" platform on "Amazon Linux 2".

During setup, you can upload the initial code bundle.

Configure Security Groups:

Find the security group used by your Elastic Beanstalk environment (e.g., awseb-e-uniquename-stack-AWSEBSecurityGroup-XYZ).

Navigate to the security group for your RDS instance.

Add an inbound rule to the RDS security group to allow MySQL/Aurora (port 3306) traffic from the Elastic Beanstalk security group. This allows your application to connect to the database securely.

Set Environment Variables:

In your Elastic Beanstalk environment, go to Configuration > Software > Edit.

Under Environment properties, add the following variables with the values from your RDS instance:

DB_HOST: The RDS instance endpoint.

DB_NAME: The database name you created.

DB_USERNAME: The master username for RDS.

DB_PASSWORD: The master password for RDS.

Deploy the Application:

Zip all the project files (index.php, config/, etc.).

Upload and deploy the zip file to your Elastic Beanstalk environment.

Security Considerations
HTTPS is Essential: This application should only be run over HTTPS in production. Use AWS Certificate Manager (ACM) with a Load Balancer to easily enable SSL/TLS.

Error Reporting: In production, ensure PHP display_errors is set to Off to avoid leaking server information.

Session Security: For enhanced security, configure PHP session cookies to be httponly and secure. This is recommended in the code and should be enabled once HTTPS is active.

AWS Credentials: Never hardcode AWS keys or database credentials in your code. Always use environment variables as configured in the deployment steps.

Future Improvements
Password Reset: Implement a secure "Forgot Password" feature using time-limited, single-use tokens sent via email.

Two-Factor Authentication (2FA): Add support for 2FA using apps like Google Authenticator for an extra layer of security.

Social Logins: Integrate OAuth for logins via Google, GitHub, etc.

User Roles & Permissions: Expand the users table to include roles (e.g., admin, user) to build a permission system.