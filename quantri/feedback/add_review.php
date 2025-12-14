<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $customer_name = $_POST['customer_name'] ?? '';
    $rating = $_POST['rating'] ?? 0;
    $comment = $_POST['comment'] ?? '';

    if (!empty($product_id) && !empty($customer_name) && $rating >= 1 && $rating <= 5) {
        $conn = createConnection();
        $sql = "INSERT INTO product_reviews (product_id, customer_name, rating, comment) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $product_id, $customer_name, $rating, $comment);
        if ($stmt->execute()) {
            $message = "Thêm đánh giá thành công!";
            $message_type = 'success';
            echo "<script>alert('{$message}'); window.location.href = '/shopweb/quantri/feedback/reviews.php';</script>";
            exit;
        } else {
            $message = "Thêm thất bại: " . $conn->error;
            $message_type = 'danger';
        }
        $conn->close();
    } else {
        $message = "Thông tin không hợp lệ!";
        $message_type = 'warning';
    }
}

$conn = createConnection();
$sql = "SELECT id, name FROM products ORDER BY id";
$products = executeResult($conn, $sql);
$conn->close();
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-star me-2"></i>Thêm đánh giá mới</h2>
        <p class="dashboard-subtitle">Thêm đánh giá mới cho sản phẩm</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Thông tin đánh giá</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="">Chọn sản phẩm</option>
                            <?php if ($products): ?>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo htmlspecialchars($product['id']); ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn sản phẩm.</div>
                    </div>
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        <div class="invalid-feedback">Vui lòng nhập tên khách hàng.</div>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Đánh giá (1-5 sao) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                        <div class="invalid-feedback">Vui lòng nhập số sao từ 1 đến 5.</div>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Bình luận</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4"></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-gradient"><i class="fas fa-save me-1"></i>Thêm đánh giá</button>
                        <a href="/shopweb/quantri/feedback/reviews.php" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<?php require('../includes/footer.php'); ?>