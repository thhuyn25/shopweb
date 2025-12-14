<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
$order_id = isset($_GET['id']) ? $_GET['id'] : '';
$message = '';
$message_type = '';
$order = null;
$items = [];

if (empty($order_id)) {
    $message = "Vui lòng chọn một đơn hàng để in hóa đơn.";
    $message_type = 'info';
} else {
    $sql = "SELECT o.*, o.customer_name AS full_name, o.phone, o.email 
            FROM orders o 
            WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        $message = "Không tìm thấy đơn hàng với ID: $order_id.";
        $message_type = 'danger';
    } else {
        $sql = "SELECT od.product_id, p.name, od.quantity, od.price 
                FROM order_details od 
                JOIN products p ON od.product_id = p.id 
                WHERE od.order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($items as &$item) {
            $item['total'] = $item['quantity'] * $item['price'];
        }
    }
}

$conn->close();
?>

<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-file-invoice me-2"></i>In hóa đơn #<?php echo isset($order['order_id']) ? htmlspecialchars($order['order_id']) : ''; ?></h2>
        <p class="dashboard-subtitle">Hóa đơn/phiếu giao hàng</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
            <i class="fas fa-<?php echo $message_type == 'danger' ? 'exclamation-triangle' : 'info-circle'; ?> me-2"></i>
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($order): ?>
        <div class="dashboard-section animate-fadeIn">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Hóa đơn #<?php echo htmlspecialchars($order['order_id']); ?></h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3>CÔNG TY ABC</h3>
                        <p>Địa chỉ: 123 Đường ABC, TP. Hà Nội</p>
                        <p>SĐT: 0123-456-789 | Email: info@abc.com</p>
                        <h4>HÓA ĐƠN/PHIẾU GIAO HÀNG</h4>
                        <p>Mã đơn hàng: <?php echo htmlspecialchars($order['order_id']); ?></p>
                        <p>Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                        <p>Ngày in: <?php echo date('d/m/Y H:i'); ?></p>
                    </div>

                    <h5 class="mb-3"><i class="fas fa-user me-2"></i>Thông tin khách hàng</h5>
                    <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($order['full_name'] ?? 'Chưa có thông tin'); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['phone'] ?? 'Chưa có thông tin'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'] ?? 'Chưa có thông tin'); ?></p>

                    <h5 class="mt-4 mb-3"><i class="fas fa-shopping-cart me-2"></i>Chi tiết đơn hàng</h5>
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

                    <div class="text-center mt-4">
                        <p><strong>Ghi chú:</strong> Nếu có thắc mắc, vui lòng liên hệ qua SĐT: 0123-456-789 hoặc email: info@abc.com.</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button onclick="window.print()" class="btn btn-gradient"><i class="fas fa-print me-1"></i>In hóa đơn</button>
                <a href="order_list.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require('../includes/footer.php'); ?>