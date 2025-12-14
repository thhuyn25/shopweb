<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$message = '';
$message_type = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $conn = createConnection();
    $sql = "DELETE FROM product_reviews WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Xóa đánh giá thành công!";
        $message_type = 'success';
        echo "<script>alert('{$message}'); window.location.href = '/shopweb/quantri/feedback/reviews.php';</script>";
        exit;
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
        <h2 class="dashboard-title"><i class="fas fa-trash me-2"></i>Xóa đánh giá</h2>
        <p class="dashboard-subtitle">Kết quả xóa đánh giá sản phẩm</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <a href="/shopweb/quantri/feedback/reviews.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
        </a>
    </div>
</div>
<?php require('../includes/footer.php'); ?>