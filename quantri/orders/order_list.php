<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(o.id LIKE ? OR o.customer_name LIKE ? OR o.phone LIKE ?)";
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param];
}

if (!empty($status_filter)) {
    $where_conditions[] = "o.status = ?";
    $params[] = $status_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(o.created_at) >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(o.created_at) <= ?";
    $params[] = $date_to;
}

$where_sql = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$count_sql = "SELECT COUNT(*) as total FROM orders o $where_sql";
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$sql = "SELECT o.*, COUNT(od.id) as item_count
        FROM orders o 
        LEFT JOIN order_details od ON o.id = od.order_id
        $where_sql
        GROUP BY o.id
        ORDER BY o.created_at DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat("s", count($params)) . "ii";
    $stmt->bind_param($types, ...array_merge($params, [$limit, $offset]));
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-list me-2"></i>Quản lý đơn hàng</h2>
        <p class="dashboard-subtitle">Danh sách tất cả đơn hàng</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <form method="GET" class="search-form">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ID đơn, tên khách hàng, SĐT...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                        <option value="processing" <?php echo $status_filter == 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                        <option value="shipping" <?php echo $status_filter == 'shipping' ? 'selected' : ''; ?>>Đang giao</option>
                        <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                        <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-gradient w-100"><i class="fas fa-search me-1"></i>Tìm kiếm</button>
                    <a href="?" class="btn btn-outline-secondary w-100 mt-2"><i class="fas fa-redo me-1"></i>Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Danh sách đơn hàng</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Số điện thoại</th>
                                <th>Số sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày đặt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted fs-5">Không có đơn hàng nào</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?php echo htmlspecialchars($order['id']); ?></strong></td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['phone'] ?? 'Chưa có thông tin'); ?></td>
                                        <td><span class="badge bg-info"><?php echo $order['item_count']; ?> sản phẩm</span></td>
                                        <td><strong class="text-primary"><?php echo currency_format($order['total_amount']); ?></strong></td>
                                        <td><span class="badge bg-<?php echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'processing' ? 'info' : ($order['status'] == 'shipping' ? 'primary' : ($order['status'] == 'completed' ? 'success' : 'danger'))); ?>">
                                            <?php echo ['pending' => 'Chờ xác nhận', 'processing' => 'Đang xử lý', 'shipping' => 'Đang giao', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'][$order['status']] ?? $order['status']; ?>
                                        </span></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="status_update.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-sm btn-outline-warning" title="Cập nhật trạng thái">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="print_invoice.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-success" title="In hóa đơn">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>"><i class="fas fa-chevron-left"></i></a>
                </li>
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>"><i class="fas fa-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

    <div class="dashboard-section animate-fadeIn">
        <div class="card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h5 class="text-primary"><?php echo $total_records; ?></h5>
                        <small class="text-muted">Tổng đơn hàng</small>
                    </div>
                    <div class="col-md-3">
                        <h5 class="text-warning"><?php echo array_sum(array_column($orders, 'item_count')); ?></h5>
                        <small class="text-muted">Tổng sản phẩm</small>
                    </div>
                    <div class="col-md-3">
                        <h5 class="text-success"><?php echo currency_format(array_sum(array_column($orders, 'total_amount'))); ?></h5>
                        <small class="text-muted">Tổng doanh thu</small>
                    </div>
                    <div class="col-md-3">
                        <h5 class="text-info"><?php echo $page; ?>/<?php echo $total_pages; ?></h5>
                        <small class="text-muted">Trang hiện tại</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('../includes/footer.php'); ?>