<?php
session_start();
include '../database/dbhelper.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

$conn = createConnection();
if (!$conn) {
    die("<div class='alert alert-danger text-center'>Lỗi kết nối cơ sở dữ liệu!</div>");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $sql = "SELECT id FROM users WHERE email = ?";
    $user = executePrepared($conn, $sql, [$email])[0] ?? null;

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", time() + 1800); 

        executePrepared($conn, "DELETE FROM password_resets WHERE email = ?", [$email]);
        $result = executePrepared($conn, "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)", [$email, $token, $expires_at]);

        if ($result) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'bunnyhyn@gmail.com'; 
                $mail->Password = 'uuqf vblw uhrw wnhb'; 
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('ngoxuanquynh.dk@gmail.com', 'Streetwear Shop');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Đặt lại mật khẩu - Streetwear Shop';
                $mail->Body = '
                    <html>
                        <head><meta charset="UTF-8"></head>
                        <body style="font-family: Arial, sans-serif; color: #333;">
                            <p>Xin chào,</p>
                            <p>Bạn vừa yêu cầu khôi phục mật khẩu từ <strong>Streetwear Shop</strong>.</p>
                            <p>Nhấn vào nút bên dưới để đặt lại mật khẩu:</p>
                            <p><a href="http://localhost/shopweb/xacthuc/reset_password.php?token=' . $token . '" 
                                  style="background: #007bff; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                                  Đặt lại mật khẩu</a></p>
                            <p>Liên kết sẽ hết hạn sau 30 phút.</p>
                        </body>
                    </html>';

                $mail->send();
                $message = "<div class='alert alert-success text-center'>Email khôi phục đã được gửi! Vui lòng kiểm tra hộp thư.</div>";
            } catch (Exception $e) {
                $message = "<div class='alert alert-danger text-center'>Lỗi gửi email: {$mail->ErrorInfo}</div>";
            }
        } else {
            $message = "<div class='alert alert-danger text-center'>Lỗi lưu token, vui lòng thử lại!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger text-center'>Email không tồn tại trong hệ thống.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khôi phục mật khẩu</title>
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="/shopweb/css/user.css">
</head>
<body class="user-page">
<?php include '../fontend/header.php'; ?>

<section class="recover-container">
    <h2 class="text-center">KHÔI PHỤC MẬT KHẨU</h2>
    <?= $message ?>
    <form method="post" class="recover-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required placeholder="Nhập email đã đăng ký">
        </div>
        <button type="submit" class="btn-recover">Gửi liên kết đặt lại</button>
        <div class="link-group">
            <a href="/shopweb/xacthuc/login.php">← Trở về đăng nhập</a>
        </div>
    </form>
</section>

<?php include '../fontend/footer.php'; ?>
</body>
</html>