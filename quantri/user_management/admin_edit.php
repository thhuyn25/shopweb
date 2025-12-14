<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
$message = '';
$message_type = '';
$admin = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, username, email FROM users WHERE id = ? AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if (!$admin) {
        $message = 'Không tìm thấy admin với ID: ' . htmlspecialchars($id) . '.';
        $message_type = 'danger';
    }
}

if ($_POST && isset($admin)) {
    $id = $_POST['id'] ?? $admin['id'];
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($email)) {
        $sql_check_username = "SELECT id FROM users WHERE username = ? AND id != ? AND role = 'admin'";
        $stmt_check_username = $conn->prepare($sql_check_username);
        $stmt_check_username->bind_param("si", $username, $id);
        $stmt_check_username->execute();
        $username_exists = $stmt_check_username->get_result()->fetch_assoc();

        $sql_check_email = "SELECT id FROM users WHERE email = ? AND id != ? AND role = 'admin'";
        $stmt_check_email = $conn->prepare($sql_check_email);
        $stmt_check_email->bind_param("si", $email, $id);
        $stmt_check_email->execute();
        $email_exists = $stmt_check_email->get_result()->fetch_assoc();

        if ($username_exists) {
            $message = 'Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.';
            $message_type = 'danger';
        } elseif ($email_exists) {
            $message = 'Email đã tồn tại. Vui lòng sử dụng email khác.';
            $message_type = 'danger';
        } else {
            if (!empty($password)) {
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET username = ?, email = ?, password = ? WHERE id = ? AND role = 'admin'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $username, $email, $password_hashed, $id);
            } else {
                $sql = "UPDATE users SET username = ?, email = ? WHERE id = ? AND role = 'admin'";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $username, $email, $id);
            }

            if ($stmt->execute()) {
                $message = 'Cập nhật admin thành công! <a href="admin_management.php" class="alert-link">Quay lại danh sách admin</a>.';
                $message_type = 'success';
                $admin['username'] = $username;
                $admin['email'] = $email;
            } else {
                $message = 'Có lỗi xảy ra khi cập nhật admin: ' . $conn->error;
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
        <h2 class="dashboard-title"><i class="fas fa-edit me-2"></i>Chỉnh sửa admin #<?php echo htmlspecialchars($admin['id']); ?></h2>
        <p class="dashboard-subtitle">Cập nhật thông tin admin</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($admin): ?>
        <div class="dashboard-section animate-fadeIn">
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($admin['id']); ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập tên đăng nhập.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới (để trống nếu không muốn thay đổi)</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Nhập mật khẩu mới nếu bạn muốn thay đổi mật khẩu.</div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-gradient"><i class="fas fa-save me-1"></i>Lưu</button>
                            <a href="admin_management.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center animate-fadeIn" role="alert">
            Không tìm thấy admin hoặc có lỗi xảy ra.
        </div>
    <?php endif; ?>
</div>
<?php require('../includes/footer.php'); ?>