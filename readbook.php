<?php
session_start();
include 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get all categories
$categories = $conn->query("SELECT * FROM category ORDER BY category_name ASC");

// Get selected category
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query
$query = "SELECT b.*, c.category_name FROM books b 
          JOIN category c ON b.category_id = c.category_id";

if ($selected_category != '') {
    $query .= " WHERE b.category_id = " . intval($selected_category);
}

$query .= " ORDER BY b.created_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books - Readify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background-color: #2c3e50;
        }
        .navbar-custom a {
            color: white !important;
        }
        .category-filter {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .book-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        }
        .book-cover {
            height: 300px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            text-align: center;
            padding: 20px;
        }
        .book-info {
            padding: 15px;
        }
        .book-title {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .book-author {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .book-category {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .btn-read {
            background-color: #3498db;
            color: white;
            border: none;
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-read:hover {
            background-color: #2980b9;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 30px 0;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">📚 Readify</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="user_dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="readbook.php">Browse Books</a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($username); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="user_logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="header">
        <div class="container">
            <h1>Browse Our Collection</h1>
            <p>Discover amazing books across all categories</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Category Filter -->
        <div class="category-filter">
            <h5 class="mb-3">Filter by Category</h5>
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-4">
                        <select name="category" class="form-select" onchange="this.form.submit()">
                            <option value="">-- All Categories --</option>
                            <?php
                            while ($row = $categories->fetch_assoc()) {
                                $selected = ($selected_category == $row['category_id']) ? 'selected' : '';
                                echo '<option value="' . $row['category_id'] . '" ' . $selected . '>' . htmlspecialchars($row['category_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Books Grid -->
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($book = $result->fetch_assoc()) {
                    ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card book-card">
                            <div class="book-cover">
                                <div style="text-align: center;">
                                    <div style="font-size: 48px; margin-bottom: 10px;">📖</div>
                                    <strong><?php echo htmlspecialchars(substr($book['title'], 0, 20)); ?>...</strong>
                                </div>
                            </div>
                            <div class="book-info">
                                <div class="book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                                <div class="book-author">by <?php echo htmlspecialchars($book['author']); ?></div>
                                <div class="book-category"><?php echo htmlspecialchars($book['category_name']); ?></div>
                                <div class="mb-2" style="font-size: 12px; color: #95a5a6;">
                                    ISBN: <?php echo htmlspecialchars($book['isbn']); ?><br>
                                    Pages: <?php echo $book['pages']; ?>
                                </div>
                                <a href="read_book.php?book_id=<?php echo $book['book_id']; ?>" class="btn-read">Read Now</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12"><p class="text-center text-muted">No books found in this category.</p></div>';
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; 2025 Readify. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>