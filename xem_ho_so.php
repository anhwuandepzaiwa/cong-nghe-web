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
$user_id = $_SESSION['user_id'];
if (isset($_POST['view_application'])) {
    $_SESSION['application_id'] = $_POST['application_id'];
    $application_id = $_SESSION['application_id'];
}else{
    $application_id = 0;
    echo "<p>No application.</p>";
}
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
       
        .form-group label {
            font-weight: 600; /* Medium boldness for labels */
        }
    </style>
</head>

<body>
    <nav class="sidebar close">
    <header>
        <div class="text logo-text">
            <span class="name"><?php echo $_SESSION['username'];?></span>
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

            <li class="nav-link" style="background-color: #db1225; border-radius: 10px;">
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

    <h2>Chi Tiết Hồ Sơ</h2>
    <?php if ($application_id != 0): ?>
        <form method="POST" action="">
            <?php
                $result = getApplicationDetails($_SESSION['application_id']);
                while ($row = mysqli_fetch_assoc($result)):
                    $subjects = [];

                    // Xác định môn học theo khối xét tuyển
                    switch ($row['admission_block']) {
                        case 'A01':
                            $subjects = [
                                'Toán' => $row['toan'],
                                'Lý' => $row['ly'],
                                'Anh' => $row['anh']
                            ];
                            break;
                        case 'A00':
                            $subjects = [
                                'Toán' => $row['toan'],
                                'Lý' => $row['ly'],
                                'Hóa' => $row['hoa']
                            ];
                            break;
                        case 'C00':
                            $subjects = [
                                'Văn' => $row['van'],
                                'Sử' => $row['su'],
                                'Địa' => $row['dia']
                            ];
                            break;
                        case 'D01':
                            $subjects = [
                                'Toán' => $row['toan'],
                                'Văn' => $row['van'],
                                'Anh' => $row['anh']
                            ];
                            break;
                        case 'B00':
                            $subjects = [
                                'Toán' => $row['toan'],
                                'Hóa' => $row['hoa'],
                                'Sinh' => $row['sinh']
                            ];
                            break;
                    }
            ?>

            <div class="form-group">
                <label>Họ tên học sinh:</label>
                <span><?php echo isset($row['student_name']) ? $row['student_name'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Ngày sinh:</label>
                <span><?php echo isset($row['birth_date']) ? $row['birth_date'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Số CMND/CCCD:</label>
                <span><?php echo isset($row['id_number']) ? $row['id_number'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Ngày cấp:</label>
                <span><?php echo isset($row['issue_date']) ? $row['issue_date'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Nơi cấp:</label>
                <span><?php echo isset($row['place_of_issue']) ? $row['place_of_issue'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Giới tính:</label>
                <span><?php echo isset($row['gender']) ? $row['gender'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Nơi sinh:</label>
                <span><?php echo isset($row['place_of_birth']) ? $row['place_of_birth'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Số điện thoại:</label>
                <span><?php echo isset($row['phone_number']) ? $row['phone_number'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Liên hệ khẩn cấp:</label>
                <span><?php echo isset($row['emergency_contact']) ? $row['emergency_contact'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <span><?php echo isset($row['email']) ? $row['email'] : 'N/A'; ?></span>
            </div>

            <!-- Dồn các thông tin địa chỉ thường trú vào một trường duy nhất -->
            <div class="form-group">
                <label>Địa chỉ thường trú:</label>
                <span>
                    <?php
                        // Kết hợp các trường thành một địa chỉ đầy đủ
                        $address = isset($row['permanent_address']) ? $row['permanent_address'] : '';
                        $address .= isset($row['permanent_ward']) ? ' - ' . $row['permanent_ward'] : '';
                        $address .= isset($row['permanent_district']) ? ' - ' . $row['permanent_district'] : '';
                        $address .= isset($row['permanent_province']) ? ' - ' . $row['permanent_province'] : '';
                        
                        echo $address ?: 'N/A';
                    ?>
                </span>
            </div>

            <!-- Dồn các thông tin địa chỉ tạm trú vào một trường duy nhất -->
            <div class="form-group">
                <label>Địa chỉ tạm trú:</label>
                <span>
                    <?php
                        // Kết hợp các trường thành một địa chỉ đầy đủ
                        $temp_address = isset($row['temporary_address']) ?  $row['temporary_address'] : '';
                        $temp_address .= isset($row['temporary_ward']) ? ' - ' . $row['temporary_ward'] : '';
                        $temp_address .= isset($row['temporary_district']) ? ' - ' . $row['temporary_district'] : '';
                        $temp_address .= isset($row['temporary_province']) ? ' - ' . $row['temporary_province'] : '';
                        
                        echo $temp_address ?: 'N/A';
                    ?>
                </span>
            </div>

            <div class="form-group">
                <label>Tên ngành nộp hồ sơ:</label>
                <span><?php echo isset($row['program_name']) ? $row['program_name'] : 'N/A'; ?></span>
            </div>

            <div class="form-group">
                <label>Tên khối xét hồ sơ:</label>
                <span><?php echo isset($row['admission_block']) ? $row['admission_block'] : 'N/A'; ?></span>
            </div>

            <h3>Điểm các môn</h3>
            <?php 
            // Hiển thị điểm của từng môn theo khối
            foreach ($subjects as $subject => $score): 
            ?>
                <div class="form-group">
                    <label><?php echo $subject; ?>:</label>
                    <span><?php echo isset($score) ? $score : 'N/A'; ?></span>
                </div>
            <?php endforeach; ?>
        <?php endwhile; ?>
        </form>
    <?php else: ?>
        <p>Chưa chọn hồ sơ để xem chi tiết hồ sơ</p>
    <?php endif; ?>

</section>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script  src="./script.js"></script>

</body>]
</html>