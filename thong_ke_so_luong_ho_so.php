<?php
session_start();
include 'functions.php';
// Kiểm tra người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Kiểm tra quyền của người dùng
$account_type = $_SESSION['account_type'];
if($account_type != 'admin')
{
    header("Location: trang_chu.php");
    exit();
}
$user_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        #createProgramForm {
            display: none;
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <nav class="sidebar close">
    <header>
        <div class="text logo-text">
            <span class="name"><?php echo $_SESSION['username']; ?></span>
        </div>
        </div>

        <i class='bx bx-chevron-right toggle'></i>
    </header>

    <div class="menu-bar">
        <div class="menu">

        <ul class="menu-links">
            <li class="nav-link">
            <a href="trang_chu.php">
                <i class='bx bx-home-alt icon'></i>
                <span class="text nav-text">Trang chủ</span>
            </a>
            </li>

            <li class="nav-link">
            <a href="nop_ho_so.php">
                <i class='bx bx-bar-chart-alt-2 icon'></i>
                <span class="text nav-text">Nộp hồ sơ chi tiết</span>
            </a>
            </li>

            <li class="nav-link">
            <a href="xem_ho_so.php">
                <i class='bx bx-bell icon'></i>
                <span class="text nav-text">Xem hồ sơ chi tiết</span>
            </a>
            </li>

            <?php if ($account_type == 'admin'): ?>
            <li class="nav-link">
            <a href="thong_ke_so_luong_ho_so.php">
                <i class='bx bx-pie-chart-alt icon'></i>
                <span class="text nav-text">Thống kê hồ sơ</span>
            </a>
            </li>
            <?php endif; ?>
        </ul>
        </div>

        <div class="bottom-content">
        <li class="">
            <a href="logout.php">
            <i class='bx bx-log-out icon'></i>
            <span class="text nav-text">Logout</span>
            </a>
        </li>

        <li class="mode">
            <div class="sun-moon">
            <i class='bx bx-moon icon moon'></i>
            <i class='bx bx-sun icon sun'></i>
            </div>
            <span class="mode-text text">Dark mode</span>
            <div class="toggle-switch">
            <span class="switch"></span>
            </div>
        </li>
        </div>
    </div>
</nav>

<section class="home" style="margin-left: 10px;">
    <div class="text">Chào mừng <?php echo $_SESSION['username']; ?></div>
    <h2>Thống Kê Hồ Sơ Theo Ngành</h2>
    <table border="1">
        <tr>
            <th>Tên Ngành</th>
            <th>Số Hồ Sơ Đã Duyệt</th>
            <th>Số Hồ Sơ Chưa Duyệt</th>
            <th>Số Hồ Sơ Không Duyệt</th>
        </tr>
        <?php
            $result = getProgramApplicationStatistics();
            while ($row = mysqli_fetch_assoc($result)) :
        ?>
        <tr>
            <td><?php echo $row['program_name']; ?></td>
            <td><?php echo $row['approved_count']; ?></td>
            <td><?php echo $row['pending_count']; ?></td>
            <td><?php echo $row['rejected_count']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table> 
</section>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script  src="./script.js"></script>

</body>
</html>