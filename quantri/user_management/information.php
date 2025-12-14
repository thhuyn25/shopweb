<?php
  session_start();
  require('../includes/header.php');
  require_once('../../database/dbhelper.php');

  $conn = createConnection();

  // Get statistics
  $customer_count = $conn->query("SELECT COUNT(*) as total FROM customers")->fetch_assoc()['total'];
  $admin_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];
  $user_count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
  $order_count = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
  $total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'];

  $conn->close();

  // Xử lý thông báo từ session
  $message = $_SESSION['message'] ?? '';
  $message_type = $_SESSION['message_type'] ?? '';
  unset($_SESSION['message']);
  unset($_SESSION['message_type']);
  ?>
  <div>
      <div class="dashboard-header animate-fadeIn">
          <h2 class="dashboard-title"><i class="fas fa-info-circle me-2"></i>Thông tin cần quản lý</h2>
          <p class="dashboard-subtitle">Tổng quan hệ thống</p>
      </div>

      <?php if ($message): ?>
          <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fadeIn" role="alert">
              <?php echo htmlspecialchars($message); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
      <?php endif; ?>

      <!-- Statistics -->
      <div class="row mt-4 justify-content-center">
          <div class="col-md-3 col-sm-6 mb-4">
              <div class="stats-card animate-fadeIn text-center">
                  <div class="icon"><i class="fas fa-users"></i></div>
                  <h3><?php echo htmlspecialchars($customer_count); ?></h3>
                  <p>TỔNG KHÁCH HÀNG</p>
              </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-4">
              <div class="stats-card animate-fadeIn text-center">
                  <div class="icon"><i class="fas fa-user-shield"></i></div>
                  <h3><?php echo htmlspecialchars($admin_count); ?></h3>
                  <p>TỔNG ADMIN</p>
              </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-4">
              <div class="stats-card animate-fadeIn text-center">
                  <div class="icon"><i class="fas fa-user"></i></div>
                  <h3><?php echo htmlspecialchars($user_count); ?></h3>
                  <p>TỔNG NGƯỜI DÙNG</p>
              </div>
          </div>
          <div class="col-md-3 col-sm-6 mb-4">
              <div class="stats-card animate-fadeIn text-center">
                  <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                  <h3><?php echo htmlspecialchars($order_count); ?></h3>
                  <p>TỔNG ĐƠN HÀNG</p>
              </div>
          </div>

      </div>

      <!-- Quick Links -->
      <div class="dashboard-section animate-fadeIn">
          <div class="card">
              <div class="card-header">
                  <h6 class="section-header"><i class="fas fa-link me-2"></i>Liên kết nhanh</h6>
              </div>
              <div class="card-body">
                  <div class="row justify-content-center">
                      <div class="col-md-4 col-sm-6 mb-3">
                          <a href="/shopweb/quantri/user_management/customer_management.php" class="btn btn-gradient w-100 text-center">
                              <i class="fas fa-users me-2"></i>Quản lý khách hàng
                          </a>
                      </div>
                      <div class="col-md-4 col-sm-6 mb-3">
                          <a href="/shopweb/quantri/user_management/admin_management.php" class="btn btn-gradient w-100 text-center">
                              <i class="fas fa-user-shield me-2"></i>Phân quyền
                          </a>
                      </div>
                      <div class="col-md-4 col-sm-6 mb-3">
                          <a href="/shopweb/quantri/user_management/admin_add.php" class="btn btn-gradient w-100 text-center">
                              <i class="fas fa-plus me-2"></i>Thêm admin mới
                          </a>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <?php require('../includes/footer.php'); ?>