<?php
// Hàm kết nối CSDL
function connect_db(){
    $servername = "localhost";
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
function register_user($conn, $full_name, $email, $id_number, $username, $password, $account_type){
    $sql = "INSERT INTO users (full_name, email, id_number, username, password, account_type) VALUES ('$full_name','$email', '$id_number', '$username', '$password', '$account_type')";
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

    // Kiểm tra nếu thời gian bắt đầu lớn hơn thời gian kết thúc
    if ($start_date > $end_date) {
        echo "<p style='color: red;'>Thời gian bắt đầu không được lớn hơn thời gian kết thúc!</p>";
    } else {
        // Bước 1: Thêm chương trình vào bảng `programs` (không có admission_block)
        $sql = "INSERT INTO programs (program_name, start_date, end_date, is_visible) 
                VALUES ('$program_name', '$start_date', '$end_date', '$is_visible')";
        
        if (mysqli_query($conn, $sql)) {
            // Bước 2: Lấy ID của chương trình mới thêm vào
            $program_id = mysqli_insert_id($conn);
            
            // Bước 3: Thêm admission_block vào bảng `program_blocks`
            $sqlBlock = "INSERT INTO program_blocks (program_id, admission_block) 
                         VALUES ('$program_id', '$admission_block')";
            
            if (mysqli_query($conn, $sqlBlock)) {
                return "Ngành và khối xét tuyển đã được tạo thành công!";
            } else {
                return "Có lỗi xảy ra khi thêm khối xét tuyển. Vui lòng thử lại.";
            }
        } else {
            return "Có lỗi xảy ra khi tạo chương trình. Vui lòng thử lại.";
        }
    }
}


// Hàm cập nhật thông tin ngành
function updateProgram($program_id, $program_name, $admission_block, $start_date, $end_date) {
    global $conn;

    // Kiểm tra thời gian bắt đầu không lớn hơn thời gian kết thúc
    if ($start_date > $end_date) {
        echo "<p style='color: red;'>Thời gian bắt đầu không được lớn hơn thời gian kết thúc!</p>";
    } else {
        // Cập nhật thông tin chương trình trong bảng `programs`
        $sql = "UPDATE programs SET 
                program_name='$program_name', 
                start_date='$start_date', 
                end_date='$end_date' 
                WHERE id='$program_id'";

        if (mysqli_query($conn, $sql)) {
            // Cập nhật admission_block trong bảng `program_blocks`
            $sqlBlock = "UPDATE program_blocks SET 
                         admission_block='$admission_block' 
                         WHERE program_id='$program_id'";

            return mysqli_query($conn, $sqlBlock) ? "Thông tin ngành đã được cập nhật thành công!" : "Có lỗi xảy ra khi cập nhật khối xét tuyển.";
        } else {
            return "Có lỗi xảy ra khi cập nhật thông tin ngành. Vui lòng thử lại.";
        }
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
        // Nếu là học sinh, chỉ lấy các chương trình đang hiển thị
        $sql = "SELECT p.id, p.program_name, pb.admission_block, p.start_date, p.end_date 
                FROM programs p 
                JOIN program_blocks pb ON p.id = pb.program_id 
                WHERE p.is_visible = 1";
    } elseif ($account_type == 'gv') {
        // Nếu là giáo viên, lấy danh sách các ngành mà giáo viên được phép dạy
        $sql = "SELECT p.id, p.program_name, pb.admission_block, p.start_date, p.end_date, p.is_visible 
                FROM programs p 
                JOIN program_blocks pb ON p.id = pb.program_id
                JOIN program_teachers pt ON p.id = pt.program_id 
                WHERE pt.teacher_id = $user_id";
    } else {
        // Lấy tất cả chương trình
        $sql = "SELECT p.id, p.program_name, pb.admission_block, p.start_date, p.end_date, p.is_visible 
                FROM programs p 
                JOIN program_blocks pb ON p.id = pb.program_id";
    }

    return mysqli_query($conn, $sql);
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

    // Cập nhật thông tin chương trình trong bảng `programs`
    $sql = "UPDATE programs SET 
            program_name='$program_name', 
            start_date='$start_date', 
            end_date='$end_date' 
            WHERE id='$program_id'";

    if (mysqli_query($conn, $sql)) {
        // Cập nhật admission_block trong bảng `program_blocks`
        $sqlBlock = "UPDATE program_blocks SET 
                     admission_block='$admission_block' 
                     WHERE program_id='$program_id'";

        return mysqli_query($conn, $sqlBlock) ? "Thông tin ngành đã được cập nhật thành công!" : "Có lỗi xảy ra khi cập nhật khối xét tuyển.";
    } else {
        return "Có lỗi xảy ra khi cập nhật thông tin ngành. Vui lòng thử lại.";
    }
}

// Hàm lấy danh sách các khối theo program_id
function getAdmissionBlocks($program_id) {
    global $conn;
    $sql = "SELECT admission_block FROM program_blocks WHERE program_id = $program_id";
    $result = $conn->query($sql);

    $admission_blocks = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Tách các khối thành mảng nếu có nhiều khối trong một chuỗi
            $blocks = explode(", ", $row['admission_block']);
            $admission_blocks = array_merge($admission_blocks, $blocks);
        }
        $admission_blocks = array_unique($admission_blocks); // Loại bỏ các giá trị trùng lặp
    }
    return $admission_blocks;
}

// Hàm upload ảnh học bạ
function uploadTranscriptImage($file) {
    // Kiểm tra nếu file không tồn tại hoặc có lỗi khi upload
    if (!isset($file) || $file['error'] !== 0) {
        return "Có lỗi xảy ra khi upload file.";
    }

    // Kiểm tra loại file (chỉ chấp nhận jpg và png)
    $allowedTypes = ['image/jpeg', 'image/png'];
    if (!in_array($file['type'], $allowedTypes)) {
        return "Chỉ chấp nhận file ảnh có đuôi .jpg và .png.";
    }

    // Kiểm tra dung lượng file (< 100MB)
    $maxFileSize = 100 * 1024 * 1024; // 100MB
    if ($file['size'] > $maxFileSize) {
        return "Dung lượng file vượt quá 100MB.";
    }

    // Tạo tên file mới để tránh trùng lặp
    $uniqueFileName = uniqid("transcript_", true) . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
    $uploadDir = "uploads/"; // Thư mục lưu file
    $filePath = $uploadDir . $uniqueFileName;

    // Kiểm tra và tạo thư mục nếu chưa tồn tại
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Di chuyển file vào thư mục đích
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return "Upload ảnh học bạ thành công.";
    } else {
        return "Có lỗi xảy ra khi lưu file.";
    }
}

function getTranscriptImagePath($student_id, $program_id, $admission_block) {
    global $conn;
    $sql = "SELECT transcript_image FROM applications WHERE student_id = '$student_id' AND program_id = '$program_id' AND admission_block = '$admission_block'";
    $result = mysqli_query($conn, $sql);
    
    if ($row = $result->fetch_assoc()) {
        return $row['transcript_image'];
    }
    return null;
}

// Function to delete an uploaded file
function deleteUploadedFile($filePath) {
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            return "File đã được xóa thành công.";
        } else {
            return "Có lỗi xảy ra khi xóa file.";
        }
    } else {
        return "File không tồn tại.";
    }
}

// Hàm kiểm tra hồ sơ trùng lặp
function isApplicationExists($student_id, $program_id, $admission_block) {
    global $conn;
    $sql = "SELECT COUNT(*) FROM applications WHERE student_id = '$student_id' AND program_id = '$program_id' AND admission_block = '$admission_block'";
    
    // Thực thi câu lệnh SQL
    $result = mysqli_query($conn, $sql);
    
    // Lấy kết quả số lượng bản ghi
    $row = mysqli_fetch_row($result);
    return $row[0]; // Trả về số lượng bản ghi
}


// Hàm lưu hồ sơ vào bảng `applications`
function saveApplication($student_id, $program_id, $admission_block, $scores, $transcript_image_path) {
    global $conn;

    // If no duplicate, proceed with the insertion
    $sql = "INSERT INTO applications 
        (student_id, program_id, admission_block, toan, ly, hoa, anh, van, su, dia, sinh, transcript_image_path, status) 
        VALUES (
            '$student_id', 
            '$program_id', 
            '$admission_block', 
            '{$scores['toan']}', 
            '{$scores['ly']}', 
            '{$scores['hoa']}', 
            '{$scores['anh']}', 
            '{$scores['van']}', 
            '{$scores['su']}', 
            '{$scores['dia']}', 
            '{$scores['sinh']}', 
            '$transcript_image_path', 
            'Chưa duyệt'
        )";

    return mysqli_query($conn, $sql);
}

function getStudentApplications($account_type, $user_id, $program_id) {
    global $conn;

    // Tạo cơ sở truy vấn chung
    $sql = "SELECT 
                a.id AS application_id,
                si.full_name AS student_name,
                p.program_name AS program_name,
                a.admission_block,
                a.status,
                GROUP_CONCAT(u.full_name SEPARATOR ', ') AS reviewer_names
            FROM 
                applications a
            JOIN 
                student_info si ON a.student_id = si.user_id
            JOIN 
                programs p ON a.program_id = p.id
            LEFT JOIN 
                program_teachers pt ON pt.program_id = p.id
            LEFT JOIN 
                users u ON u.id = pt.teacher_id";

    // Thêm điều kiện cho từng loại tài khoản
    if ($account_type == 'hs') {
        // Học sinh chỉ thấy hồ sơ của chính mình và thuộc ngành đã nộp
        $sql .= " WHERE si.user_id = " . intval($user_id) . " AND a.program_id = " . intval($program_id);
    } elseif ($account_type == 'gv') {
        // Giáo viên chỉ thấy hồ sơ của ngành mà mình được phân công
        $sql .= " WHERE pt.teacher_id = " . intval($user_id);
        if ($program_id) {
            // Nếu có program_id, lọc thêm ngành cụ thể
            $sql .= " AND a.program_id = " . intval($program_id);
        }
    } elseif ($account_type == 'admin') {
        // Admin có thể xem toàn bộ hồ sơ, hoặc lọc theo ngành nếu có program_id
        if ($program_id) {
            $sql .= " WHERE a.program_id = " . intval($program_id);
        }
    }

    // Thêm GROUP BY và ORDER BY
    $sql .= " GROUP BY a.id ORDER BY a.created_at DESC";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    return $result;
}

function updateApplicationStatus($application_id, $status) {
    global $conn; // Make sure your database connection is accessible here
    $sql = "UPDATE applications SET status = '$status' WHERE id = $application_id";
    return mysqli_query($conn, $sql);
}

function deleteApplication($application_id) {
    global $conn;
    $sql = "DELETE FROM applications WHERE id = $application_id";
    return mysqli_query($conn, $sql);
}

function getApplicationDetails($application_id) {
    global $conn;

    $sql = "SELECT 
                si.full_name AS student_name,
                si.birth_date,
                si.id_number,
                si.issue_date,
                si.place_of_issue,
                si.gender,
                si.place_of_birth,
                si.phone_number,
                si.emergency_contact,
                si.email,
                si.permanent_province,
                si.permanent_district,
                si.permanent_ward,
                si.permanent_address,
                si.temporary_province,
                si.temporary_district,
                si.temporary_ward,
                si.temporary_address,
                p.program_name,
                a.admission_block,
                a.status,
                a.toan, a.ly, a.hoa, a.anh, a.van, a.su, a.dia, a.sinh
            FROM 
                applications a
            JOIN 
                student_info si ON a.student_id = si.user_id
            JOIN 
                programs p ON a.program_id = p.id
            WHERE 
                a.id = $application_id";
                
    return mysqli_query($conn, $sql);
}

function getProgramApplicationStatistics() {
    global $conn;
    
    $query = "
        SELECT programs.program_name, 
               COUNT(CASE WHEN applications.status = 'Đã duyệt' THEN 1 END) AS approved_count,
               COUNT(CASE WHEN applications.status = 'Chưa duyệt' THEN 1 END) AS pending_count,
               COUNT(CASE WHEN applications.status = 'Không duyệt' THEN 1 END) AS rejected_count
        FROM programs
        LEFT JOIN applications ON programs.id = applications.program_id
        GROUP BY programs.program_name
    ";
    
    return mysqli_query($conn, $query);
}



?>

