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

// Handle Add Book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $isbn = trim($_POST['isbn']);
        $category_id = intval($_POST['category_id']);
        $description = trim($_POST['description']);
        $pages = intval($_POST['pages']);
        $cover_image = $_POST['cover_image'] ?? 'book.jpg';
        
        if (empty($title) || empty($author) || empty($isbn) || empty($category_id) || empty($description) || empty($pages)) {
            $error = "All fields are required!";
        } else {
            $query = "INSERT INTO books (title, author, isbn, category_id, description, pages, cover_image) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssisjs", $title, $author, $isbn, $category_id, $description, $pages, $cover_image);
            
            if ($stmt->execute()) {
                $success = "Book added successfully!";
            } else {
                $error = "ISBN already exists or error occurred!";
            }
        }
    } elseif ($_POST['action'] == 'delete') {
        $book_id = intval($_POST['book_id']);
        $query = "DELETE FROM books WHERE book_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $book_id);
        
        if ($stmt->execute()) {
            $success = "Book deleted successfully!";
        } else {
            $error = "Error occurred while deleting book!";
        }
    }
}

// Get all books
$books_result = $conn->query("SELECT b.*, c.category_name FROM books b 
                              JOIN category c ON b.category_id = c.category_id 
                              ORDER BY b.created_at DESC");

// Get all categories for dropdown
$categories = $conn->query("SELECT * FROM category ORDER BY category_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Readify Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-book"></i> Readify Admin</h4>
            <p>Welcome, <?php echo htmlspecialchars($admin_username); ?></p>
        </div>
        <a href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="manage_books.php" class="active"><i class="fas fa-book-open"></i> Manage Books</a>
        <a href="manage_categories.php"><i class="fas fa-folder"></i> Manage Categories</a>
        <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-book-open"></i> Manage Books</h1>
            <p>Add, view and manage books in your bookstore</p>
        </div>

        <?php if (!empty($success)) { ?>
            <div class="success-msg"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php } ?>
        <?php if (!empty($error)) { ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php } ?>

        <!-- Add Book Form -->
        <div class="form-container">
            <h5><i class="fas fa-plus-circle"></i> Add New Book</h5>
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Book Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control" id="author" name="author" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" class="form-control" id="isbn" name="isbn" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php
                            $categories->data_seek(0);
                            while ($cat = $categories->fetch_assoc()) {
                                echo '<option value="' . $cat['category_id'] . '">' . htmlspecialchars($cat['category_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="pages" class="form-label">Pages</label>
                        <input type="number" class="form-control" id="pages" name="pages" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cover_image" class="form-label">Cover Image Name</label>
                        <input type="text" class="form-control" id="cover_image" name="cover_image" placeholder="e.g., book_cover.jpg">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                </div>

                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn-add"><i class="fas fa-plus"></i> Add Book</button>
            </form>
        </div>

        <!-- Books Table -->
        <div class="table-container">
            <h5><i class="fas fa-list"></i> All Books</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Category</th>
                            <th>Pages</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($books_result->num_rows > 0) {
                            while ($book = $books_result->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo $book['book_id']; ?></td>
                                    <td><?php echo htmlspecialchars(substr($book['title'], 0, 30)); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                                    <td><?php echo htmlspecialchars($book['category_name']); ?></td>
                                    <td><?php echo $book['pages']; ?></td>
                                    <td>
                                        <form method="POST" action="" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center text-muted">No books found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>