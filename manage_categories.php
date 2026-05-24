<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];
$error = '';
$success = '';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $category_name = trim($_POST['category_name']);
        
        if (empty($category_name)) {
            $error = "Category name is required!";
        } else {
            $query = "INSERT INTO category (category_name) VALUES (?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $category_name);
            
            if ($stmt->execute()) {
                $success = "Category added successfully!";
            } else {
                $error = "Category already exists or error occurred!";
            }
        }
    } elseif ($_POST['action'] == 'delete') {
        $category_id = intval($_POST['category_id']);
        $query = "DELETE FROM category WHERE category_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $category_id);
        
        if ($stmt->execute()) {
            $success = "Category deleted successfully!";
        } else {
            $error = "Cannot delete category with associated books!";
        }
    }
}

// Get all categories
$categories = $conn->query("SELECT * FROM category ORDER BY category_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Readify Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .sidebar {
            background-color: #34495e;
            min-height: 100vh;
            padding-top: 20px;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: background-color 0.3s ease;
            border-left: 4px solid transparent;
        }
        .sidebar a:hover {
            background-color: #2c3e50;
            border-left-color: #e74c3c;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .page-header {
            margin-bottom: 30px;
        }
        .page-header h1 {
            color: #2c3e50;
        }
        .form-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .btn-action {
            padding: 6px 12px;
            font-size: 12px;
            margin: 0 2px;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
        }
        .success-msg {
            background-color: #d5f4e6;
            color: #27ae60;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #27ae60;
        }
        .error-msg {
            background-color: #fadbd8;
            color: #e74c3c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #e74c3c;
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div style="padding: 20px; border-bottom: 1px solid #2c3e50; margin-bottom: 20px;">
            <h4 style="color: white; margin: 0;">📚 Readify Admin</h4>
            <p style="color: #bdc3c7; margin: 5px 0 0 0; font-size: 12px;">Welcome, <?php echo htmlspecialchars($admin_username); ?></p>
        </div>
        <a href="admin_dashboard.php">📊 Dashboard</a>
        <a href="manage_books.php">📖 Manage Books</a>
        <a href="manage_categories.php" style="border-left-color: #e74c3c; background-color: #2c3e50;">📁 Manage Categories</a>
        <a href="manage_users.php">👥 Manage Users</a>
        <a href="admin_logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1>Manage Categories</h1>
            <p>Add and manage book categories</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (!empty($success)) { ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php } ?>
        <?php if (!empty($error)) { ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php } ?>

        <!-- Add Category Form -->
        <div class="form-container">
            <h5>Add New Category</h5>
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <input type="hidden" name="action" value="add">
                        <button type="submit" class="btn btn-success w-100">Add Category</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="table-container">
            <h5>All Categories</h5>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($categories->num_rows > 0) {
                        while ($category = $categories->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $category['category_id']; ?></td>
                                <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                                <td>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                        <button type="submit" class="btn btn-action btn-delete" onclick="return confirm('Are you sure?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="4" class="text-center text-muted">No categories found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>