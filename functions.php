<?php
// Hàm kết nối CSDL
function connect_db(){
    $servername = "localhost:3307";
    $username = "root";
    $password = "";
    $dbname = "project";

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if(!$conn) 
    {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }
    return $conn;
}

$conn = connect_db();

// Hàm kiểm tra email đã tồn tại
function check_email_exists($conn, $email) {
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}

// Hàm kiểm tra số căn cước đã tồn tại
function check_id_number_exists($conn, $id_number) {
    $sql = "SELECT * FROM users WHERE id_number = '$id_number'";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}

// Hàm kiểm tra tài khoản đã tồn tại
function check_username_exists($conn, $username) {
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}

// Hàm đăng ký tài khoản mới
function register_user($conn, $email, $id_number, $username, $password, $account_type){
    $sql = "INSERT INTO users (email, id_number, username, password, account_type) VALUES ('$email', '$id_number', '$username', '$password', '$account_type')";
    if(mysqli_query($conn, $sql)) 
    {
        return true;
    }
    else 
    {
        return false;
    }
}

// Hàm xử lý đăng ký
function handle_registration() {
    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $account_type = $_POST['account_type'];
        
        if ($password !== $confirm_password) 
        {
            echo "Mật khẩu không khớp";
            return;
        }

        $conn = connect_db();

        if (check_username_exists($conn, $username)) 
        {
            echo "Tài khoản đã tồn tại";
        } else {
            if (register_user($conn, $username, $password, $account_type)) 
            {
                echo "Đăng ký thành công";
            } 
            else 
            {
                echo "Đăng ký thất bại: " . mysqli_error($conn);
            }
        }

        mysqli_close($conn);
    }
}

function save_token($conn, $email, $token) {
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));
    $sql = "UPDATE users SET token = '$token', token_expiry = '$expiry' WHERE email = '$email'";
    mysqli_query($conn, $sql);
}

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function send_verification_email($email, $token, $is_reset) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'quanvh987654321@gmail.com';
        $mail->Password = 'tiqltinumosbwguu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('quanvh987654321@gmail.com', 'Văn Hồng Quân');
        $mail->addAddress($email);

        $mail->isHTML(true);

        if ($is_reset == 1) {
            $mail->Subject = 'Đặt lại mật khẩu';
            $reset_link = "http://localhost/project/reset_password.php?token=$token";
            $mail->Body = "Vui lòng nhấn vào liên kết sau để đặt lại mật khẩu của bạn: <a href=\"$reset_link\">Đặt lại mật khẩu</a>";
        } else {
            $mail->Subject = 'Xác thực tài khoản';
            $verification_link = "http://localhost/project/verify.php?token=$token";
            $mail->Body = "Vui lòng nhấn vào liên kết sau để xác thực tài khoản của bạn: <a href=\"$verification_link\">Xác thực tài khoản</a>";
        }
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Email không thể gửi. Lỗi: {$mail->ErrorInfo}";
        return false;
    }
}

function handle_login_attempts(){
    // Kiểm tra xem người dùng có bị khóa không
    if(isset($_SESSION['locked']) && time() - $_SESSION['locked'] < 300)
    {
        echo "Bạn đã bị vô hiệu hóa trong 5 phút. Vui lòng thử lại sau.";
        return false;
    }

    // Xóa khóa nếu đã hết 5 phút
    if(isset($_SESSION['locked']) && time() - $_SESSION['locked'] >= 300)
    {
        unset($_SESSION['locked']);
        $_SESSION['login_attempts'] = 0;    // Reset lại số lần đăng nhập sai
    }

    // Nếu đã đăng nhập sai 3 lần thì khóa tài khoản trong 5 phút
    if($_SESSION['login_attempts'] >= 3)
    {
        $_SESSION['locked'] = time();   // Ghi nhận thời gian bị khóa
        echo "Bạn đã nhập sai quá 3 lần. Tài khoản bị vô hiệu hóa trong 5 phút.";
        return false;
    }

    return true;
}

// Kiểm tra người dùng đã đăng nhập hay chưa
function check_user_logged_in(){
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
}

function logout(){
    session_destroy();
    header("Location: login.php");
    exit;
}

function delete_account($conn, $username){
    $delete_sql = "DELETE FROM users WHERE username = '$username'";
    return mysqli_query($conn, $delete_sql);
}

function update_account($conn, $old_username, $new_username, $new_password, $account_type){
    $update_sql = "UPDATE users SET username = '$new_username', password = '$new_password', account_type = '$account_type' WHERE username = '$old_username'";
    return mysqli_query($conn, $update_sql);
}
?>