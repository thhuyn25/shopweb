<?php
session_start();
require('../includes/header.php');
require_once('../../database/dbhelper.php');

$conn = createConnection();
$customer = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, full_name, email, phone FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();

    if (!$customer) {
        $_SESSION['message'] = 'Không tìm thấy khách hàng với ID: ' . htmlspecialchars($id) . '.';
        $_SESSION['message_type'] = 'warning';
        header('Location: customer_management.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'ID khách hàng không hợp lệ.';
    $_SESSION['message_type'] = 'warning';
    header('Location: customer_management.php');
    exit();
}

$conn->close();
?>
<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"><i class="fas fa-user me-2"></i>Chi tiết khách hàng #<?php echo htmlspecialchars($customer['id']); ?></h2>
        <p class="dashboard-subtitle">Thông tin chi tiết khách hàng</p>
    </div>

    <div class="dashboard-section animate-fadeIn">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($customer['id']); ?></p>
                        <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($customer['full_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                        <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($customer['phone']); ?></p>
                    </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                    <a href="customer_management.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require('../includes/footer.php'); ?>