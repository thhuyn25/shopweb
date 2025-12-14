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
      $where_conditions[] = "(c.full_name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?)";
      $search_param = "%$search%";
      $params = [$search_param, $search_param, $search_param];
  }

  $where_sql = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

  // Get total records
  $count_sql = "SELECT COUNT(*) as total FROM customers c $where_sql";
  $stmt = $conn->prepare($count_sql);
  if (!empty($params)) {
      $stmt->bind_param(str_repeat("s", count($params)), ...$params);
  }
  $stmt->execute();
  $total_records = $stmt->get_result()->fetch_assoc()['total'];
  $total_pages = ceil($total_records / $limit);

  // Get customers
  $sql = "SELECT c.* FROM customers c $where_sql ORDER BY c.id DESC LIMIT ? OFFSET ?";
  $stmt = $conn->prepare($sql);
  if (!empty($params)) {
      $types = str_repeat("s", count($params)) . "ii";
      $stmt->bind_param($types, ...array_merge($params, [$limit, $offset]));
  } else {
      $stmt->bind_param("ii", $limit, $offset);
  }
  $stmt->execute();
  $customers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $conn->close();

  $message = $_SESSION['message'] ?? '';
  $message_type = $_SESSION['message_type'] ?? '';
  unset($_SESSION['message']);
  unset($_SESSION['message_type']);
  ?>
  <div>
      <div class="dashboard-header animate-fadeIn">
          <h2 class="dashboard-title"><i class="fas fa-users me-2"></i>Quản lý khách hàng</h2>
          <p class="dashboard-subtitle">Danh sách khách hàng</p>
      </div>

      <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
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
                      <input type="text" class="form-control" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tên, email, SĐT...">
                  </div>
                  <div class="col-md-2">
                      <button type="submit" class="btn btn-gradient w-100"><i class="fas fa-search me-1"></i>Tìm kiếm</button>
                  </div>
                  <div class="col-md-2">
                      <a href="customer_management.php" class="btn btn-outline-secondary w-100"><i class="fas fa-redo me-1"></i>Reset</a>
                  </div>
              </div>
          </form>
      </div>

      <!-- Customers Table -->
      <div class="dashboard-section animate-fadeIn">
          <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0"><i class="fas fa-users me-2"></i>Danh sách khách hàng</h5>
                  <a href="customer_add.php" class="btn btn-gradient"><i class="fas fa-plus me-1"></i>Thêm khách hàng</a>
              </div>
              <div class="card-body p-0">
                  <div class="table-responsive">
                      <table class="table">
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>Họ và tên</th>
                                  <th>Email</th>
                                  <th>Số điện thoại</th>
                                  <th>Thao tác</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php if (empty($customers)): ?>
                                  <tr>
                                      <td colspan="5" class="text-center py-5">
                                          <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                          <p class="text-muted fs-5">Không có khách hàng nào</p>
                                      </td>
                                  </tr>
                              <?php else: ?>
                                  <?php foreach ($customers as $customer): ?>
                                      <tr>
                                          <td><?php echo htmlspecialchars($customer['id']); ?></td>
                                          <td><?php echo htmlspecialchars($customer['full_name']); ?></td>
                                          <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                          <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                          <td>
                                              <div class="btn-group" role="group">
                                                  <a href="customer_details.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                      <i class="fas fa-eye"></i>
                                                  </a>
                                                  <a href="customer_edit.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                                      <i class="fas fa-edit"></i>
                                                  </a>
                                                  <a href="customer_delete.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');">
                                                      <i class="fas fa-trash"></i>
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