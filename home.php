<?php 
session_start();
include 'functions.php';

// Check if user is logged in
check_user_logged_in();

$username = $_SESSION['username'];
$account_type = $_SESSION['account_type'];

// Handle logout
if (isset($_POST['logout'])) 
{
    logout();
}

// Get message from session (if any)
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);  // Clear the message after displaying
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css'>
  <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&amp;display=swap'><link rel="stylesheet" href="./style.css">
    <title>Trang Chủ</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .hero {
            background-color: #007bff;
            color: white;
            padding: 60px 0;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Trang Chủ</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Xin chào, <?php echo $username; ?> 
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <form action="" method="post">
                            <input type="submit" name="logout" value="Đăng xuất" class="dropdown-item">
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    
    <!-- Hero Section -->
    <div class="hero">
        <h1>Chào Mừng Đến Với Trang Chủ</h1>
        <p>Khám phá danh sách các khóa học của chúng tôi</p>
    </div>

    <!-- Main Content -->
    <div class="container content">
        <!-- Add Course button for Teachers and Admin only -->
        <?php if ($account_type === 'admin' || $account_type === 'gv'): ?>
            <a href="" class="btn btn-success mb-3">Thêm Khóa Học</a>
        <?php endif; ?>

        <h3>Danh Sách Khóa Học</h3>
        
        <div class="row">
            <!-- Course Card 1 -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Khóa học Công nghệ WEB</h5>
                        <p class="card-text">test</p>
                        
                        <!-- Show/Hide and Delete buttons for Teacher and Admin only -->
                        <?php if ($account_type === 'admin' || $account_type === 'gv'): ?>
                            <button type="submit" name="hide" value="course1" class="btn btn-warning">Ẩn</button>
                            <button type="submit" name="delete" value="course1" class="btn btn-danger">Xóa</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Course Card 2 -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Khóa học Nền tảng phát triển web</h5>
                        <p class="card-text">test 2</p>
                        
                        <!-- Show/Hide and Delete buttons for Teacher and Admin only -->
                        <?php if ($account_type === 'admin' || $account_type === 'gv'): ?>
                            <button type="submit" name="show" value="course2" class="btn btn-success">Hiện</button>
                            <button type="submit" name="delete" value="course2" class="btn btn-danger">Xóa</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
