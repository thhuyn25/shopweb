<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT p.id AS product_id, p.name AS product_name, 
               COALESCE(i.quantity, 0) AS quantity, 
               COALESCE(i.location, 'Chưa nhập kho') AS location, 
               COALESCE(i.last_updated, 'Chưa cập nhật') AS last_updated, 
               i.id AS inventory_id
        FROM products p 
        LEFT JOIN inventory i ON p.id = i.product_id 
        ORDER BY COALESCE(i.last_updated, p.created_at) DESC";
$inventory = executeResult($conn, $sql);
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-clipboard-check me-2"></i>Kiểm kho</h2>
        <p class="dashboard-subtitle">Kiểm tra thông tin kho hàng</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách kiểm kho</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>ID Sản phẩm</th>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Vị trí</th>
                                <th>Cập nhật lần cuối</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($inventory && !empty($inventory)): ?>
                                <?php foreach ($inventory as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_id']); ?></td>
                                        <td><?php echo htmlspecialchars($item['product_name'] ?? 'Chưa có thông tin'); ?></td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($item['location']); ?></td>
                                        <td><?php echo htmlspecialchars($item['last_updated']); ?></td>
                                        <td>
                                            <?php if ($item['inventory_id']): ?>
                                                <a href="/shopweb/quantri/inventory/edit_inventory.php?id=<?php echo htmlspecialchars($item['inventory_id']); ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit me-1"></i>Cập nhật
                                                </a>
                                            <?php else: ?>
                                                <a href="/shopweb/quantri/inventory/add_inventory.php?product_id=<?php echo htmlspecialchars($item['product_id']); ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus me-1"></i>Nhập kho
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Không có sản phẩm để kiểm kho</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$conn->close();
require('../includes/footer.php');
?>