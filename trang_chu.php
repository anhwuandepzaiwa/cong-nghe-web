<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Trang Chủ</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .hero {
            background-color: #007bff;
            color: white;
            padding: 60px 0;
            text-align: center;
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

        .card {
            margin-bottom: 20px;
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
                        <a class="dropdown-item" href="">Đăng xuất</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Phần Hero -->
    <div class="hero">
        <h1>Chào Mừng Đến Với Trang Chủ</h1>
        <p>Khám phá danh sách các khóa học của chúng tôi</p>
    </div>
    <form action="" method="get">
        <!-- Nội dung chính -->
        <div class="container content">
            <a href="" class="btn btn-success mb-3">Thêm Khóa Học</a>

            <h3>Danh Sách Khóa Học</h3>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Khóa học Công nghệ WEB</h5>
                            <p class="card-text">test</p>

                            <button type="submit" name="" value="" class="btn btn-warning">Ẩn</button>
                            <button type="submit" name="" value="" class="btn btn-danger">Xóa</button>
                            <!-- <button type="submit" name="hien" class="btn btn-success">Hiện</button> -->

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Khóa học Nền tảng phát triển web</h5>
                            <p class="card-text">test 2</p>

                            <!-- <button type="submit" name="" value="" class="btn btn-warning">Ẩn</button> -->
                            <button type="submit" name="" value="" class="btn btn-danger">Xóa</button>
                            <button type="submit" name="" class="btn btn-success">Hiện</button>

                        </div>
                    </div>
                </div>
            </div>
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