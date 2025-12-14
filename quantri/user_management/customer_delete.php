<?php
session_start();
require_once('../../database/dbhelper.php');

$conn = createConnection();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Xóa khách hàng thành công!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Có lỗi xảy ra khi xóa khách hàng: ' . $conn->error;
        $_SESSION['message_type'] = 'danger';
    }
} else {
    $_SESSION['message'] = 'ID khách hàng không hợp lệ hoặc không tìm thấy.';
    $_SESSION['message_type'] = 'warning';
}

$conn->close();
header('Location: customer_management.php');
exit();
?>