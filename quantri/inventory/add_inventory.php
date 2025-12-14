<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');
require_once('../../functions.php');

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $location = $_POST['location'] ?? '';

    if (!empty($product_id) && $quantity > 0) {
        $conn = createConnection();
        $sql = "INSERT INTO inventory (product_id, quantity, location) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $product_id, $quantity, $location);
        if ($stmt->execute()) {
            if (updateProductStock($conn, $product_id)) {
                $message = "Thêm sản phẩm vào kho thành công!";
                $message_type = 'success';
            } else {
                $message = "Thêm thất bại khi cập nhật stock: " . $conn->error;
                $message_type = 'danger';
            }
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

$sql = "SELECT id, name FROM products ORDER BY id";
$conn = createConnection();
$products = executeResult($conn, $sql);
$conn->close();
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-plus me-2"></i>Thêm sản phẩm vào kho</h2>
        <p class="dashboard-subtitle">Thêm sản phẩm mới vào kho hàng</p>
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
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Thông tin sản phẩm</h5>
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
                        <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                        <div class="invalid-feedback">Vui lòng nhập số lượng lớn hơn 0.</div>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Vị trí</label>
                        <input type="text" class="form-control" id="location" name="location">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-gradient"><i class="fas fa-save me-1"></i>Thêm vào kho</button>
                        <a href="/shopweb/quantri/inventory/management.php" class="btn btn-outline-secondary ms-2">
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