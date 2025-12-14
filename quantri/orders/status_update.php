<?php
session_start();
ob_start(); // Thêm output buffering để tránh lỗi header
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
$message = '';
$message_type = '';
$order = null;
$status_history = [];

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("Không tìm thấy ID đơn hàng. Vui lòng chọn một đơn hàng từ danh sách.");
    }

    $id_param = trim($_GET['id']);
    $sql = "SELECT o.*, o.customer_name AS full_name, o.phone, o.email 
            FROM orders o 
            WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Lỗi khi chuẩn bị truy vấn: ' . $conn->error);
    }
    $stmt->bind_param("i", $id_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        throw new Exception("Không tìm thấy đơn hàng với ID: " . htmlspecialchars($id_param));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_status = isset($_POST['status']) ? trim($_POST['status']) : '';
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
        $notify_customer = isset($_POST['notify_customer']);

        $valid_statuses = ['pending', 'processing', 'shipping', 'completed', 'cancelled'];
        if (!in_array($new_status, $valid_statuses)) {
            throw new Exception('Trạng thái không hợp lệ.');
        }
        if ($new_status === $order['status']) {
            $message = 'Trạng thái mới trùng với trạng thái hiện tại.';
            $message_type = 'warning';
        } else {
            $conn->begin_transaction();
            try {
                $sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Lỗi khi chuẩn bị cập nhật trạng thái: ' . $conn->error);
                }
                $stmt->bind_param("si", $new_status, $id_param);
                $stmt->execute();

                // Không cần session, gán created_by = null
                $created_by = null;
                $sql = "INSERT INTO order_status_history (order_id, status, notes, created_by, created_at) 
                        VALUES (?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Lỗi khi chuẩn bị lưu lịch sử trạng thái: ' . $conn->error);
                }
                $stmt->bind_param("issi", $order['id'], $new_status, $notes, $created_by);
                $stmt->execute();

                if ($new_status === 'cancelled' && $order['status'] !== 'cancelled') {
                    $sql = "SELECT product_id, quantity FROM order_details WHERE order_id = ?";
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        throw new Exception('Lỗi khi lấy chi tiết đơn hàng: ' . $conn->error);
                    }
                    $stmt->bind_param("i", $order['id']);
                    $stmt->execute();
                    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                    foreach ($items as $item) {
                        $sql = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        if (!$stmt) {
                            throw new Exception('Lỗi khi cập nhật kho: ' . $conn->error);
                        }
                        $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                        $stmt->execute();
                    }
                }

                $conn->commit();

                if ($notify_customer && !empty($order['email'])) {
                    $status_labels = [
                        'pending' => 'Chờ xác nhận',
                        'processing' => 'Đang xử lý',
                        'shipping' => 'Đang giao',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy'
                    ];
                    $email_message = "Đơn hàng #{$order['id']} của bạn đã được cập nhật trạng thái thành: {$status_labels[$new_status]}.";
                    $message .= ' (Email thông báo đã được gửi đến khách hàng)';
                }

                $message = 'Cập nhật trạng thái đơn hàng thành công!' . $message;
                $message_type = 'success';
                $order['status'] = $new_status;
            } catch (Exception $e) {
                $conn->rollback();
                throw new Exception('Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage());
            }
        }
    }

    $sql = "SELECT osh.*, u.username 
            FROM order_status_history osh
            LEFT JOIN users u ON osh.created_by = u.id
            WHERE osh.order_id = ? 
            ORDER BY osh.created_at DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Lỗi khi lấy lịch sử trạng thái: ' . $conn->error);
    }
    $stmt->bind_param("i", $order['id']);
    $stmt->execute();
    $status_history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $message = $e->getMessage();
    $message_type = 'danger';
}

$status_options = [
    'pending' => 'Chờ xác nhận',
    'processing' => 'Đang xử lý',
    'shipping' => 'Đang giao',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy'
];

$conn->close();
?>

<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-edit me-2"></i>Cập nhật trạng thái đơn hàng #<?php echo isset($order['id']) ? htmlspecialchars($order['id']) : ''; ?></h2>
        <p class="dashboard-subtitle">Quản lý trạng thái đơn hàng</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
            <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
            <?php echo htmlspecialchars($message); ?>
            <?php if ($message_type == 'danger'): ?>
                <a href="order_list.php" class="alert-link">Quay lại danh sách trạng thái</a>.
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
        <div class="dashboard-section animate-fadeIn">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Cập nhật trạng thái</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Trạng thái hiện tại:</label>
                                <span class="badge bg-<?php echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'processing' ? 'info' : ($order['status'] == 'shipping' ? 'primary' : ($order['status'] == 'completed' ? 'success' : 'danger'))); ?>">
                                    <?php echo htmlspecialchars($status_options[$order['status']] ?? $order['status']); ?>
                                </span>
                            </div>

                            <form method="POST" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Trạng thái mới <span class="text-danger">*</span></label>
                                            <select class="form-select" id="status" name="status" required>
                                                <?php foreach ($status_options as $value => $text): ?>
                                                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $value == $order['status'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($text); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">Vui lòng chọn trạng thái.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch mt-4">
                                                <input class="form-check-input" type="checkbox" id="notify_customer" name="notify_customer" checked>
                                                <label class="form-check-label" for="notify_customer">Gửi email thông báo cho khách hàng</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Nhập ghi chú về việc thay đổi trạng thái..."></textarea>
                                    <div class="form-text">Ghi chú sẽ được lưu trong lịch sử đơn hàng</div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-outline-secondary me-2"><i class="fas fa-undo me-1"></i>Reset</button>
                                    <button type="submit" class="btn btn-gradient"><i class="fas fa-save me-1"></i>Cập nhật trạng thái</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Hướng dẫn trạng thái</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><span class="badge bg-warning me-2">Chờ xác nhận</span><small class="text-muted">Đơn hàng mới, cần xác nhận</small></li>
                                        <li class="mb-2"><span class="badge bg-info me-2">Đang xử lý</span><small class="text-muted">Đã xác nhận, đang chuẩn bị hàng</small></li>
                                        <li class="mb-2"><span class="badge bg-primary me-2">Đang giao</span><small class="text-muted">Đang vận chuyển đến khách hàng</small></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><span class="badge bg-success me-2">Hoàn thành</span><small class="text-muted">Đã giao thành công</small></li>
                                        <li class="mb-2"><span class="badge bg-danger me-2">Đã hủy</span><small class="text-muted">Đơn hàng bị hủy (tự động hoàn kho)</small></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Thông tin đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['full_name'] ?? 'Chưa có thông tin'); ?></p>
                            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'Chưa có thông tin'); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'Chưa có thông tin'); ?></p>
                            <p><strong>Tổng tiền:</strong> <span class="text-primary fw-bold"><?php echo currency_format($order['total_amount'] ?? 0); ?></span></p>
                            <p><strong>Phương thức thanh toán:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? 'Chưa xác định'); ?></p>
                            <p><strong>Ngày đặt:</strong> <?php echo isset($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : 'Chưa xác định'; ?></p>
                            <p><strong>Cập nhật:</strong> <?php echo isset($order['updated_at']) ? date('d/m/Y H:i', strtotime($order['updated_at'])) : 'Chưa cập nhật'; ?></p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử trạng thái</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($status_history)): ?>
                                <div class="text-center text-muted">
                                    <p>Chưa có lịch sử trạng thái.</p>
                                </div>
                            <?php else: ?>
                                <div class="timeline">
                                    <?php foreach ($status_history as $history): ?>
                                        <div class="timeline-item">
                                            <div>
                                                <h4 class="mb-1"><?php echo htmlspecialchars($status_options[$history['status']] ?? $history['status']); ?></h4>
                                                <?php if ($history['notes']): ?>
                                                    <p class="text-muted mb-1 small"><?php echo htmlspecialchars($history['notes']); ?></p>
                                                <?php endif; ?>
                                                <small class="text-muted">
                                                    <?php echo date('d/m/Y H:i', strtotime($history['created_at'])); ?>
                                                    <?php if ($history['username']): ?> bởi <?php echo htmlspecialchars($history['username']); ?><?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

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

        const statusSelect = document.getElementById('status');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                const currentStatus = '<?php echo isset($order['status']) ? addslashes($order['status']) : ''; ?>';
                const newStatus = this.value;

                if (newStatus === 'cancelled' && currentStatus !== 'cancelled') {
                    if (!confirm('Hủy đơn hàng sẽ tự động hoàn trả số lượng sản phẩm về kho. Bạn có chắc chắn?')) {
                        this.value = currentStatus;
                        return;
                    }
                }

                if (newStatus === 'completed' && currentStatus !== 'completed') {
                    if (!confirm('Xác nhận đơn hàng đã hoàn thành?')) {
                        this.value = currentStatus;
                        return;
                    }
                }
            });
        }
    </script>

<?php require('../includes/footer.php'); ?>
<?php ob_end_flush(); ?>