<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$sql = "SELECT i.*, p.name AS product_name FROM inventory i LEFT JOIN products p ON i.product_id = p.id ORDER BY i.id";
$inventory = executeResult($conn, $sql);
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-warehouse me-2"></i>Quản lý tồn kho</h2>
        <p class="dashboard-subtitle">Xem và cập nhật thông tin kho hàng</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách tồn kho</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="/shopweb/quantri/inventory/add_inventory.php" class="btn btn-gradient">
                        <i class="fas fa-plus me-1"></i>Thêm sản phẩm vào kho
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                                        <td><?php echo htmlspecialchars($item['id']); ?></td>
                                        <td><?php echo htmlspecialchars($item['product_name'] ?? 'Chưa có thông tin'); ?></td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($item['location'] ?? 'Chưa xác định'); ?></td>
                                        <td><?php echo htmlspecialchars($item['last_updated'] ?? 'Chưa cập nhật'); ?></td>
                                        <td>
                                            <a href="/shopweb/quantri/inventory/edit_inventory.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit me-1"></i>Sửa
                                            </a>
                                            <a href="/shopweb/quantri/inventory/delete_inventory.php?id=<?php echo htmlspecialchars($item['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi kho?');">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Không có sản phẩm trong kho</td>
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