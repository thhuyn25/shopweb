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
        <p class="dashboard-subtitle">Xem, quản lý và trả lời đánh giá từ khách hàng</p>
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
                                <th>Trạng thái trả lời</th>
                                <th>Trả lời</th>
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
                                        <td><?php echo htmlspecialchars($review['reply_status'] == 'unreplied' ? 'Chưa trả lời' : 'Đã trả lời'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="openReplyModal(<?php echo htmlspecialchars(json_encode($review)); ?>)">
                                                <i class="fas fa-reply me-1"></i>Trả lời
                                            </button>
                                        </td>
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
                                    <td colspan="9" class="text-center">Không có đánh giá nào</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal trả lời -->
        <div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="replyModalLabel">Trả lời đánh giá</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="replyForm" method="POST">
                            <input type="hidden" id="replyId" name="id">
                            <input type="hidden" id="replyEmail" name="email">
                            <div class="mb-3">
                                <label for="replyContent" class="form-label">Nội dung trả lời</label>
                                <textarea class="form-control" id="replyContent" name="replyContent" rows="4" required></textarea>
                                <div class="invalid-feedback">Vui lòng nhập nội dung trả lời.</div>
                            </div>
                            <button type="submit" class="btn btn-gradient">Gửi email</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
require('../includes/footer.php');
?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
function openReplyModal(review) {
    document.getElementById('replyId').value = review.id;
    document.getElementById('replyEmail').value = review.email;
    document.getElementById('replyContent').value = '';
    new bootstrap.Modal(document.getElementById('replyModal')).show();
}

document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('replyId').value;
    const email = document.getElementById('replyEmail').value;
    const content = document.getElementById('replyContent').value;

    fetch('/shopweb/quantri/feedback/send_reply.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(id)}&email=${encodeURIComponent(email)}&content=${encodeURIComponent(content)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Email đã được gửi thành công!');
            new bootstrap.Modal(document.getElementById('replyModal')).hide();
            location.reload();
        } else {
            alert('Gửi email thất bại: ' + data.message);
        }
    })
    .catch(error => alert('Lỗi: ' + error));
});
</script>