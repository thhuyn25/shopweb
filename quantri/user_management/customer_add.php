<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (!empty($full_name) && !empty($email) && !empty($phone)) {
        $sql_check_email = "SELECT id FROM customers WHERE email = ?";
        $stmt_check_email = $conn->prepare($sql_check_email);
        if (!$stmt_check_email) {
            $message = 'Lỗi chuẩn bị truy vấn kiểm tra email: ' . $conn->error;
            $message_type = 'danger';
        } else {
            $stmt_check_email->bind_param("s", $email);
            $stmt_check_email->execute();
            $email_exists = $stmt_check_email->get_result()->fetch_assoc();

            if ($email_exists) {
                $message = 'Email đã tồn tại. Vui lòng sử dụng email khác.';
                $message_type = 'danger';
            } else {
                $sql = "INSERT INTO customers (full_name, email, phone, address) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    $message = 'Lỗi chuẩn bị truy vấn thêm khách hàng: ' . $conn->error;
                    $message_type = 'danger';
                } else {
                    $stmt->bind_param("ssss", $full_name, $email, $phone, $address);
                    if ($stmt->execute()) {
                        $message = 'Thêm khách hàng thành công! <a href="customer_management.php" class="alert-link">Quay lại danh sách khách hàng</a>.';
                        $message_type = 'success';
                    } else {
                        $message = 'Có lỗi xảy ra khi thêm khách hàng: ' . $conn->error;
                        $message_type = 'danger';
                    }
                    $stmt->close();
                }
            }
            $stmt_check_email->close();
        }
    } else {
        $message = 'Vui lòng điền đầy đủ thông tin bắt buộc (Họ và tên, Email, Số điện thoại).';
        $message_type = 'warning';
    }
}

$conn->close();
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-plus me-2"></i>Thêm khách hàng mới</h2>
        <p class="dashboard-subtitle">Thêm khách hàng mới vào hệ thống</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="dashboard-section animate-fadeIn">
        <div class="card">
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
                        <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                        <div class="invalid-feedback">Vui lòng nhập số điện thoại.</div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-gradient"><i class="fas fa-save me-1"></i>Lưu</button>
                        <a href="customer_management.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require('../includes/footer.php'); ?>