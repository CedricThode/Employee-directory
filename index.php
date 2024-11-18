<?php
session_start();
require './includes/config.php'; // connect to database

// Check if admin is logged in if not, redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $job_title = $_POST['job_title'];
    $department = $_POST['department'];
    $photo_path = null;

    // Validation
    if (empty($name) || empty($age) || empty($job_title) || empty($department)) {
        $error_message = "All fields are required!";
    } else {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = './uploads/';
            $filename = basename($_FILES['photo']['name']);
            $target_path = $upload_dir . uniqid() . '_' . $filename;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
                $photo_path = $target_path;
            } else {
                $error_message = "Failed to upload photo.";
            }
        }

        // Insert employee data into the database
        if (!isset($error_message)) {
            $stmt = $pdo->prepare("INSERT INTO employees (name, age, job_title, department, photo_path) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $age, $job_title, $department, $photo_path])) {
                $_SESSION['message'] = "Employee added successfully!";
                header("Location: index.php");
                exit;
            } else {
                $error_message = "Failed to add employee.";
            }
        }
    }
}

// Display success or failure after deleting an employee
if (isset($_SESSION['message'])) {
    echo '<div class="message">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}

// Gets all employees from the database
$stmt = $pdo->prepare("SELECT * FROM employees");
$stmt->execute();
$employees = $stmt->fetchAll();

// delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_employee'])) {
    $employee_id = $_POST['employee_id'];

    // Fetch the photo path 
    $stmt = $pdo->prepare("SELECT photo_path FROM employees WHERE id = ?");
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch();

    if ($employee) {
        // Delete photo file if it exists
        if (!empty($employee['photo_path']) && file_exists($employee['photo_path'])) {
            unlink($employee['photo_path']); 
        }

        // Delete employee from database
        $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
        if ($stmt->execute([$employee_id])) {
            $_SESSION['message'] = "Employee deleted successfully!";
        } else {
            $_SESSION['message'] = "Failed to delete employee.";
        }
    } else {
        $_SESSION['message'] = "Employee not found.";
    }

    // Redirect to avoid form resubmission
    header("Location: index.php");
    exit;

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Directory</title>
    <link rel="stylesheet" href="./stylesheets/index.css"> 
   
</head>
<body>

<div class="dashboard">
    <div class="header">
        <h1>Welcome to the Employee Directory</h1>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <a href="logout.php" class="logout-button">Logout</a>
    </div>

    <div class="actions">
    <button class="action-button" onclick="toggleAddEmployeeForm()">Add New Employee</button>
    <button class="action-button" onclick="toggleView()">Switch to Card View</button>
</div>

    <!-- Add Employee Form -->
    <div id="add-employee-form" class="add-employee-form" style="display: none;">
        <h2>Add Employee</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="age">Age:</label>
            <input type="number" name="age" id="age" required>

            <label for="job_title">Job Title:</label>
            <input type="text" name="job_title" id="job_title" required>

            <label for="department">Department:</label>
            <input type="text" name="department" id="department" required>

            <label for="photo">Photo:</label>
            <input type="file" name="photo" id="photo">

            <button type="submit" name="add_employee" class="submit-button">Add Employee</button>
        </form>
    </div>

    <!-- List Employees -->
    <div id="employee-view" class="employee-list list-view">
        <h2>Employee List</h2>
        <?php if (count($employees) > 0): ?>
            <div class="employee-container">
                <?php foreach ($employees as $employee): ?>
                    <div class="employee-card">
                        <div class="employee-photo">
                            <?php if ($employee['photo_path']): ?>
                                <img src="<?php echo htmlspecialchars($employee['photo_path']); ?>" alt="Profile Photo">
                            <?php else: ?>
                                <img src="default.png" alt="Default Photo">
                            <?php endif; ?>
                        </div>
                        <div class="employee-details">
                            <p><strong>ID:</strong> <?php echo htmlspecialchars($employee['id']); ?></p>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($employee['name']); ?></p>
                            <p><strong>Age:</strong> <?php echo htmlspecialchars($employee['age']); ?></p>
                            <p><strong>Job Title:</strong> <?php echo htmlspecialchars($employee['job_title']); ?></p>
                            <p><strong>Department:</strong> <?php echo htmlspecialchars($employee['department']); ?></p>
                        </div>
                        <div class="employee-actions">
    <a href="./pages/edit.php?id=<?php echo $employee['id']; ?>" class="action-button">Edit</a>
    <form action="index.php" method="POST" style="display:inline;">
        <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
        <button type="submit" name="delete_employee" class="delete-button" onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($employee['name']); ?>?');">Delete</button>
    </form>
</div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No employees found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleView() {
        const employeeView = document.getElementById("employee-view");
        const button = document.querySelector(".actions button:last-child");

        if (employeeView.classList.contains("list-view")) {
            employeeView.classList.remove("list-view");
            employeeView.classList.add("card-view");
            button.textContent = "Switch to List View";
        } else {
            employeeView.classList.remove("card-view");
            employeeView.classList.add("list-view");
            button.textContent = "Switch to Card View";
        }
    }

    function toggleAddEmployeeForm() {
        const form = document.getElementById("add-employee-form");
        form.style.display = form.style.display === "block" ? "none" : "block";
    }
</script>

</body>
</html>
