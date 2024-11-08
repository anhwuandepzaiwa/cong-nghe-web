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
// function handle_registration() {
//     if (isset($_POST['submit'])) {
//         $username = $_POST['username'];
//         $password = $_POST['password'];
//         $confirm_password = $_POST['confirm_password'];
//         $account_type = $_POST['account_type'];
        
//         if ($password !== $confirm_password) 
//         {
//             echo "Mật khẩu không khớp";
//             return;
//         }

//         $conn = connect_db();

//         if (check_username_exists($conn, $username)) 
//         {
//             echo "Tài khoản đã tồn tại";
//         } else {
//             if (register_user($conn, $username, $password, $account_type)) 
//             {
//                 echo "Đăng ký thành công";
//             } 
//             else 
//             {
//                 echo "Đăng ký thất bại: " . mysqli_error($conn);
//             }
//         }

//         mysqli_close($conn);
//     }
// }

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

// Admin
// Hàm thêm ngành mới
function createProgram($program_name, $admission_block, $start_date, $end_date, $is_visible) {
    global $conn;
    if ($start_date > $end_date) {
        echo "<p style='color: red;'>Thời gian bắt đầu không được lớn hơn thời gian kết thúc!</p>";
    } else {
        $sql = "INSERT INTO programs (program_name, admission_block, start_date, end_date, is_visible) 
            VALUES ('$program_name', '$admission_block', '$start_date', '$end_date', '$is_visible')";
        return mysqli_query($conn, $sql) ? "Ngành đã được tạo thành công!" : "Có lỗi xảy ra. Vui lòng thử lại.";
    }
    
}

// Hàm cập nhật thông tin ngành
function updateProgram($program_id, $program_name, $admission_block, $start_date, $end_date) {
    global $conn;
    if ($start_date > $end_date) 
    {
        echo "<p style='color: red;'>Thời gian bắt đầu không được lớn hơn thời gian kết thúc!</p>";
    }
    else 
    {
        $sql = "UPDATE programs SET 
            program_name='$program_name', 
            admission_block='$admission_block', 
            start_date='$start_date', 
            end_date='$end_date' 
            WHERE id='$program_id'";
        return mysqli_query($conn, $sql) ? "Thông tin ngành đã được cập nhật thành công!" : "Có lỗi xảy ra. Vui lòng thử lại.";
    }
}

// Hàm xoá ngành
function deleteProgram($program_id) {
    global $conn;
    $sql = "DELETE FROM programs WHERE id='$program_id'";
    return mysqli_query($conn, $sql) ? "Ngành đã được xóa thành công!" : "Có lỗi xảy ra. Vui lòng thử lại.";
}

// Hàm thay đổi trạng thái hiển thị ngành
function toggleProgramVisibility($program_id, $is_visible) {
    global $conn;
    $new_visibility = $is_visible ? 0 : 1;
    $sql = "UPDATE programs SET is_visible='$new_visibility' WHERE id='$program_id'";
    return mysqli_query($conn, $sql) ? "Trạng thái đã được cập nhật thành công!" : "Có lỗi xảy ra. Vui lòng thử lại.";
}

// Hàm lấy danh sách các ngành
function getAllPrograms($account_type, $user_id) {
    global $conn;
    if ($account_type == 'hs') {
        // Nếu là học sinh, chỉ lấy các trường cần thiết và chỉ lấy những chương trình đang hiển thị
        $sql = "SELECT id, program_name, admission_block, start_date, end_date FROM programs WHERE is_visible = 1";
    } 
    elseif ($account_type == 'gv') 
    {
        $sql = "SELECT p.id, p.program_name, p.admission_block, p.start_date, p.end_date, p.is_visible 
            FROM programs p 
            JOIN program_teachers pt ON p.id = pt.program_id 
            WHERE pt.teacher_id = $user_id";

        return mysqli_query($conn, $sql);
    }
    else 
    {
        // Nếu không phải học sinh, lấy tất cả các trường
        $sql = "SELECT * FROM programs";
    }
    return mysqli_query($conn, $sql);;
}

function getTeacherOptions() {
    global $conn;
    $options = "";
    $result = mysqli_query($conn, "SELECT id, username FROM users WHERE account_type = 'gv'");
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= "<option value='{$row['id']}'>{$row['username']}</option>";
    }
    return $options;
}

function updateProgramForTeacher($program_id, $program_name, $admission_block, $start_date, $end_date) {
    global $conn;
    $sql = "UPDATE programs SET 
            program_name='$program_name', 
            admission_block='$admission_block', 
            start_date='$start_date', 
            end_date='$end_date' 
            WHERE id='$program_id'";
    return mysqli_query($conn, $sql) ? "Thông tin ngành đã được cập nhật thành công!" : "Có lỗi xảy ra. Vui lòng thử lại.";
}

?>