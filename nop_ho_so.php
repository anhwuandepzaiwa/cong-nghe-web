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
if (isset($_POST['apply'])) {
    $_SESSION['program_id'] = $_POST['program_id'];
    $program_id = $_SESSION['program_id'];
}else{
    $program_id = 0;
    echo "<p>No program selected for application.</p>";
}

$admission_blocks = getAdmissionBlocks($program_id);
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

            <li class="nav-link" style="background-color: #db1225; border-radius: 10px">
            <a href="#">
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
    <div class="text">Chào mừng <?php echo $_SESSION['username']; echo $program_id;?></div>
    <?php if ($account_type == 'hs'): ?>
    <button id="createProgramBtn">Nộp Hồ Sơ Xét Tuyển</button>
    <div id="createProgramForm" style="display: none;">
        <form action="" method="POST" enctype="multipart/form-data">
            <select name="admission_block" required>
                <option value="">Chọn khối xét tuyển</option>
                <?php foreach ($admission_blocks as $block): ?>
                    <option value="<?php echo $block; ?>"><?php echo $block; ?></option>
                <?php endforeach; ?>
            </select><br>
            <div id="scoreFields"></div>
            Upload ảnh học bạ:
            <input type="file" id="transcript_image" name="transcript_image" accept=".jpg, .jpeg, .png" required><br>
            <input type="submit" name="submit" value="Nộp Hồ Sơ">
            <button type="button" id="cancelBtn">Hủy</button>
        </form>
    </div>
<?php endif; ?>


<?php
//include 'functions.php';
$message = "";
if (isset($_POST['submit'])) {
    $program_id = $_SESSION['program_id'];
    $admission_block = $_POST['admission_block'];
    $scores = [
        'toan'  => isset($_POST['math_score']) ? $_POST['math_score'] : null,
        'ly'    => isset($_POST['physics_score']) ? $_POST['physics_score'] : null,
        'hoa'   => isset($_POST['chemistry_score']) ? $_POST['chemistry_score'] : null,
        'anh'   => isset($_POST['english_score']) ? $_POST['english_score'] : null,
        'van'   => isset($_POST['literature_score']) ? $_POST['literature_score'] : null,
        'su'    => isset($_POST['history_score']) ? $_POST['history_score'] : null,
        'dia'   => isset($_POST['geography_score']) ? $_POST['geography_score'] : null,
        'sinh'  => isset($_POST['biology_score']) ? $_POST['biology_score'] : null
    ];
    
    // Kiểm tra hồ sơ trùng lặp
    if (isApplicationExists($user_id, $program_id, $admission_block)) {
        echo "Hồ sơ đã được nộp vào khối xét tuyển này.";
    } else {
        // Upload ảnh học bạ
        $message = uploadTranscriptImage($_FILES['transcript_image']);
        if (strpos($message, "thành công") !== false) {
            $transcript_image_path = "uploads/" . $_FILES['transcript_image']['name']; // Đường dẫn ảnh học bạ
            // Lưu hồ sơ vào bảng `applications`
            saveApplication($user_id, $program_id, $admission_block, $scores, $transcript_image_path);
            echo "Nộp hồ sơ thành công!";
        } else {
            echo $message;  // Hiển thị lỗi nếu không thể upload ảnh
        }
    } 
}
elseif (isset($_POST['approve'])) {
    $program_id = $_SESSION['program_id'];
    updateApplicationStatus($_POST['application_id'], 'Đã Duyệt');
    $message = "Hồ sơ ID {$_POST['application_id']} đã được duyệt thành công.";
} 
elseif (isset($_POST['reject'])) {
    $program_id = $_SESSION['program_id'];
    updateApplicationStatus($_POST['application_id'], 'Không Duyệt');
    $message = "Hồ sơ ID {$_POST['application_id']} đã bị từ chối.";
} 
elseif (isset($_POST['delete'])) {
    $program_id = $_SESSION['program_id'];
    deleteApplication($_POST['application_id']);
    $message = "Hồ sơ ID {$_POST['application_id']} đã bị xóa.";
}
if ($message) {
    echo "<p style='color: green; font-weight: bold;'>$message</p>";
}

?>

<h2>Hồ Sơ Học Sinh Đã Nộp</h2>

<?php if ($program_id != 0): ?>
    <table border="1">
        <tr>
            <th>STT</th>
            <th>Họ tên học sinh</th>
            <th>Tên ngành nộp hồ sơ</th>
            <th>Tên khối xét hồ sơ</th>
            <?php if ($account_type == 'admin' || $account_type == 'hs'): ?>
                <th>Tên người duyệt hồ sơ</th>
            <?php endif; ?>
            <th>Trạng thái hồ sơ</th>
            <th>Hành Động</th>
        </tr>
        
        <?php
            $result = getStudentApplications($account_type, $user_id, $program_id);
            $stt = 1;
            while ($row = mysqli_fetch_assoc($result)):
        ?>
            <tr>
                <td><?php echo $stt++; ?></td>
                <td><?php echo $row['student_name']; ?></td>
                <td><?php echo $row['program_name']; ?></td>
                <td><?php echo $row['admission_block']; ?></td>
                <?php if ($account_type == 'admin' || $account_type == 'hs'): ?>
                    <td>
                        <select>
                            <?php 
                            $reviewers = explode(', ', $row['reviewer_names']); // Tách tên giáo viên ra thành mảng
                            foreach ($reviewers as $reviewer): 
                            ?>
                                <option><?php echo $reviewer; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                <?php endif; ?>

                <td><?php echo $row['status']; ?></td>
                <?php if ($account_type == 'admin' || $account_type == 'gv'): ?>
                    <td>
                        <!-- Nút hành động cho cả admin và gv -->
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                            <input type="submit" name="approve" value="Duyệt">
                            <input type="submit" name="reject" value="Không Duyệt">
                            <?php if ($account_type == 'admin'): ?>
                                <input type="submit" name="delete" value="Xóa hồ sơ">
                            <?php endif; ?>
                        </form>
                
                    </td>
                <?php endif; ?>
                <?php if ($account_type == 'admin' || $account_type == 'gv' || $account_type == 'hs'): ?>
                    <td>
                        <form action="xem_ho_so.php" method="POST">
                            <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                            <input type="submit" name="view_application" value="Xem Hồ Sơ">
                        </form> 
                    </td>
                <?php endif; ?>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>Chưa chọn hồ sơ để nộp</p>
<?php endif; ?>

<?php if ($account_type == 'admin'): ?>
    <div id="editProgramForm" style="display:none;">
        <form action="" method="POST">
            <h2>Sửa Ngành Xét Tuyển</h2>
            <input type="hidden" id="edit_program_id" name="program_id">
            Tên ngành:<input type="text" id="edit_program_name" name="program_name" required><br>
            Khối xét tuyển:<input type="text" id="edit_admission_block" name="admission_block" required><br>
            Thời gian bắt đầu:<input type="date" id="edit_start_date" name="start_date" required><br>
            Thời gian kết thúc:<input type="date" id="edit_end_date" name="end_date" required><br>
            <input type="submit" name="update" value="Cập Nhật">
            <button type="button" onclick="document.getElementById('editProgramForm').style.display='none'">Hủy</button>
        </form>
    </div>
<?php endif; ?>    

<script>
    document.getElementById("createProgramBtn").onclick = function() {
        document.getElementById("createProgramForm").style.display = "block";
    }
    document.getElementById("cancelBtn").onclick = function() {
        document.getElementById("createProgramForm").style.display = "none";
    }
    document.querySelector('[name="admission_block"]').addEventListener('change', function() {
    var selectedBlock = this.value;
    var scoreFields = document.getElementById('scoreFields');
    scoreFields.innerHTML = ''; // Xóa các trường cũ trước khi thêm trường mới

    if (selectedBlock === 'A00') {
    scoreFields.innerHTML += 'Điểm môn Toán: <input type="number" name="math_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Lý: <input type="number" name="physics_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Hóa: <input type="number" name="chemistry_score" required><br>';
} else if (selectedBlock === 'A01') {
    scoreFields.innerHTML += 'Điểm môn Toán: <input type="number" name="math_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Lý: <input type="number" name="physics_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Anh: <input type="number" name="english_score" required><br>';
} else if (selectedBlock === 'C00') {
    scoreFields.innerHTML += 'Điểm môn Văn: <input type="number" name="literature_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Sử: <input type="number" name="history_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Địa: <input type="number" name="geography_score" required><br>';
} else if (selectedBlock === 'D01') {
    scoreFields.innerHTML += 'Điểm môn Toán: <input type="number" name="math_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Văn: <input type="number" name="literature_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Anh: <input type="number" name="english_score" required><br>';
} else if (selectedBlock === 'B00') {
    scoreFields.innerHTML += 'Điểm môn Toán: <input type="number" name="math_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Hóa: <input type="number" name="chemistry_score" required><br>';
    scoreFields.innerHTML += 'Điểm môn Sinh: <input type="number" name="biology_score" required><br>';
}

});

</script>

</section>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script  src="./script.js"></script>
</body>


</html>