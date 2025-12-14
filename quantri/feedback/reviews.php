<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT r.*, p.name AS product_name FROM product_reviews r LEFT JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC";
$reviews = executeResult($conn, $sql);

if (!$reviews) {
    $reviews = [];
    $message = "Không thể tải danh sách đánh giá. Vui lòng thử lại!";
    $message_type = "danger";
}
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-star me-2"></i>Quản lý đánh giá</h2>
        <p class="dashboard-subtitle">Xem và quản lý đánh giá sản phẩm</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách đánh giá</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="/shopweb/quantri/feedback/add_review.php" class="btn btn-gradient">
                        <i class="fas fa-plus me-1"></i>Thêm đánh giá mới
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên sản phẩm</th>
                                <th>Khách hàng</th>
                                <th>Đánh giá (sao)</th>
                                <th>Bình luận</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reviews)): ?>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($review['id']); ?></td>
                                        <td><?php echo htmlspecialchars($review['product_name'] ?? 'Chưa có thông tin'); ?></td>
                                        <td><?php echo htmlspecialchars($review['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($review['rating']); ?> <i class="fas fa-star text-warning"></i></td>
                                        <td><?php echo htmlspecialchars($review['comment'] ?? 'Chưa có bình luận'); ?></td>
                                        <td><?php echo htmlspecialchars($review['created_at'] ?? 'Chưa xác định'); ?></td>
                                        <td>
                                            <a href="/shopweb/quantri/feedback/edit_review.php?id=<?php echo htmlspecialchars($review['id']); ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit me-1"></i>Sửa
                                            </a>
                                            <a href="/shopweb/quantri/feedback/delete_review.php?id=<?php echo htmlspecialchars($review['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?');">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Không có đánh giá nào</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$conn->close();
require('../includes/footer.php');
?>