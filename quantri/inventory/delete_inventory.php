<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');
require_once('../../functions.php');

$message = '';
$message_type = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $conn = createConnection();
    $sql = "SELECT product_id FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product_id = $result->fetch_assoc()['product_id'];

    $sql = "DELETE FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if (updateProductStock($conn, $product_id)) {
            $message = "Xóa sản phẩm khỏi kho thành công!";
            $message_type = 'success';
        } else {
            $message = "Xóa thất bại khi cập nhật stock: " . $conn->error;
            $message_type = 'danger';
        }
    } else {
        $message = "Xóa thất bại: " . $conn->error;
        $message_type = 'danger';
    }
    $conn->close();
} else {
    $message = "ID không hợp lệ!";
    $message_type = 'danger';
}
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-trash me-2"></i>Xóa sản phẩm khỏi kho</h2>
        <p class="dashboard-subtitle">Kết quả xóa sản phẩm</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <a href="/shopweb/quantri/inventory/management.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
        </a>
    </div>
</div>
<?php require('../includes/footer.php'); ?>