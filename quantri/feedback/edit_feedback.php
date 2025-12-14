<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$message = '';
$message_type = '';
$feedback = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $conn = createConnection();
    $sql = "SELECT * FROM customer_feedback WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $feedback = $stmt->get_result()->fetch_assoc();

    if (!$feedback) {
        $message = "Phản hồi không tồn tại!";
        $message_type = 'danger';
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $customer_name = $_POST['customer_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $feedback_text = $_POST['feedback'] ?? '';
        $status = $_POST['status'] ?? 'pending';

        if (!empty($customer_name) && !empty($email) && !empty($feedback_text)) {
            $sql = "UPDATE customer_feedback SET customer_name = ?, email = ?, feedback = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $customer_name, $email, $feedback_text, $status, $id);
            if ($stmt->execute()) {
                $message = "Cập nhật phản hồi thành công!";
                $message_type = 'success';
                echo "<script>alert('{$message}'); window.location.href = '/shopweb/quantri/feedback/customer_feedback.php';</script>";
                exit;
            } else {
                $message = "Cập nhật thất bại: " . $conn->error;
                $message_type = 'danger';
            }
        } else {
            $message = "Thông tin không hợp lệ!";
            $message_type = 'warning';
        }
        $conn->close();
    }
} else {
    $message = "ID không hợp lệ!";
    $message_type = 'danger';
}
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-comment-dots me-2"></i>Sửa phản hồi #<?php echo htmlspecialchars($feedback['id'] ?? ''); ?></h2>
        <p class="dashboard-subtitle">Cập nhật thông tin phản hồi khách hàng</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($feedback): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Thông tin phản hồi</h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($feedback['customer_name']); ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập tên khách hàng.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($feedback['email']); ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                        </div>
                        <div class="mb-3">
                            <label for="feedback" class="form-label">Phản hồi <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="4" required><?php echo htmlspecialchars($feedback['feedback']); ?></textarea>
                            <div class="invalid-feedback">Vui lòng nhập nội dung phản hồi.</div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending" <?php echo $feedback['status'] == 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                <option value="resolved" <?php echo $feedback['status'] == 'resolved' ? 'selected' : ''; ?>>Đã xử lý</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-gradient"><i class="fas fa-save me-1"></i>Cập nhật phản hồi</button>
                            <a href="/shopweb/quantri/feedback/customer_feedback.php" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-arrow-left me-1"></i>Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
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