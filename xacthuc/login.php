<?php
if (ini_get("session.auto_start") == 0) {
    ini_set('session.gc_maxlifetime', 86400);
    session_set_cookie_params(86400);
}
session_start();
include '../database/dbhelper.php';

error_log("Login.php executed at: " . date('Y-m-d H:i:s'));
$conn = createConnection();
$error = '';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])) {
    $login_input = filter_var($_COOKIE['remember_user'], FILTER_SANITIZE_STRING);
    error_log("Attempting cookie login for input: $login_input");
    $sql = "SELECT id, username, role FROM users WHERE username = ? OR email = ?";
    $user = executePrepared($conn, $sql, [$login_input, $login_input])[0] ?? null;
    error_log("Cookie login - User data: " . json_encode($user));
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? 'user';
        error_log("Cookie login - Session data: " . json_encode($_SESSION));
        $redirect = ($user['role'] == 'admin') ? '/shopweb/quantri/index.php' : '/shopweb/index.php';
        header("Location: http://localhost$redirect");
        exit;
    } else {
        error_log("Cookie login failed: No user found for input $login_input");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) ? true : false;

    $sql = "SELECT id, username, password, role FROM users WHERE username = ? OR email = ?";
    $user = executePrepared($conn, $sql, [$login_input, $login_input])[0] ?? null;
    error_log("Form login - User data: " . json_encode($user));
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['role'] = $user['role'] ?? 'user';
        error_log("Form login - Session data: " . json_encode($_SESSION));
        if ($remember_me) {
            setcookie('remember_user', $user['username'], time() + (30 * 24 * 60 * 60), "/");
        }
        $redirect = ($user['role'] == 'admin') ? '/shopweb/quantri/index.php' : '/shopweb/index.php';
        header("Location: http://localhost$redirect");
        exit;
    } else {
        $error = "<div class='alert alert-danger text-center'>Tên đăng nhập, email hoặc mật khẩu không đúng!</div>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng nhập - Streetwear Shop</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="/shopweb/css/user.css">
</head>
<body class="user-page">
    <?php include '../fontend/header.php'; ?>
    <section class="login-container">
        <h2 class="text-center mb-4">Đăng Nhập</h2>
        <?php if ($error) echo $error; ?>
        <form method="post" class="login-form">
            <div class="form-group">
                <label for="username">Email hoặc Tên đăng nhập</label>
                <input type="text" id="username" name="username" required placeholder="Nhập email hoặc tên đăng nhập">
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required placeholder="Nhập mật khẩu">
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="remember_me" name="remember_me">
                <label for="remember_me">Ghi nhớ đăng nhập</label>
            </div>
            <button type="submit" class="btn-login">Đăng nhập</button>
            <div class="link-group">
                <a href="/shopweb/xacthuc/recover.php">Quên mật khẩu?</a>
                <a href="/shopweb/xacthuc/register.php">Tạo tài khoản mới!</a>
            </div>
        </form>
    </section>
    <?php include '../fontend/footer.php'; ?>
</body>
</html>