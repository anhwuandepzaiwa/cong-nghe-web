<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực tài khoản</title>
</head>
<body>
    <h2>Xác thực tài khoản</h2>
    <form action="" method="post">
        Email: <input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required><br>
        Số căn cước: <input type="text" name="id_number" value="<?php echo isset($_POST['id_number']) ? $_POST['id_number'] : ''; ?>" required><br>
        <input type="submit" name="submit" value="Gửi yêu cầu">
    </form>
    <p><a href="login.php">Quay lại trang đăng nhập</a></p>

    <?php
        include 'functions.php';

        if (isset($_POST['submit'])) 
        {
            $email = trim($_POST['email']);
            $id_number = trim($_POST['id_number']);

            // Kiểm tra định dạng email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            {
                echo "Địa chỉ email không hợp lệ.";
                return;
            }

            // Kiểm tra định dạng số căn cước (12 chữ số)
            if (!preg_match('/^\d{12}$/', $id_number)) 
            {
                echo "Số căn cước phải gồm 12 chữ số.";
                return;
            }

            if (check_email_exists($conn, $email) && check_id_number_exists($conn, $id_number)) 
            {
                $token = bin2hex(random_bytes(16));

                save_token($conn, $email, $token);

                if (send_verification_email($email, $token, 0)) 
                {
                    echo "Email xác thực đã được gửi đến bạn. Vui lòng kiểm tra hộp thư.";
                } else 
                {
                    echo "Gửi email xác thực thất bại.";
                }
            } 
            else 
            {
                echo "Không tìm thấy tài khoản với số căn cước và email này.";
            }

            mysqli_close($conn);
        }
    ?>
</body>
</html>
