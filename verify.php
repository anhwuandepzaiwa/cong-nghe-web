<?php
include 'functions.php';

// Kiểm tra xem đã có token hay chưa
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    // Kiểm tra token có hợp lệ hay không
    $sql = "SELECT * FROM users WHERE token = '$token' AND token_expiry > NOW()";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "Liên kết xác thực không hợp lệ hoặc đã hết hạn.";
        exit;
    }
} else {
    echo "Không có token xác thực.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Thông Tin Thí Sinh</title>
    <script src="https://esgoo.net/scripts/jquery.js"></script>
    <style type="text/css">
        .css_select_div { text-align: center; }
        .css_select { display: inline-table; width: 25%; padding: 5px; margin: 5px 2%; border: solid 1px #686868; border-radius: 5px; }
    </style>
    <script>
        $(document).ready(function() {
            // Lấy tỉnh thành cho Địa Chỉ Hộ Khẩu Thường Trú
            $.getJSON('https://esgoo.net/api-tinhthanh/1/0.htm', function(data_tinh) {
                if (data_tinh.error == 0) {
                    $.each(data_tinh.data, function(key_tinh, val_tinh) {
                        $("#tinh_permanent").append('<option value="' + val_tinh.id + '" data-fullname="' + val_tinh.full_name + '">' + val_tinh.full_name + '</option>');
                    });
                    
                    // Province selection change
                    $("#tinh_permanent").change(function(e) {
                        var idtinh = $(this).val();
                        var selectedProvinceName = $("#tinh_permanent option:selected").data("fullname"); 
                        $("form").append('<input type="hidden" name="permanent_province" value="' + selectedProvinceName + '">'); // Capture province name
                        
                        // Get districts
                        $.getJSON('https://esgoo.net/api-tinhthanh/2/' + idtinh + '.htm', function(data_quan) {
                            if (data_quan.error == 0) {
                                $("#quan_permanent").html('<option value="0">Quận Huyện</option>');
                                $("#phuong_permanent").html('<option value="0">Phường Xã</option>');
                                $.each(data_quan.data, function(key_quan, val_quan) {
                                    $("#quan_permanent").append('<option value="' + val_quan.id + '" data-fullname="' + val_quan.full_name + '">' + val_quan.full_name + '</option>');
                                });

                                // District selection change
                                $("#quan_permanent").change(function(e) {
                                    var idquan = $(this).val();
                                    var selectedDistrictName = $("#quan_permanent option:selected").data("fullname"); // Capture district name
                                    $("form").append('<input type="hidden" name="permanent_district" value="' + selectedDistrictName + '">');

                                    // Get wards
                                    $.getJSON('https://esgoo.net/api-tinhthanh/3/' + idquan + '.htm', function(data_phuong) {
                                        if (data_phuong.error == 0) {
                                            $("#phuong_permanent").html('<option value="0">Phường Xã</option>');
                                            $.each(data_phuong.data, function(key_phuong, val_phuong) {
                                                $("#phuong_permanent").append('<option value="' + val_phuong.id + '" data-fullname="' + val_phuong.full_name + '">' + val_phuong.full_name + '</option>');
                                            });
                                            
                                            // Ward selection change
                                            $("#phuong_permanent").change(function(e) {
                                                var selectedWardName = $("#phuong_permanent option:selected").data("fullname"); // Capture ward name
                                                $("form").append('<input type="hidden" name="permanent_ward" value="' + selectedWardName + '">');
                                            });
                                        }
                                    });
                                });
                            }
                        });
                    });
                }
            });


        // Temporary Address (Địa Chỉ Liên Lạc Tạm Trú)
        $.getJSON('https://esgoo.net/api-tinhthanh/1/0.htm', function(data_tinh) {
            if (data_tinh.error == 0) {
                $.each(data_tinh.data, function(key_tinh, val_tinh) {
                    $("#tinh_temporary").append('<option value="' + val_tinh.id + '" data-fullname="' + val_tinh.full_name + '">' + val_tinh.full_name + '</option>');
                });
                
                // Province selection change for temporary address
                $("#tinh_temporary").change(function(e) {
                    var idtinh = $(this).val();
                    var selectedProvinceNameTemp = $("#tinh_temporary option:selected").data("fullname"); // Capture province name
                    $("form").append('<input type="hidden" name="temporary_province" value="' + selectedProvinceNameTemp + '">');
                    
                    // Get districts for temporary address
                    $.getJSON('https://esgoo.net/api-tinhthanh/2/' + idtinh + '.htm', function(data_quan) {
                        if (data_quan.error == 0) {
                            $("#quan_temporary").html('<option value="0">Quận Huyện</option>');
                            $("#phuong_temporary").html('<option value="0">Phường Xã</option>');
                            $.each(data_quan.data, function(key_quan, val_quan) {
                                $("#quan_temporary").append('<option value="' + val_quan.id + '" data-fullname="' + val_quan.full_name + '">' + val_quan.full_name + '</option>');
                            });

                            // District selection change for temporary address
                            $("#quan_temporary").change(function(e) {
                                var idquan = $(this).val();
                                var selectedDistrictNameTemp = $("#quan_temporary option:selected").data("fullname"); // Capture district name
                                $("form").append('<input type="hidden" name="temporary_district" value="' + selectedDistrictNameTemp + '">');

                                // Get wards for temporary address
                                $.getJSON('https://esgoo.net/api-tinhthanh/3/' + idquan + '.htm', function(data_phuong) {
                                    if (data_phuong.error == 0) {
                                        $("#phuong_temporary").html('<option value="0">Phường Xã</option>');
                                        $.each(data_phuong.data, function(key_phuong, val_phuong) {
                                            $("#phuong_temporary").append('<option value="' + val_phuong.id + '" data-fullname="' + val_phuong.full_name + '">' + val_phuong.full_name + '</option>');
                                        });
                                        
                                        // Ward selection change for temporary address
                                        $("#phuong_temporary").change(function(e) {
                                            var selectedWardNameTemp = $("#phuong_temporary option:selected").data("fullname"); // Capture ward name
                                            $("form").append('<input type="hidden" name="temporary_ward" value="' + selectedWardNameTemp + '">');
                                        });
                                    }
                                });
                            });
                        }
                    });
                });
            }
        });

    });
    </script>
</head>
<body>
    <h2>Đăng Ký Thông Tin Thí Sinh</h2>
    <form id="form" action="" method="post">
        <h3>Thông Tin Cá Nhân</h3>
        Tên đầy đủ: <input type="text" name="full_name" value="<?php $user['full_name']; ?>" readonly><br>
        Username: <input type="text" name="username" value="<?php echo $user['username']; ?>" readonly><br>
        Ngày sinh: <input type="date" name="birth_date" value="<?php echo isset($_POST['birth_date']) ? $_POST['birth_date'] : ''; ?>" required><br>
        Số căn cước: <input type="text" name="id_number" value="<?php echo $user['id_number']; ?>" readonly><br>
        Ngày cấp căn cước: <input type="date" name="issue_date" value="<?php echo isset($_POST['issue_date']) ? $_POST['issue_date'] : ''; ?>" required><br>
        Nơi cấp căn cước: <input type="text" name="place_of_issue" value="<?php echo isset($_POST['place_of_issue']) ? $_POST['place_of_issue'] : ''; ?>" required><br>
        Giới tính: <select name="gender" required>
            <option value="Nam" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
            <option value="Nữ" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
            <option value="Khác" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Khác') ? 'selected' : ''; ?>>Khác</option>
        </select><br>
        Nơi sinh: <input type="text" name="place_of_birth" value="<?php echo isset($_POST['place_of_birth']) ? $_POST['place_of_birth'] : ''; ?>" required><br>

        <h3>Thông Tin Liên Hệ</h3>
        Số điện thoại: <input type="text" name="phone_number" value="<?php echo isset($_POST['phone_number']) ? $_POST['phone_number'] : ''; ?>" required><br>
        Số điện thoại người thân: <input type="text" name="emergency_contact" value="<?php echo isset($_POST['emergency_contact']) ? $_POST['emergency_contact'] : ''; ?>" required><br>
        Email: <input type="email" name="email" value="<?php echo $user['email']; ?>" readonly><br>

        <h3>Địa Chỉ Hộ Khẩu Thường Trú</h3>
        <div class="css_select_div">
            <select class="css_select" id="tinh_permanent" title="Chọn Tỉnh Thành">
                <option value="0">Tỉnh Thành</option>
            </select>
            <select class="css_select" id="quan_permanent" title="Chọn Quận Huyện">
                <option value="0">Quận Huyện</option>
            </select>
            <select class="css_select" id="phuong_permanent" title="Chọn Phường Xã">
                <option value="0">Phường Xã</option>
            </select>
        </div>
        Địa chỉ chi tiết: <input type="text" name="permanent_address" value="<?php echo isset($_POST['permanent_address']) ? $_POST['permanent_address'] : ''; ?>" required><br>

        <h3>Địa Chỉ Liên Lạc Tạm Trú</h3>
        <div class="css_select_div">
            <select class="css_select" id="tinh_temporary" title="Chọn Tỉnh Thành">
                <option value="0">Tỉnh Thành</option>
            </select>
            <select class="css_select" id="quan_temporary" title="Chọn Quận Huyện">
                <option value="0">Quận Huyện</option>
            </select>
            <select class="css_select" id="phuong_temporary" title="Chọn Phường Xã">
                <option value="0">Phường Xã</option>
            </select>
        </div>
        Địa chỉ chi tiết: <input type="text" name="temporary_address" value="<?php echo isset($_POST['temporary_address']) ? $_POST['temporary_address'] : ''; ?>" required><br>

        <input type="submit" name="submit" value="Lưu">
    </form>

    <p>
        <a href="login.php">Quay lại trang đăng nhập</a>
    </p>
    
    <?php
        if (isset($_POST['submit'])) {
            $full_name = $_POST['full_name'];
            $username = $user['username']; 
            $birth_date = $_POST['birth_date'];
            $id_number = $user['id_number']; 
            $issue_date = $_POST['issue_date'];
            $place_of_issue = $_POST['place_of_issue'];
            $gender = $_POST['gender'];
            $place_of_birth = $_POST['place_of_birth'];
            $phone_number = $_POST['phone_number'];
            $emergency_contact = $_POST['emergency_contact'];
            $email = $user['email']; 
            $permanent_province = $_POST['permanent_province'];
            $permanent_district = $_POST['permanent_district'];
            $permanent_ward = $_POST['permanent_ward'];
            $permanent_address = $_POST['permanent_address'];
            $temporary_province = $_POST['temporary_province'];
            $temporary_district = $_POST['temporary_district'];
            $temporary_ward = $_POST['temporary_ward'];
            $temporary_address = $_POST['temporary_address'];

            $sql_insert = "INSERT INTO student_info (full_name, username, birth_date, id_number, issue_date, place_of_issue, gender, place_of_birth, phone_number, emergency_contact, email, permanent_province, permanent_district, permanent_ward, permanent_address, temporary_province, temporary_district, temporary_ward, temporary_address)
            VALUES ('$full_name', '$username', '$birth_date', '$id_number', '$issue_date', '$place_of_issue', '$gender', '$place_of_birth', '$phone_number', '$emergency_contact', '$email', '$permanent_province', '$permanent_district', '$permanent_ward', '$permanent_address', '$temporary_province', '$temporary_district', '$temporary_ward', '$temporary_address')";

            if (mysqli_query($conn, $sql_insert)) {
                $sql_update = "UPDATE users SET is_verified = 1, token = NULL, token_expiry = NULL WHERE token = '$token'";
                mysqli_query($conn, $sql_update);
                
                echo "<p>Cập nhật thông tin thành công!</p>";
            } else {
                echo "Có lỗi xảy ra: " . mysqli_error($conn);
            }
        }
        mysqli_close($conn);
    ?>
</body>
</html>
