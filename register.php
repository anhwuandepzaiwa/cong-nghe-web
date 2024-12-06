<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản</title>
</head>
<body>
    <h2>Đăng Ký Tài Khoản</h2>
    <form action="" method="post">
        Họ và tên: <input type="text" name="full_name" value="<?php echo isset($_POST['full_name']) ? $_POST['full_name'] : ''; ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required><br>
        Số căn cước: <input type="text" name="id_number" value="<?php echo isset($_POST['id_number']) ? $_POST['id_number'] : ''; ?>" required><br>
        Tài khoản: <input type="text" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" required><br>
        Mật khẩu: <input type="password" name="password" required><br>
        Nhập lại mật khẩu: <input type="password" name="confirm_password" required><br>
        Loại tài khoản:
        <select id="account_type" name="account_type" required>
            <option value="gv" <?php echo (isset($_POST['account_type']) && $_POST['account_type'] == 'gv') ? 'selected' : ''; ?>>Giáo viên</option>
            <option value="hs" <?php echo (isset($_POST['account_type']) && $_POST['account_type'] == 'hs') ? 'selected' : ''; ?>>Học sinh</option>
        </select><br>
        <input type="submit" name="submit" value="Đăng ký">
    </form>

    <p>Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a></p>

    <?php
        include 'functions.php';
        if (isset($_POST['submit'])) 
        {
            $full_name = trim($_POST['full_name']);
            $email = trim($_POST['email']);
            $id_number = trim($_POST['id_number']);
            $username = trim($_POST['username']);
            $password = md5($_POST['password']);
            $confirm_password = md5($_POST['confirm_password']);
            $account_type = $_POST['account_type'];
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            {
                echo "Địa chỉ email không hợp lệ";
                return;
            }

            if (!preg_match('/^\d{12}$/', $id_number)) 
            {
                echo "Số căn cước phải gồm 12 chữ số.";
                return;
            }

            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_-]{2,19}$/', $username)) 
            {
                echo "Tên tài khoản phải từ 3 đến 20 ký tự, bắt đầu bằng chữ cái và chỉ chứa chữ, số, gạch dưới hoặc gạch ngang.";
                return;
            }
            
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) 
            {
                echo "Mật khẩu phải có ít nhất 8 ký tự, bao gồm 1 chữ hoa, 1 chữ thường, 1 số và 1 ký tự đặc biệt.";
                return;
            }

            if ($password !== $confirm_password) 
            {
                echo "Mật khẩu không khớp";
                return;
            }

            if (check_username_exists($conn, $username)) 
            {
                echo "Tài khoản đã tồn tại";
            } 
            elseif (check_email_exists($conn, $email)) 
            {
                echo "Email đã tồn tại";
            } 
            elseif (check_id_number_exists($conn, $id_number)) 
            {
                echo "Số căn cước đã tồn tại";
            }
            else 
            {
                if (register_user($conn, $full_name, $email, $id_number, $username, $password, $account_type)) 
                {
                    echo "Đăng ký thành công";
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

                    header("Location: login.php");
                    exit();
                } 
                else 
                {
                    echo "Đăng ký thất bại: " . mysqli_error($conn);
                }
            }
            mysqli_close($conn);
        }
    ?>
</body>
</html>