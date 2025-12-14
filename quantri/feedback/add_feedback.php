<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $feedback = $_POST['feedback'] ?? '';
    $status = $_POST['status'] ?? 'pending';

    if (!empty($customer_name) && !empty($email) && !empty($feedback)) {
        $conn = createConnection();
        $sql = "INSERT INTO customer_feedback (customer_name, email, feedback, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $customer_name, $email, $feedback, $status);
        if ($stmt->execute()) {
            $message = "Thêm phản hồi thành công!";
            $message_type = 'success';
            echo "<script>alert('{$message}'); window.location.href = '/shopweb/quantri/feedback/customer_feedback.php';</script>";
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
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-comment-dots me-2"></i>Thêm phản hồi mới</h2>
        <p class="dashboard-subtitle">Thêm phản hồi mới từ khách hàng</p>
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
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Thông tin phản hồi</h5>
            </div>
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                        <div class="invalid-feedback">Vui lòng nhập tên khách hàng.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Phản hồi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4" required></textarea>
                        <div class="invalid-feedback">Vui lòng nhập nội dung phản hồi.</div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="pending">Chờ xử lý</option>
                            <option value="resolved">Đã xử lý</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-gradient"><i class="fas fa-save me-1"></i>Thêm phản hồi</button>
                        <a href="/shopweb/quantri/feedback/customer_feedback.php" class="btn btn-outline-secondary ms-2">
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