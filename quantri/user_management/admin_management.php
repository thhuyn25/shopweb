<?php
session_start();
require('../includes/header.php'); 
require_once('../../database/dbhelper.php');

$conn = createConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(username LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params = [$search_param, $search_param];
}

$where_sql = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$count_sql = "SELECT COUNT(*) as total FROM users $where_sql";
$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}
$stmt->execute();
$total_records = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$sql = "SELECT id, username, email, role FROM users $where_sql ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat("s", count($params)) . "ii";
    $stmt->bind_param($types, ...array_merge($params, [$limit, $offset]));
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();

$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-user-shield me-2"></i>Quản lý Người dùng & Phân quyền</h2>
        <p class="dashboard-subtitle">Danh sách và quản lý quyền người dùng</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> alert-dismissible fade show animate-fadeIn" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Search Form -->
    <div class="dashboard-section animate-fadeIn">
        <form method="GET" class="search-form">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tên đăng nhập, email...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-gradient w-100"><i class="fas fa-search me-1"></i>Tìm kiếm</button>
                </div>
                <div class="col-md-2">
                    <a href="admin_management.php" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="dashboard-section animate-fadeIn">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Danh sách Người dùng</h5>
                <a href="admin_add.php" class="btn btn-gradient"><i class="fas fa-plus me-1"></i>Thêm người dùng</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên đăng nhập</th>
                                <th>Email</th>
                                <th>Quyền</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted fs-5">Không có người dùng nào</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="admin_details.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="admin_edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="admin_delete.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php if ($user['role'] == 'user'): ?>
                                                    <a href="admin_role_update.php?id=<?php echo $user['id']; ?>&role=admin" class="btn btn-sm btn-outline-success" title="Cấp quyền admin" onclick="return confirm('Cấp quyền admin cho <?php echo htmlspecialchars($user['username']); ?>?');">
                                                        <i class="fas fa-user-shield"></i>
                                                    </a>
                                                <?php elseif ($user['role'] == 'admin'): ?>
                                                    <a href="admin_role_update.php?id=<?php echo $user['id']; ?>&role=user" class="btn btn-sm btn-outline-primary" title="Hạ quyền xuống user" onclick="return confirm('Hạ quyền <?php echo htmlspecialchars($user['username']); ?> xuống user?');">
                                                        <i class="fas fa-user"></i>
                                                    </a>
                                                <?php endif; ?>
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

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>"><i class="fas fa-chevron-left"></i></a>
                </li>
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>"><i class="fas fa-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php require('../includes/footer.php'); ?>
</body>
</html>