<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
$message = '';
$message_type = '';
$customer = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, full_name, email, phone FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();

    if (!$customer) {
        $_SESSION['message'] = 'Không tìm thấy khách hàng với ID: ' . htmlspecialchars($id) . '.';
        $_SESSION['message_type'] = 'warning';
        header('Location: customer_management.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'ID khách hàng không hợp lệ.';
    $_SESSION['message_type'] = 'warning';
    header('Location: customer_management.php');
    exit();
}

if ($_POST && isset($customer)) {
    $id = $_POST['id'] ?? $customer['id'];
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    if (!empty($full_name) && !empty($email) && !empty($phone)) {
        $sql_check_email = "SELECT id FROM customers WHERE email = ? AND id != ?";
        $stmt_check_email = $conn->prepare($sql_check_email);
        $stmt_check_email->bind_param("si", $email, $id);
        $stmt_check_email->execute();
        $email_exists = $stmt_check_email->get_result()->fetch_assoc();

        if ($email_exists) {
            $message = 'Email đã tồn tại. Vui lòng sử dụng email khác.';
            $message_type = 'danger';
        } else {
            $sql = "UPDATE customers SET full_name = ?, email = ?, phone = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $full_name, $email, $phone, $id);

            if ($stmt->execute()) {
                $message = 'Cập nhật khách hàng thành công! <a href="customer_management.php" class="alert-link">Quay lại danh sách khách hàng</a>.';
                $message_type = 'success';
                $customer['full_name'] = $full_name;
                $customer['email'] = $email;
                $customer['phone'] = $phone;
            } else {
                $message = 'Có lỗi xảy ra khi cập nhật khách hàng: ' . $conn->error;
                $message_type = 'danger';
            }
        }
    } else {
        $message = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
        $message_type = 'warning';
    }
}

$conn->close();
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-edit me-2"></i>Chỉnh sửa khách hàng #<?php echo htmlspecialchars($customer['id']); ?></h2>
        <p class="dashboard-subtitle">Cập nhật thông tin khách hàng</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="dashboard-section animate-fadeIn">
        <div class="card">
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($customer['id']); ?>">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($customer['full_name']); ?>" required>
                        <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                        <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                        <div class="invalid-feedback">Vui lòng nhập số điện thoại.</div>
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