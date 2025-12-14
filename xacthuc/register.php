<?php
session_start();
include '../database/dbhelper.php';

$conn = createConnection();
if (!$conn) {
    die("<div class='alert alert-danger text-center'>Lỗi kết nối cơ sở dữ liệu!</div>");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var($_POST['username'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $birthday = $_POST['birthday'] ?? '';

    if (empty($username) || strlen($username) < 3) {
        $error = "<div class='alert alert-danger text-center'>Tên đăng nhập phải có ít nhất 3 ký tự!</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "<div class='alert alert-danger text-center'>Email không hợp lệ!</div>";
    } elseif (strlen($password) < 6) {
        $error = "<div class='alert alert-danger text-center'>Mật khẩu phải có ít nhất 6 ký tự!</div>";
    } elseif ($password !== $confirm_password) {
        $error = "<div class='alert alert-danger text-center'>Mật khẩu nhập lại không khớp!</div>";
    } elseif (!in_array($gender, ['Nam', 'Nữ', 'Khác'])) {
        $error = "<div class='alert alert-danger text-center'>Vui lòng chọn giới tính hợp lệ!</div>";
    } elseif (empty($birthday) || strtotime($birthday) > time()) {
        $error = "<div class='alert alert-danger text-center'>Ngày sinh không hợp lệ!</div>";
    } else {
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $existingUser = executePrepared($conn, $sql, [$username, $email]);

        if (!empty($existingUser)) {
            $error = "<div class='alert alert-danger text-center'>Tài khoản hoặc email đã tồn tại!</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password, gender, birthday, role) VALUES (?, ?, ?, ?, ?, 'user')";
            $result = executePrepared($conn, $sql, [$username, $email, $hashed_password, $gender, $birthday]);

            if ($result) {
                $success = "<div class='alert alert-success text-center'>Đăng ký thành công! <a href='/shopweb/xacthuc/login.php'>Đăng nhập ngay</a></div>";
            } else {
                $error = "<div class='alert alert-danger text-center'>Lỗi hệ thống, vui lòng thử lại sau!</div>";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng ký - Streetwear Shop</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="/shopweb/css/user.css">
</head>
<body class="user-page">
    <?php 
    $logged_in = isset($_SESSION['user_id']);
    $user_name = $logged_in ? $_SESSION['user_name'] : '';
    include '../fontend/header.php'; 
    ?>
    <section class="register-container">
        <h2 class="text-center mb-4">Đăng Ký Tài Khoản</h2>
        <?php 
            if ($error) echo $error; 
            if ($success) echo $success;
        ?>
        <form method="post" class="register-form">
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input type="text" id="username" name="username" required placeholder="Nhập tên đăng nhập" value="<?php echo htmlspecialchars($username ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">Địa chỉ Email</label>
                <input type="email" id="email" name="email" required placeholder="Nhập email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required placeholder="Nhập mật khẩu">
            </div>

            <div class="form-group">
                <label for="confirm_password">Xác nhận mật khẩu</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Nhập lại mật khẩu">
            </div>

            <div class="form-group">
                <label for="birthday">Ngày sinh</label>
                <input type="date" id="birthday" name="birthday" required value="<?php echo htmlspecialchars($birthday ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Giới tính</label>
                <div class="gender-group">
                    <label><input type="radio" name="gender" value="Nam" <?php echo ($gender ?? '') == 'Nam' ? 'checked' : ''; ?> required> Nam</label>
                    <label><input type="radio" name="gender" value="Nữ" <?php echo ($gender ?? '') == 'Nữ' ? 'checked' : ''; ?> required> Nữ</label>
                    <label><input type="radio" name="gender" value="Khác" <?php echo ($gender ?? '') == 'Khác' ? 'checked' : ''; ?> required> Khác</label>
                </div>
            </div>

            <button type="submit" class="btn-register">Đăng ký</button>

            <div class="link-group">
                Đã có tài khoản? <a href="/shopweb/xacthuc/login.php">Đăng nhập</a>
            </div>
        </form>
    </section>
    <?php include '../fontend/footer.php'; ?>
</body>
</html>