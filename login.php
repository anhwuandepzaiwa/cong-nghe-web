<?php
    session_start();
    include 'functions.php';

    // Khởi tạo số lần thử đăng nhập nếu chưa có
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
</head>
<body>
    <h2>Đăng Nhập</h2>
    <form action="" method="post">
        Tài khoản: <input type="text" name="username" required><br>
        Mật khẩu: <input type="password" name="password" required><br>
        <input type="submit" name="submit" value="Đăng nhập">
    </form>

    <p>
        <span style="margin-right: 60px;"><a href="register.php">Tạo tài khoản mới</a></span> 
        <span><a href="forgot_password.php">Quên mật khẩu?</a></span><br><br>
        <span><a href="verify_account.php">Xác thực tài khoản</a></span>
    </p>


    <?php
        if (isset($_POST['submit'])) 
        {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if(handle_login_attempts())
            {
                // Lấy account_type từ cơ sở dữ liệu cùng với xác thực tài khoản
                $sql = "SELECT id, full_name ,account_type, is_verified FROM users WHERE username = '$username' AND password = '$password'";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) === 1){
                    $user = mysqli_fetch_assoc($result);

                    if ($user['is_verified'] == 1) 
                    {
                        // Đăng nhập thành công, lưu thông tin vào session
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['username'] = $username;
                        $_SESSION['username'] = $username;
                        $_SESSION['account_type'] = $user['account_type'];
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['login_attempts'] = 0;
        
                        // Điều hướng sang trang chủ
                        header("Location: trang_chu.php");
                        exit;
                    } 
                    else 
                    {
                        echo "Tài khoản của bạn chưa được xác thực. Vui lòng xác thực tài khoản trước khi đăng nhập.";
                    }
                } 
                else 
                {
                    $_SESSION['login_attempts']++;  // Tăng số lần thử
                    echo "Sai tên đăng nhập hoặc mật khẩu. Bạn còn " . (3 - $_SESSION['login_attempts']) . " lần thử.";
                }
            }
        }
        mysqli_close($conn);
    ?>
</body>
</html>