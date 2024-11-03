<?php
session_start();
include 'functions.php';

$conn = connect_db();

// Kiểm tra người dùng đã đăng nhập hay chưa
check_user_logged_in();

// Kiểm tra nếu có tên tài khoản để chỉnh sửa
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
}

// Xử lý cập nhật tài khoản
if (isset($_POST['update'])) {
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];
    $account_type = $_POST['account_type'];

    if (update_account($conn, $username, $new_username, $new_password, $account_type)) {
        echo "Tài khoản đã được cập nhật.";
        header("Location: home.php"); // Quay lại trang chủ
        exit;
    } else {
        echo "Lỗi khi cập nhật tài khoản: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Tài Khoản</title>
</head>
<body>
    <h2>Chỉnh Sửa Tài Khoản</h2>
    <form action="edit.php?username=<?php echo $username; ?>" method="post">
        Tài khoản: <input type="text" name="username" value="<?php echo $user['username']; ?>" required><br>
        Mật khẩu: <input type="password" name="password" value="<?php echo $user['password']; ?>" required><br>
        Loại tài khoản:
        <select name="account_type">
            <option value="admin" <?php if ($user['account_type'] === 'admin') echo 'selected'; ?>>Admin</option>
            <option value="gv" <?php if ($user['account_type'] === 'gv') echo 'selected'; ?>>Giáo viên</option>
        </select><br>
        <input type="submit" name="update" value="Cập nhật">
    </form>
</body>
</html>
