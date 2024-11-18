
# Employee Directory Management System

## Overview
The Employee Directory Management System is a web-based application that allows administrators to manage employee records efficiently. It includes functionalities for adding, viewing, editing, and deleting employee records.

## Features
- Secure Admin Login with session management.
- Add new employees with details including profile photos.
- View employees in a list format.
- Edit employee details with support for updating profile photos.
- Delete employee records with confirmation prompts.
- Secure data handling with hashed passwords and prepared statements to prevent SQL injection.

---

## Prerequisites
1. **XAMPP**:
   - Download and install XAMPP from [apachefriends.org](https://www.apachefriends.org/index.html).
   - Ensure `Apache` and `MySQL` services are running.

2. **PHP Version**:
   - Ensure your system supports PHP (preferably version 7.4 or above).

---

## Setup Instructions
### 1. Clone or Download the Project
- Place the project folder (`employee-directory`) in the `htdocs` directory of your XAMPP installation (e.g., `C:\xampp\htdocs\employee-directory`).

### 2. Create the Database
- Open `phpMyAdmin` (via XAMPP control panel or `http://localhost/phpmyadmin`).
- Create a new database named `employee_directory`.

### 3. Generate Tables
Run the following SQL queries in the `employee_directory` database to generate the required tables:

#### Admin Table
```sql
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
```

#### Employees Table
```sql
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    photo_path VARCHAR(255) DEFAULT NULL
);
```

### 4. Configure the Project
- Open the `includes/config.php` file and update the database connection details:
  ```php
  $host = 'localhost';
  $db = 'employee_directory';
  $user = 'root';  // Default XAMPP username
  $password = '';  // Default XAMPP password (leave blank if not set)
  $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
  $pdo = new PDO($dsn, $user, $password);
  ```

---

## How to Use
1. **Access the Login Page**:
   - Open your browser and navigate to `http://localhost/employee-directory/login.php`.

2. **Admin Login**:
   - Register an admin account via the `register.php` page or insert credentials directly into the `admin` table:
     ```sql
     INSERT INTO admin (username, password) VALUES ('admin', 'hashed_password');
     ```
     Replace `hashed_password` with the output of `password_hash('your_password', PASSWORD_DEFAULT)` in PHP.

3. **Manage Employees**:
   - After logging in, use the admin dashboard to:
     - Add new employees.
     - View employee records.
     - Edit employee details.
     - Delete employee records.

---

## Security Features
- Passwords are securely hashed using `password_hash()`.
- SQL queries use prepared statements to prevent SQL injection.
- Session-based authentication restricts access to authorized users.

---

## Assumptions
- PHP file uploads are stored in the `/uploads` directory, which must have write permissions.
- No file size or type validation is implemented for profile photos (optional improvement).

---

## Future Enhancements
- Implement card view for employee records.
- Add file type and size validation for profile photo uploads.
- Introduce pagination for large employee lists.

---
