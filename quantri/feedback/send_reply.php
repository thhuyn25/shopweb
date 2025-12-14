<?php
session_start();
require_once('../../database/dbhelper.php');

// Kiểm tra và tải PHPMailer
$vendorPath = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($vendorPath)) {
    $response = ['success' => false, 'message' => 'Lỗi: File vendor/autoload.php không tồn tại. Vui lòng chạy composer install.'];
    echo json_encode($response);
    exit;
}
require $vendorPath;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['email']) && isset($_POST['content'])) {
    $id = $_POST['id'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $content = trim($_POST['content']);

    $conn = createConnection();
    if ($conn) {
        $mail = new PHPMailer(true);
        try {
            // Cấu hình SMTP Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'bunnyhyn@gmail.com';
            $mail->Password = 'uuqf vblw uhrw wnhb';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPDebug = 2; 
            $mail->Debugoutput = function($str, $level) {
                $logFile = __DIR__ . '/email_debug.log';
                file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "SMTP Debug: {$level} - {$str}\n", FILE_APPEND);
            };
            $mail->CharSet = 'UTF-8';

            // Người gửi và người nhận
            $mail->setFrom('bunnyhyn@gmail.com', 'Quản trị viên');
            $mail->addAddress($email, 'Khách hàng');

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Phản hồi từ Quản trị viên về đánh giá';
            $mail->Body = nl2br(htmlspecialchars($content));
            $mail->AltBody = strip_tags($content);

            $mail->send();
            $response['success'] = true;
            $response['message'] = 'Email đã được gửi thành công';

            // Cập nhật trạng thái trả lời
            $sql = "UPDATE product_reviews SET reply_status = 'replied' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
        } catch (Exception $e) {
            $response['message'] = "Lỗi khi gửi email: {$mail->ErrorInfo}";
            $logFile = __DIR__ . '/email_debug.log';
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Email Error: {$mail->ErrorInfo}\n", FILE_APPEND);
        }
        $conn->close();
    } else {
        $response['message'] = 'Kết nối cơ sở dữ liệu thất bại';
        $logFile = __DIR__ . '/email_debug.log';
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Database Error: " . mysqli_connect_error() . "\n", FILE_APPEND);
    }
} else {
    $response['message'] = 'Dữ liệu gửi đi không hợp lệ';
}

echo json_encode($response);
?>