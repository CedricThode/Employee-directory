<?php
session_start();
require '../includes/config.php'; 

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $employee_id = $_GET['id'];

    // Fetch the current employee data
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$employee_id]);
    $employee = $stmt->fetch();

    if ($employee) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $age = $_POST['age'];
            $job_title = $_POST['job_title'];
            $department = $_POST['department'];
            $photo_path = $employee['photo_path']; 

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
                        $error_message = "Failed to upload new photo.";
                    }
                }

                // Update the employee data in the database
                if (!isset($error_message)) {
                    $update_stmt = $pdo->prepare("UPDATE employees SET name = ?, age = ?, job_title = ?, department = ?, photo_path = ? WHERE id = ?");
                    if ($update_stmt->execute([$name, $age, $job_title, $department, $photo_path, $employee_id])) {
                        header("Location: ../index.php?updated=1");
                        exit;
                    } else {
                        $error_message = "Failed to update employee.";
                    }
                }
            }
        }
    } else {
        $error_message = "Employee not found.";
    }
} else {
    $error_message = "Invalid Request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="../stylesheets/edit.css">
</head>
<body>
<div class="back-arrow">
        <a href="../index.php">&#8592; Back</a>
    </div>
<div class="container">
    <h1>Edit Employee</h1>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form action="edit.php?id=<?php echo $employee['id']; ?>" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required><br>

        <label for="age">Age:</label>
        <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($employee['age']); ?>" required><br>

        <label for="job_title">Job Title:</label>
        <input type="text" id="job_title" name="job_title" value="<?php echo htmlspecialchars($employee['job_title']); ?>" required><br>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($employee['department']); ?>" required><br>

        <label for="photo">Profile Photo:</label>
        <input type="file" id="photo" name="photo" accept="image/*"><br>

        <button type="submit">Update Employee</button>
    </form>
    </div>
</body>
</html>
