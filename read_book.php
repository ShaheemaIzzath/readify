<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get book details
if (isset($_GET['book_id'])) {
    $book_id = intval($_GET['book_id']);
    $query = "SELECT b.*, c.category_name FROM books b 
              JOIN category c ON b.category_id = c.category_id 
              WHERE b.book_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        header("Location: readbook.php");
        exit();
    }
} else {
    header("Location: readbook.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - Readify</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        .book-header {
            background-color: #2c3e50;
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
        }
        .book-details {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .book-cover-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .book-cover {
            width: 200px;
            height: 300px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            margin: 0 auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .book-info {
            margin-top: 30px;
        }
        .book-title {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .book-author {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        .book-meta {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .book-meta p {
            margin: 5px 0;
            font-size: 14px;
        }
        .book-category {
            display: inline-block;
            background-color: #e74c3c;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 10px;
        }
        .book-description {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .book-description h5 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .book-description p {
            color: #7f8c8d;
            line-height: 1.6;
        }
        .pdf-viewer {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .pdf-controls {
            background-color: #34495e;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .pdf-controls button {
            background-color: #3498db;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
            transition: background-color 0.3s ease;
        }
        .pdf-controls button:hover {
            background-color: #2980b9;
        }
        .pdf-container {
            text-align: center;
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-back {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }
        .btn-back:hover {
            background-color: #2980b9;
            color: white;
            text-decoration: none;
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
                        <a class="nav-link" href="readbook.php">Browse Books</a>
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
    <div class="book-header">
        <div class="container">
            <a href="readbook.php" class="btn-back">← Back to Books</a>
            <h1><?php echo htmlspecialchars($book['title']); ?></h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Book Details -->
        <div class="book-details">
            <div class="row">
                <div class="col-md-4">
                    <div class="book-cover-section">
                        <div class="book-cover">📖</div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="book-info">
                        <h2 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h2>
                        <p class="book-author">By <strong><?php echo htmlspecialchars($book['author']); ?></strong></p>
                        
                        <div>
                            <span class="book-category"><?php echo htmlspecialchars($book['category_name']); ?></span>
                        </div>

                        <div class="book-meta">
                            <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
                            <p><strong>Pages:</strong> <?php echo $book['pages']; ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($book['category_name']); ?></p>
                            <p><strong>Published:</strong> <?php echo date('M d, Y', strtotime($book['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Book Description -->
        <div class="book-description">
            <h5>📖 About This Book</h5>
            <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>
        </div>

        <!-- PDF Viewer Section -->
        <div class="pdf-viewer">
            <div class="pdf-controls">
                <div>
                    <button onclick="previousPage()">&larr; Previous</button>
                    <span id="page-info">Page 1 of <?php echo $book['pages']; ?></span>
                    <button onclick="nextPage()">Next &rarr;</button>
                </div>
                <div>
                    <button onclick="downloadPDF()">📥 Download PDF</button>
                </div>
            </div>

            <div class="pdf-container">
                <div style="text-align: center;">
                    <p style="font-size: 18px; color: #7f8c8d; margin-bottom: 20px;">
                        📖 PDF Viewer<br>
                        <small style="font-size: 14px;">Left to Right Reading Mode</small>
                    </p>
                    <div style="width: 100%; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <div id="current-page" style="font-size: 14px; color: #7f8c8d; margin-bottom: 20px;">
                            Displaying Page <span id="current-page-num">1</span>
                        </div>
                        <div style="height: 400px; background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%); display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <p style="color: white; font-size: 16px;">📄 Book Content Here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div style="text-align: center; margin-bottom: 40px;">
            <a href="readbook.php" class="btn-back">Continue Reading Other Books</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p>&copy; 2025 Readify. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentPage = 1;
        const totalPages = <?php echo $book['pages']; ?>;

        function updatePageInfo() {
            document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;
            document.getElementById('current-page-num').textContent = currentPage;
        }

        function nextPage() {
            if (currentPage < totalPages) {
                currentPage++;
                updatePageInfo();
            }
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                updatePageInfo();
            }
        }

        function downloadPDF() {
            alert('PDF Download: ' + '<?php echo htmlspecialchars($book['title']); ?>.pdf');
            // In a real scenario, you would provide actual PDF download functionality
        }

        updatePageInfo();
    </script>
</body>
</html>