<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');
require_once('../../functions.php');

$message = '';
$message_type = '';
$item = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $conn = createConnection();
    $sql = "SELECT i.*, p.name AS product_name FROM inventory i LEFT JOIN products p ON i.product_id = p.id WHERE i.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();

    if (!$item) {
        $message = "Sản phẩm không tồn tại trong kho!";
        $message_type = 'danger';
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $quantity = $_POST['quantity'] ?? 0;
        $location = $_POST['location'] ?? '';

        if ($quantity >= 0) {
            $sql = "UPDATE inventory SET quantity = ?, location = ?, last_updated = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $quantity, $location, $id);
            if ($stmt->execute()) {
                if (updateProductStock($conn, $item['product_id'])) {
                    $message = "Cập nhật thông tin kho thành công!";
                    $message_type = 'success';
                    $item['quantity'] = $quantity;
                    $item['location'] = $location;
                    $item['last_updated'] = date('Y-m-d H:i:s');
                } else {
                    $message = "Cập nhật thất bại khi cập nhật stock: " . $conn->error;
                    $message_type = 'warning';
                }
            } else {
                $message = "Cập nhật thất bại: " . $conn->error;
                $message_type = 'warning';
            }
        } else {
            $message = "Số lượng phải lớn hơn hoặc bằng 0!";
            $message_type = 'warning';
        }
    }
    $conn->close();
} else {
    $message = "ID không hợp lệ!";
    $message_type = 'danger';
}
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-edit me-2"></i>Sửa thông tin kho #<?php echo htmlspecialchars($item['id'] ?? ''); ?></h2>
        <p class="dashboard-subtitle">Cập nhật thông tin sản phẩm trong kho</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($item): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Thông tin sản phẩm</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Sản phẩm</label>
                            <input type="text" class="form-control" id="product_name" value="<?php echo htmlspecialchars($item['product_name'] ?? ''); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($item['quantity'] ?? 0); ?>" min="0" required>
                            <div class="invalid-feedback">Vui lòng nhập số lượng lớn hơn hoặc bằng 0.</div>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Vị trí</label>
                            <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($item['location'] ?? ''); ?>">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-gradient"><i class="fas fa-save me-1"></i>Cập nhật</button>
                            <a href="/shopweb/quantri/inventory/management.php" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-arrow-left me-1"></i>Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
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