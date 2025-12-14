<?php
require_once('../../database/dbhelper.php');

$conn = createConnection();

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$new_role = isset($_GET['role']) && in_array($_GET['role'], ['user', 'admin']) ? $_GET['role'] : null;

if ($user_id <= 0 || !$new_role) {
    $message = 'Thông tin không hợp lệ!';
    $message_type = 'warning';
    header('Location: admin_management.php?message=' . urlencode($message) . '&message_type=' . urlencode($message_type));
    exit;
}

$sql = "UPDATE users SET role = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $new_role, $user_id);
if ($stmt->execute()) {
    $message = 'Cập nhật vai trò thành công!';
    $message_type = 'success';
} else {
    $message = 'Lỗi khi cập nhật vai trò: ' . $conn->error;
    $message_type = 'warning';
}
$stmt->close();
$conn->close();

header('Location: admin_management.php?message=' . urlencode($message) . '&message_type=' . urlencode($message_type));
exit;
?>