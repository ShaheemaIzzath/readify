<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];

// Get all users
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Readify Admin</title>
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
        .btn-edit {
            background-color: #3498db;
            color: white;
            border: none;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
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
        <a href="manage_categories.php">📁 Manage Categories</a>
        <a href="manage_users.php" style="border-left-color: #e74c3c; background-color: #2c3e50;">👥 Manage Users</a>
        <a href="admin_logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1>Manage Users</h1>
            <p>View and manage all registered users</p>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($user = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="delete_user.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-action btn-delete" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-muted">No users found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>