<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: auto;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Đặt lại mật khẩu</h2>
    <form action="" method="post">
        <div class="form-group">
            <label for="new_password">Mật khẩu mới:</label>
            <input type="password" id="new_password" name="new_password" value="<?php echo isset($_POST['new_password']) ? $_POST['new_password'] : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Xác nhận mật khẩu mới:</label>
            <input type="password" id="confirm_password" name="confirm_password" value="<?php echo isset($_POST['confirm_password']) ? $_POST['confirm_password'] : ''; ?>" required>
        </div>
        <input type="submit" name="submit" value="Đặt lại mật khẩu">
    </form>
    <p><a href="login.php">Quay lại trang đăng nhập</a></p>

    <?php
        if (isset($_POST['submit'])) {
            include 'functions.php'; 
            
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) 
            {
                echo "Mật khẩu phải có ít nhất 8 ký tự, bao gồm 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.";
                return;
            }

            $token = $_GET['token']; 

            if ($new_password !== $confirm_password) {
                echo "Mật khẩu và xác nhận mật khẩu không khớp.";
            } else {
                $new_password = md5($new_password);
                $sql = "UPDATE users SET password = '$new_password', token = NULL WHERE token = '$token'";
                if (mysqli_query($conn, $sql)) {
                    echo "Mật khẩu đã được cập nhật thành công. Bạn có thể đăng nhập.";
                } else {
                    echo "Có lỗi xảy ra. Vui lòng thử lại.";
                }
            }
            mysqli_close($conn);
        }
    ?>
</body>
</html>
