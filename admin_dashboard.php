<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_username = $_SESSION['admin_username'];

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_books = $conn->query("SELECT COUNT(*) as count FROM books")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM category")->fetch_assoc()['count'];

// Get recent users
$recent_users = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Readify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .navbar-custom {
            background-color: #2c3e50;
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
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 20px;
        }
        .stat-card h5 {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #e74c3c;
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
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div style="padding: 20px; border-bottom: 1px solid #2c3e50; margin-bottom: 20px;">
            <h4 style="color: white; margin: 0;">📚 Readify Admin</h4>
            <p style="color: #bdc3c7; margin: 5px 0 0 0; font-size: 12px;">Welcome, <?php echo htmlspecialchars($admin_username); ?></p>
        </div>
        <a href="admin_dashboard.php" style="border-left-color: #e74c3c; background-color: #2c3e50;">📊 Dashboard</a>
        <a href="manage_books.php">📖 Manage Books</a>
        <a href="manage_categories.php">📁 Manage Categories</a>
        <a href="manage_users.php">👥 Manage Users</a>
        <a href="admin_logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1>Admin Dashboard</h1>
            <p>Welcome to Readify Admin Panel</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="stat-card">
                    <h5>Total Users</h5>
                    <div class="number"><?php echo $total_users; ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5>Total Books</h5>
                    <div class="number"><?php echo $total_books; ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5>Total Categories</h5>
                    <div class="number"><?php echo $total_categories; ?></div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="table-container">
            <h5>Recent Users</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($user = $recent_users->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $user['user_id'] . '</td>';
                        echo '<td>' . htmlspecialchars($user['username']) . '</td>';
                        echo '<td>' . htmlspecialchars($user['email']) . '</td>';
                        echo '<td>' . date('M d, Y', strtotime($user['created_at'])) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>