<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Thêm Khóa Học</title>
    <style>
        body {
            background-color: #f8f9fa;
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
    </style>
</head>

<body>

    <!-- Thanh điều hướng -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Trang Chủ</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Xin chào, Giáo viên 1 </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="">Quản trị tài khoản</a>
                        <a class="dropdown-item" href="">Đăng xuất</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Nội dung chính -->
    <div class="container content">
        <a href="" class="btn btn-secondary">Trở Về</a>
        <h3>Thêm Khóa Học Mới</h3>
        <form>
            <div class="form-group">
                <label for="courseName">Tên Khóa Học</label>
                <input type="text" name="" class="form-control" id="courseName" placeholder="Nhập tên khóa học" required>
            </div>
            <div class="form-group">
                <label for="courseContent">Nội Dung Khóa Học</label>
                <textarea class="form-control" name="" id="courseContent" rows="5" placeholder="Nhập nội dung khóa học" required></textarea>
            </div>
            <div class="form-group">
                <label for="courseVisibility">Trạng Thái Khóa Học</label>
                <select class="form-control" name="" id="courseVisibility" required>
                    <option value="">Hiện</option>
                    <option value="">Ẩn</option>
                </select>
            </div>
            <button type="submit" name="" class="btn btn-primary">Lưu Khóa Học</button>
            <!-- <a href="" class="btn btn-secondary">Hủy</a> -->
        </form>


    </div>

    <!-- Chân trang -->
    <footer>
        <p>&copy; 2024 Công nghệ web.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>