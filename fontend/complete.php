<?php
ob_start();
session_start();

// Kiểm tra và bao gồm tệp dbhelper.php
if (!file_exists('../database/dbhelper.php')) {
    error_log("Lỗi: Không tìm thấy tệp dbhelper.php tại " . date('Y-m-d H:i:s'));
    die("Lỗi hệ thống: Không tìm thấy tệp cấu hình cơ sở dữ liệu.");
}
include '../database/dbhelper.php';

// Tạo kết nối cơ sở dữ liệu
$connection = createConnection();
if (!$connection) {
    error_log("Lỗi: Không thể kết nối cơ sở dữ liệu tại " . date('Y-m-d H:i:s') . " - " . mysqli_connect_error());
    die("Lỗi hệ thống: Không thể kết nối cơ sở dữ liệu.");
}

$errors = [];
$data = [];
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if ($order_id) {
    $sql = "SELECT id, customer_name, email, address, phone, payment_method, total_amount 
            FROM orders WHERE id = ?";
    $order = executePrepared($connection, $sql, [$order_id]);
    if ($order && count($order) > 0) {
        $order = $order[0];
        $data = [
            'order_id' => $order['id'],
            'fullname' => $order['customer_name'],
            'email' => $order['email'],
            'address' => $order['address'],
            'phone' => $order['phone'],
            'payment_method' => $order['payment_method'] == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : $order['payment_method'],
            'amount' => $order['total_amount']
        ];
        $_SESSION['order_complete'] = $data;
    } else {
        $errors[] = "Không tìm thấy đơn hàng với mã #$order_id.";
    }
} else {
    $errors[] = "Không tìm thấy thông tin đơn hàng.";
}

if (empty($errors)) {
    $order_id = (int)$data['order_id'];
    $customer_name = htmlspecialchars($data['fullname'] ?? '', ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($data['phone'] ?? '', ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($data['address'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $payment_method = htmlspecialchars($data['payment_method'] ?? '', ENT_QUOTES, 'UTF-8');
    $amount = (float)($data['amount'] ?? 0);
    $shipping_fee = ($amount >= 1000000) ? 0 : 30000;

    $order_items = [];
    if ($order_id) {
        $sql = "SELECT od.*, p.name AS product_name, p.image
                FROM order_details od
                JOIN products p ON od.product_id = p.id
                WHERE od.order_id = ?";
        $order_items = executePrepared($connection, $sql, [$order_id]);
        if ($order_items === false) {
            $errors[] = "Lỗi khi truy vấn chi tiết đơn hàng.";
            error_log("Lỗi SQL: " . $sql . " - " . $connection->error);
        }
    }
} else {
    $errors[] = "Không có thông tin đơn hàng để hiển thị.";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Hoàn tất đơn hàng - Streetwear Shop</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="../css/complete.css">
</head>
<body>
    <?php if (file_exists('header.php')) { include 'header.php'; } else { echo "<p>Lỗi: Không thể tải header.</p>"; error_log("Lỗi: Tệp header.php không tồn tại tại " . date('Y-m-d H:i:s')); } ?>

    <div id="main-content-wp" class="complete-page">
        <?php if (!empty($errors)) { ?>
            <div class="alert-box error-box">
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php } ?>
                </ul>
            </div>
            <a href="/shopweb/fontend/index.php" class="btn-back">Quay về trang chủ</a>
        <?php } else { ?>
        <div class="container">
            <div class="order-row" >
            <div class="order-left">
                <div class="section box">
                    <h1>ĐẶT HÀNG THÀNH CÔNG</h1>
                    <p><strong>Mã đơn hàng:</strong> #<?php echo $order_id; ?></p>
                    <div class="order-info box">
                        <h2>Thông tin giao hàng</h2>
                        <p><strong>Họ tên:</strong> <?php echo $customer_name; ?></p>
                        <p><strong>SĐT:</strong> <?php echo $phone; ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo $address; ?></p>
                        <p><strong>Thanh toán:</strong> <?php echo $payment_method; ?></p>
                    </div>
                    <p>Cảm ơn bạn đã mua hàng!</p>
                    <a href="/shopweb/fontend/category.php?type=all" class="btn-continue">Tiếp tục mua hàng</a>
                </div>
            </div>
            <div class="order-right">
                <div class="order-items box">
                    <h2>Sản phẩm</h2>
                    <?php if (!empty($order_items)) { ?>
                        <?php foreach ($order_items as $item) { ?>
                            <div class="product-item">
                            <img src="../images/<?php echo htmlspecialchars($item['image'] ?? 'default.png'); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <div class="product-details">
                                <p class="product-name">
                                    <?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo $item['quantity']; ?>)
                                </p>
                                <p class="product-size"><?php echo htmlspecialchars($item['size']); ?></p>
                            </div>
                            <div class="product-price">
                                <?php echo currency_format($item['price'] * $item['quantity']); ?>
                            </div>
                        </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p>Không có sản phẩm nào trong đơn hàng.</p>
                    <?php } ?>
                    <div class="totals">
                        <p><span>Tạm tính</span><span><?php echo currency_format($amount - $shipping_fee); ?></span></p>
                        <p><span>Phí vận chuyển</span><span><?php echo $shipping_fee ? currency_format($shipping_fee) : 'Miễn phí'; ?></span></p>
                        <p class="total-line"><span>Tổng cộng</span><span><?php echo currency_format($amount); ?></span></p>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <?php } ?>
        <?php if (file_exists('footer.php')) { include 'footer.php'; } else { echo "<p>Lỗi: Không thể tải footer.</p>"; error_log("Lỗi: Tệp footer.php không tồn tại tại " . date('Y-m-d H:i:s')); } ?>
    </div>
</body>
</html>
<?php ob_end_flush(); ?>