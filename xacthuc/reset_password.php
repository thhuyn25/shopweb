<?php
session_start();
include '../database/dbhelper.php';

$conn = createConnection();
$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (!$token) {
    $error = "Liên kết không hợp lệ.";
} else {
    $sql_check = "SELECT email, expires_at FROM password_resets WHERE token = ?";
    $reset = executePrepared($conn, $sql_check, [$token])[0] ?? null;

    if (!$reset) {
        $error = "Liên kết không hợp lệ hoặc đã hết hạn.";
    } else {
        $now = date("Y-m-d H:i:s");
        if ($now > $reset['expires_at']) {
            $error = "Liên kết đã hết hạn.";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if (strlen($new_password) < 6) {
                $error = "Mật khẩu phải có ít nhất 6 ký tự.";
            } elseif ($new_password != $confirm_password) {
                $error = "Mật khẩu xác nhận không khớp.";
            } else {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $sql_update = "UPDATE users SET password = ? WHERE email = ?";
                executePrepared($conn, $sql_update, [$hashed, $reset['email']]);

                $sql_delete = "DELETE FROM password_resets WHERE token = ?";
                executePrepared($conn, $sql_delete, [$token]);

                $success = "Mật khẩu đã được đặt lại thành công. <a href='/shopweb/xacthuc/login.php'>Đăng nhập</a>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="/shopweb/css/user.css">
</head>
<body class="user-page">
<?php include '../fontend/header.php'; ?>

<section class="recover-container">
    <h2 class="text-center">ĐẶT LẠI MẬT KHẨU</h2>

    <?php if ($error) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>
    <?php if ($success) { echo "<div class='alert alert-success text-center'>$success</div>"; exit; } ?>

    <form method="post" class="recover-form">
        <div class="form-group">
            <label for="new_password">Mật khẩu mới:</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Xác nhận mật khẩu:</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn-recover">Cập nhật mật khẩu</button>
        <div class="link-group">
            <a href="/shopweb/xacthuc/login.php">← Quay lại đăng nhập</a>
        </div>
    </form>
</section>

<?php include '../fontend/footer.php'; ?>
</body>
</html>
