<?php
session_start();
require_once('../../database/dbhelper.php');

$conn = createConnection();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM users WHERE id = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = 'Xóa admin thành công!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Không tìm thấy admin với ID này hoặc người dùng không phải admin.';
            $_SESSION['message_type'] = 'warning';
        }
    } else {
        $_SESSION['message'] = 'Có lỗi xảy ra khi xóa admin: ' . $conn->error;
        $_SESSION['message_type'] = 'danger';
    }
    $stmt->close();
} else {
    $_SESSION['message'] = 'ID admin không hợp lệ hoặc không tìm thấy.';
    $_SESSION['message_type'] = 'warning';
}

$conn->close();
header('Location: admin_management.php');
exit();
?>