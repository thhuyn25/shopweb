<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
$message = '';
$message_type = '';
$order = null;
$items = [];

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("Không tìm thấy ID đơn hàng. Vui lòng chọn một đơn hàng từ danh sách.");
    }

    $id = trim($_GET['id']);
    $sql = "SELECT o.*, o.customer_name AS full_name, o.phone, o.email 
            FROM orders o 
            WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Lỗi khi chuẩn bị truy vấn: ' . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        throw new Exception("Không tìm thấy đơn hàng với ID: " . htmlspecialchars($id));
    }

    $sql = "SELECT od.product_id, p.name, od.quantity, od.price 
            FROM order_details od 
            JOIN products p ON od.product_id = p.id 
            WHERE od.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($items as &$item) {
        $item['total'] = $item['quantity'] * $item['price'];
    }

} catch (Exception $e) {
    $message = $e->getMessage();
    $message_type = 'danger';
}

$conn->close();
?>

<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-eye me-2"></i>Chi tiết đơn hàng #<?php echo isset($order['id']) ? htmlspecialchars($order['id']) : ''; ?></h2>
        <p class="dashboard-subtitle">Thông tin chi tiết đơn hàng</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
            <i class="fas fa-<?php echo $message_type == 'danger' ? 'exclamation-triangle' : 'info-circle'; ?> me-2"></i>
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
        <div class="dashboard-section animate-fadeIn">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Chi tiết đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>ID đơn hàng:</strong> #<?php echo htmlspecialchars($order['id']); ?></p>
                            <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['full_name'] ?? 'Chưa có thông tin'); ?></p>
                            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'Chưa có thông tin'); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'Chưa có thông tin'); ?></p>
                            <p><strong>Phương thức thanh toán:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? 'Chưa xác định'); ?></p>
                            <p><strong>Tổng tiền:</strong> <span class="text-primary fw-bold"><?php echo currency_format($order['total_amount'] ?? 0); ?></span></p>
                            <p><strong>Trạng thái:</strong> <span class="badge bg-<?php echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'processing' ? 'info' : ($order['status'] == 'shipping' ? 'primary' : ($order['status'] == 'completed' ? 'success' : 'danger'))); ?>">
                                <?php echo ['pending' => 'Chờ xác nhận', 'processing' => 'Đang xử lý', 'shipping' => 'Đang giao', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'][$order['status']] ?? $order['status']; ?>
                            </span></p>
                            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách sản phẩm</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Mã sản phẩm</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Số lượng</th>
                                            <th>Giá (VNĐ)</th>
                                            <th>Thành tiền (VNĐ)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['product_id']); ?></td>
                                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td><?php echo currency_format($item['price']); ?></td>
                                                <td><?php echo currency_format($item['total']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                                            <td><strong><?php echo currency_format($order['total_amount']); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Thao tác</h5>
                        </div>
                        <div class="card-body">
                            <a href="status_update.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-gradient me-2">Cập nhật trạng thái</a>
                            <a href="print_invoice.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-outline-success">In hóa đơn</a>
                            <a href="order_list.php" class="btn btn-outline-secondary mt-2 w-100">Quay lại danh sách</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require('../includes/footer.php'); ?>