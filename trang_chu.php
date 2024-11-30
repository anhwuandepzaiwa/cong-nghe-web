<?php
session_start();

// Kiểm tra người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Kiểm tra quyền của người dùng
$account_type = $_SESSION['account_type'];
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
            <li class="nav-link" style="background-color: #db1225; border-radius: 10px">
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
    <?php if ($account_type == 'admin'): ?>
        <button id="createProgramBtn">Tạo Ngành Xét Tuyển</button>
            <div id="createProgramForm" style="display: none;">
                <form action="" method="POST">
                    <h2>Tạo Ngành Xét Tuyển</h2>
                    Tên ngành:<input type="text" id="program_name" name="program_name" required placeholder="Nhập tên ngành"><br>
                    Khối xét tuyển:<input type="text" id="admission_block" name="admission_block" required placeholder="VD: A00, A01, C00"><br>
                    Thời gian bắt đầu:<input type="date" id="start_date" name="start_date" required><br>
                    Thời gian kết thúc:<input type="date" id="end_date" name="end_date" required><br>
                    Trạng thái hiển thị:<select id="is_visible" name="is_visible" required>
                        <option value="1">Hiển Thị</option>
                        <option value="0">Ẩn</option>
                    </select><br>
                    <input type="submit" name="create" value="Tạo ngành">
                    <button type="button" id="cancelBtn">Hủy</button>
                </form>
            </div>
    <?php endif; ?>

<?php
include 'functions.php';

if (isset($_POST['create'])) 
{
    // Thêm ngành mới
    $message = createProgram($_POST['program_name'], $_POST['admission_block'], $_POST['start_date'], $_POST['end_date'], $_POST['is_visible']);
    echo "<p>$message</p>";
} 
elseif (isset($_POST['toggle_visibility'])) 
{
    // Thay đổi trạng thái hiển thị
    $message = toggleProgramVisibility($_POST['program_id'], $_POST['is_visible']);
    echo "<p>$message</p>";
} 
elseif (isset($_POST['update'])) 
{
    // Cập nhật ngành
    $message = updateProgram($_POST['program_id'], $_POST['program_name'], $_POST['admission_block'], $_POST['start_date'], $_POST['end_date']);
    echo "<p>$message</p>";
} 
elseif (isset($_POST['delete'])) 
{
    // Xoá ngành
    $message = deleteProgram($_POST['program_id']);
    echo "<p>$message</p>";
}
elseif(isset($_POST['assign_teacher']))
{
    // Phân quyền giáo viên
    $teacher_id = $_POST['teacher_id'];
    $program_id = $_POST['program_id'];
    $check_sql = "SELECT * FROM program_teachers WHERE teacher_id = $teacher_id AND program_id = $program_id";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // Record already exists
        echo "Giáo viên đã được phân quyền cho ngành này!";
    } else {
        // If not assigned, proceed to assign
        $sql = "INSERT INTO program_teachers (teacher_id, program_id) VALUES ($teacher_id, $program_id)";
        if (mysqli_query($conn, $sql)) {
            echo "Giáo viên đã được phân quyền cho ngành!";
        } else {
            echo "Lỗi phân quyền giáo viên!";
        }
    }
}

?>

<h2>Các Ngành Đã Tạo</h2>
<table border="1">
    <tr>
        <th>Tên Ngành</th>
        <th>Khối Xét Tuyển</th>
        <th>Thời Gian Bắt Đầu</th>
        <th>Thời Gian Kết Thúc</th>
        <?php if ($account_type == 'admin'): ?>
            <th>Trạng Thái</th>
            <th>Hành Động</th>
            <th>Giáo Viên Phụ Trách</th>
        <?php elseif ($account_type == 'hs' || $account_type == 'gv'): ?>
            <th>Hành Động</th>
        <?php endif; ?>
    </tr>
    <?php
        $result = getAllPrograms($account_type, $user_id);

        while ($row = mysqli_fetch_assoc($result)):
    ?>
        <tr>
            <td><?php echo $row['program_name']; ?></td>
            <td><?php echo $row['admission_block']; ?></td>
            <td><?php echo $row['start_date']; ?></td>
            <td><?php echo $row['end_date']; ?></td>
            <?php if ($account_type == 'admin'): ?>
                <td><?php echo $row['is_visible'] ? 'Hiển Thị' : 'Ẩn'; ?></td>
            <?php endif; ?>
            <?php if ($account_type == 'admin'): ?>
                <td>
                    <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="program_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="is_visible" value="<?php echo $row['is_visible']; ?>">
                        <input type="submit" name="toggle_visibility" value="<?php echo $row['is_visible'] ? 'Ẩn' : 'Hiển Thị'; ?>">
                    </form>
                    <button type="button" onclick="showEditForm(<?php echo $row['id']; ?>, '<?php echo $row['program_name']; ?>', '<?php echo $row['admission_block']; ?>', '<?php echo $row['start_date']; ?>', '<?php echo $row['end_date']; ?>')">Sửa</button>
                    <form action="" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa ngành này?');">
                        <input type="hidden" name="program_id" value="<?php echo $row['id']; ?>">
                        <input type="submit" name="delete" value="Xóa">
                    </form>
                </td>
                <td>
                    <form action="" method="POST">
                        Giáo Viên:
                        <select name="teacher_id">
                            <?php echo getTeacherOptions(); ?>
                        </select>
                        <input type="hidden" name="program_id" value="<?php echo $row['id']; ?>">
                        <input type="submit" name="assign_teacher" value="Phân Quyền">
                    </form>
                </td>
                <td>
                    <form action="nop_ho_so.php" method="POST" style="display:inline;">
                        <input type="hidden" name="program_id" value="<?php echo $row['id']; ?>">
                        <input type="submit" name="apply" value="Nộp hồ sơ">
                    </form>
                </td>
            <?php elseif ($account_type == 'hs'): ?>
                <?php
                    $currentDate = date("Y-m-d");
                    $isApplicationOpen = ($currentDate >= $row['start_date'] && $currentDate <= $row['end_date']);
                ?>
                <td>
                    <?php if ($isApplicationOpen): ?>
                        <form action="nop_ho_so.php" method="POST" style="display:inline;">
                            <input type="hidden" name="program_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" name="apply" value="Nộp Hồ Sơ">
                        </form>
                    <?php else: ?>
                        <span>Đã hết hạn nộp hồ sơ</span>
                    <?php endif; ?>
                </td>
            <?php elseif ($account_type == 'gv'): ?>
                <td>
                    <form action="nop_ho_so.php" method="POST" style="display:inline;">
                        <input type="hidden" name="program_id" value="<?php echo $row['id']; ?>">
                        <input type="submit" name="apply" value="Nộp hồ sơ">
                    </form>
                </td>
            <?php endif; ?>
        </tr>
    <?php endwhile; ?>
</table>

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
    function showEditForm(id, name, block, start, end) {
        document.getElementById('edit_program_id').value = id;
        document.getElementById('edit_program_name').value = name;
        document.getElementById('edit_admission_block').value = block;
        document.getElementById('edit_start_date').value = start;
        document.getElementById('edit_end_date').value = end;
        document.getElementById('editProgramForm').style.display = 'block';
    }
</script>

</section>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script  src="./script.js"></script>
</body>


</html>