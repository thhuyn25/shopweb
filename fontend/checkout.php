<?php
ob_start(); // Bật bộ đệm đầu ra để xử lý redirect
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
    die("Lỗi hệ thống: Không thể kết nối cơ sở dữ liệu. Vui lòng kiểm tra log.");
}

// Khởi tạo biến
$list_product_buy = [];
$total_money_buy = 0;
$num_order = 0;
$errors = [];
$input_errors = [];
$shipping_fee = 0;
$order_id = null;
$fullname = '';
$email = '';
$address = '';
$phone = '';
$payment_method = '';
$note = '';
$total_amount = 0;
$total_quantity = 0;

// Tạo token CSRF để bảo vệ form
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Lấy giá trị mặc định từ $_POST hoặc để trống
$fullname = htmlspecialchars(trim($_POST['fullname'] ?? ''), ENT_QUOTES, 'UTF-8');
$email = trim($_POST['email'] ?? '');
$address = htmlspecialchars(trim($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');
$phone = trim($_POST['phone'] ?? '');
$note = htmlspecialchars(trim($_POST['note'] ?? ''), ENT_QUOTES, 'UTF-8');

// Kiểm tra và xử lý giỏ hàng
if (!is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $errors[] = "Giỏ hàng của bạn hiện đang trống.";
} else {
    $product_ids = array_map(function($key) {
        $parts = explode('-', $key);
        return intval($parts[0]);
    }, array_keys($_SESSION['cart']));

    $product_ids = array_filter($product_ids);
    if (empty($product_ids)) {
        $errors[] = "Giỏ hàng chứa sản phẩm không hợp lệ.";
    } else {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $sql = "SELECT id, name, price, image, stock FROM products WHERE id IN ($placeholders)";
        $products = executePrepared($connection, $sql, $product_ids);
        if ($products === false) {
            $errors[] = "Lỗi khi truy vấn sản phẩm từ cơ sở dữ liệu.";
            error_log("Lỗi SQL: " . $sql . " - " . $connection->error);
        } else {
            $products_data = array_column($products, null, 'id');

            $clean_cart = [];
            foreach ($_SESSION['cart'] as $key => $item) {
                $parts = explode('-', $key);
                $pid = intval($parts[0]);
                $size = $parts[1] ?? 'S';
                if (isset($products_data[$pid]) && is_array($item) && isset($item['quantity']) && is_numeric($item['quantity']) && $item['quantity'] > 0) {
                    $clean_cart[$key] = $item;
                } else {
                    $errors[] = "Sản phẩm ID {$pid} không hợp lệ hoặc đã bị xóa.";
                }
            }
            $_SESSION['cart'] = $clean_cart;

            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $key => $item) {
                    $parts = explode('-', $key);
                    $pid = intval($parts[0]);
                    $size = $parts[1] ?? 'S';

                    $product = $products_data[$pid];
                    $qty = (int)$item['quantity'];
                    if ($qty > $product['stock']) {
                        $errors[] = "Sản phẩm '{$product['name']}' không đủ số lượng trong kho (còn {$product['stock']}).";
                    } else {
                        $list_product_buy[] = [
                            'product_id' => $pid,
                            'product_name' => $product['name'],
                            'product_price' => $product['price'],
                            'product_thumb' => $product['image'],
                            'qty' => $qty,
                            'size' => $size
                        ];
                        $total_money_buy += $product['price'] * $qty;
                        $num_order += $qty;
                    }
                }
            }
        }
    }
}

if (empty($list_product_buy)) {
    $errors[] = "Không tìm thấy sản phẩm hợp lệ trong giỏ hàng.";
}

$shipping_fee = $total_money_buy >= 1000000 ? 0 : 30000;
$total_amount = $total_money_buy + $shipping_fee;

$product_is_purchased = [];
foreach ($list_product_buy as $item) {
    $product_is_purchased[$item['product_id']] = [
        'quantity' => $item['qty'],
        'total_product_price' => $item['product_price'] * $item['qty'],
        'product_thumb' => $item['product_thumb'],
        'product_name' => $item['product_name'],
        'product_price' => $item['product_price'],
        'size' => $item['size']
    ];
}

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    error_log("Bắt đầu xử lý POST tại " . date('Y-m-d H:i:s'));

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Yêu cầu không hợp lệ.";
        error_log("CSRF token không khớp tại " . date('Y-m-d H:i:s'));
    } else {
        error_log("CSRF token hợp lệ, tiếp tục xử lý...");

        $fullname = htmlspecialchars(trim($_POST['fullname'] ?? $fullname), ENT_QUOTES, 'UTF-8');
        $email = trim($_POST['email'] ?? $email);
        $address = htmlspecialchars(trim($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');
        $phone = trim($_POST['phone'] ?? '');
        $note = htmlspecialchars(trim($_POST['note'] ?? ''), ENT_QUOTES, 'UTF-8');
        $payment_method = $_POST['payment_method'] ?? '';
        $total_quantity = (int)($_POST['total_product_quantity'] ?? 0);
        $submitted_products = json_decode($_POST['product_is_purchased'] ?? '{}', true);
        $submitted_shipping_fee = (float)($_POST['shipping_fee'] ?? 0);

        $total_amount = $total_money_buy + $shipping_fee;

        if (empty($fullname)) $input_errors['fullname'] = "Họ tên không được để trống.";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $input_errors['email'] = "Email không hợp lệ.";
        if (empty($address)) $input_errors['address'] = "Địa chỉ không được để trống.";
        if (empty($phone) || !preg_match('/^0[0-9]{9}$/', $phone)) $input_errors['phone'] = "Số điện thoại phải là 10 số và bắt đầu bằng 0.";
        if (strlen($note) > 500) $input_errors['note'] = "Ghi chú quá dài (tối đa 500 ký tự).";
        if (empty($payment_method)) $errors[] = "Vui lòng chọn phương thức thanh toán.";
        if ($total_quantity <= 0 || empty($submitted_products)) $errors[] = "Giỏ hàng không hợp lệ.";
        if ($submitted_shipping_fee != $shipping_fee) $errors[] = "Phí vận chuyển không hợp lệ.";

        foreach ($submitted_products as $product_id => $item) {
            if (!isset($product_is_purchased[$product_id]) ||
                $item['quantity'] != $product_is_purchased[$product_id]['quantity'] ||
                $item['product_price'] != $product_is_purchased[$product_id]['product_price'] ||
                $item['size'] != $product_is_purchased[$product_id]['size']) {
                $errors[] = "Dữ liệu sản phẩm không hợp lệ.";
                break;
            }
        }

        if (empty($errors) && empty($input_errors)) {
            try {
                $connection->autocommit(FALSE);

                // Kiểm tra khách hàng đã tồn tại dựa trên email
                $sql = "SELECT id FROM customers WHERE email = ?";
                $customer_result = executePrepared($connection, $sql, [$email]);
                if ($customer_result && count($customer_result) > 0) {
                    // Khách hàng đã tồn tại, lấy customer_id
                    $customer_id = $customer_result[0]['id'];
                } else {
                    // Thêm khách hàng mới vào bảng customers
                    $sql = "INSERT INTO customers (full_name, email, phone) 
                            VALUES (?, ?, ?)";
                    $customer_id = executePrepared($connection, $sql, [$fullname, $email, $phone], true);
                    if ($customer_id === false) {
                        throw new Exception("Lỗi khi thêm khách hàng mới: " . $connection->error);
                    }
                }

                // Thêm đơn hàng với customer_id
                $sql = "INSERT INTO orders (customer_id, user_id, customer_name, email, address, phone, payment_method, total_amount, status, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $params = [$customer_id, null, $fullname, $email, $address, $phone, $payment_method, $total_amount, 'pending'];
                $order_id = executePrepared($connection, $sql, $params, true);
                if ($order_id === false) {
                    throw new Exception("Lỗi tạo đơn hàng: " . $connection->error);
                }

                foreach ($submitted_products as $product_id => $item) {
                    $sql = "INSERT INTO order_details (order_id, product_id, quantity, price, size, created_at)
                            VALUES (?, ?, ?, ?, ?, NOW())";
                    if (!executePrepared($connection, $sql, [$order_id, $product_id, $item['quantity'], $item['product_price'], $item['size']])) {
                        throw new Exception("Lỗi khi lưu chi tiết đơn hàng cho sản phẩm ID $product_id: " . $connection->error);
                    }

                    $sql = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
                    if (!executePrepared($connection, $sql, [$item['quantity'], $product_id, $item['quantity']])) {
                        throw new Exception("Lỗi khi cập nhật tồn kho cho sản phẩm ID $product_id: " . $connection->error);
                    }
                }

                $connection->commit();
                $connection->autocommit(TRUE);

                unset($_SESSION['cart']);
                unset($_SESSION['csrf_token']);

                $_SESSION['order_complete'] = [
                    'order_id' => $order_id,
                    'fullname' => $fullname,
                    'email' => $email,
                    'address' => $address,
                    'phone' => $phone,
                    'payment_method' => $payment_method == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : $payment_method,
                    'amount' => $total_amount
                ];

                error_log("Chuyển hướng đến complete.php với order_id $order_id tại " . date('Y-m-d H:i:s'));
                header("Location: complete.php?order_id=$order_id");
                error_log("Sau header tại " . date('Y-m-d H:i:s'));
                ob_end_flush();
                exit;
            } catch (Exception $e) {
                $connection->rollback();
                $connection->autocommit(TRUE);
                $errors[] = "Lỗi khi xử lý đơn hàng: " . $e->getMessage();
                error_log("Lỗi transaction tại " . date('Y-m-d H:i:s') . ": " . $e->getMessage());
            }
        }
    }

    if (!empty($errors) || !empty($input_errors)) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Thanh toán - Streetwear Shop</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="../css/checkout.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#place-order').click(function(e) {
                if (!confirm('Bạn có chắc muốn đặt hàng?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</head>
<body>
    <?php if (file_exists('header.php')) { include 'header.php'; } else { echo "<p>Lỗi: Không thể tải header.</p>"; error_log("Lỗi: Tệp header.php không tồn tại tại " . date('Y-m-d H:i:s')); } ?>

    <?php if (!empty($list_product_buy)) { ?>
    <div id="main-content-wp" class="checkout-page">
        <div class="section" id="breadcrumb-wp">
            <div class="wp-inner">
                <ul class="list-item">
                    <li><a href="?page=home">Trang chủ</a></li>
                    <li><a href="#">Thanh toán</a></li>
                </ul>
            </div>
        </div>
        <div id="wrapper" class="wp-inner">
            <div class="section" id="customer-info-wp">
                <div class="section-head"><h1 class="section-title">Thông tin khách hàng</h1></div>
                <div class="section-detail">
                    <form method="POST" action="" name="form-checkout">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-row">
                            <div class="form-col full-width">
                                <label for="fullname">Họ và tên</label>
                                <input type="text" name="fullname" id="fullname"
                                    class="<?php echo isset($input_errors['fullname']) ? 'input-error' : ''; ?>"
                                    placeholder="<?php echo isset($input_errors['fullname']) ? $input_errors['fullname'] : 'Nhập họ và tên'; ?>"
                                    value="<?php echo $fullname; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col half-width">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email"
                                    class="<?php echo isset($input_errors['email']) ? 'input-error' : ''; ?>"
                                    placeholder="<?php echo isset($input_errors['email']) ? $input_errors['email'] : 'Nhập email'; ?>"
                                    value="<?php echo $email; ?>">
                            </div>
                            <div class="form-col half-width">
                                <label for="phone">Số điện thoại</label>
                                <input type="tel" name="phone" id="phone"
                                    class="<?php echo isset($input_errors['phone']) ? 'input-error' : ''; ?>"
                                    placeholder="<?php echo isset($input_errors['phone']) ? $input_errors['phone'] : 'Nhập số điện thoại'; ?>"
                                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col full-width">
                                <label for="address">Địa chỉ cụ thể</label>
                                <input type="text" name="address" id="address"
                                    class="<?php echo isset($input_errors['address']) ? 'input-error' : ''; ?>"
                                    placeholder="<?php echo isset($input_errors['address']) ? $input_errors['address'] : 'Nhập địa chỉ'; ?>"
                                    value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="form-row payment-method">
                            <div class="form-col full-width">
                                <label>Phương thức thanh toán</label>
                                <div class="payment-option">
                                    <input type="radio" id="cod" name="payment_method" value="cod" checked>
                                    <label for="cod"><span class="payment-icon"></span> Thanh toán khi nhận hàng (COD)</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col full-width">
                                <label for="note">Ghi chú</label>
                                <textarea name="note" id="note"><?php echo htmlspecialchars($_POST['note'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col full-width">
                                <input type="submit" id="place-order" value="Hoàn tất đơn hàng" name="place_order">
                            </div>
                        </div>
                        <input type="hidden" name="product_is_purchased" value='<?php echo htmlspecialchars(json_encode($product_is_purchased, JSON_UNESCAPED_UNICODE)); ?>'>
                        <input type="hidden" name="total_product_quantity" value="<?php echo $num_order; ?>">
                        <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
                        <input type="hidden" name="shipping_fee" value="<?php echo $shipping_fee; ?>">
                    </form>
                </div>
            </div>
            <div class="section" id="order-review-wp">
                <?php if (!empty($errors)) { ?>
                    <div class="alert-box error-box">
                        <ul>
                            <?php foreach ($errors as $error) { ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
                <div class="section-head"><h1 class="section-title">Thông tin đơn hàng</h1></div>
                <div class="section-detail">
                    <?php foreach ($list_product_buy as $item) {
                        $image_path = !empty($item['product_thumb']) ? '../images/' . htmlspecialchars($item['product_thumb']) : '../images/default.png';
                        $server_path = $_SERVER['DOCUMENT_ROOT'] . '/shopweb/' . ltrim(str_replace('../', '', $image_path), '/');
                        if (!file_exists($server_path)) {
                            $image_path = '../images/default.png';
                        }
                    ?>
                    <div class="product-summary">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" width="60" height="60">
                        </div>
                        <div class="product-info">
                            <p class="product-name"><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo $item['qty']; ?>)</p>
                            <p class="product-size"><?php echo htmlspecialchars($item['size']); ?></p>
                        </div>
                        <div class="product-price">
                            <strong><?php echo currency_format($item['product_price'] * $item['qty']); ?></strong>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="totals">
                        <div class="totals-row">
                            <span>Tạm tính</span>
                            <strong><?php echo currency_format($total_amount - $shipping_fee); ?></strong>
                        </div>
                        <div class="totals-row">
                            <span>Phí vận chuyển</span>
                            <strong><?php echo $shipping_fee ? currency_format($shipping_fee) : 'Miễn phí'; ?></strong>
                        </div>
                        <div class="totals-row grand-total">
                            <span><strong>Tổng cộng</strong></span>
                            <strong class="total-price"><?php echo currency_format($total_amount); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } else { ?>
    <div class="container notifi_checkout">
        <p>Giỏ hàng của bạn hiện đang trống. Vui lòng thêm sản phẩm để tiếp tục thanh toán.</p>
        <?php if (!empty($errors)) { ?>
            <div class="alert-box error-box">
                <ul>
                    <?php foreach ($errors as $error) { ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <a href="/shopweb/fontend/category.php?type=all" class="continue-shopping">
            <span class="arrow-icon">←</span> Tiếp tục mua hàng
        </a>
    </div>
    <?php } ?>

    <?php if (file_exists('footer.php')) { include 'footer.php'; } else { echo "<p>Lỗi: Không thể tải footer.</p>"; error_log("Lỗi: Tệp footer.php không tồn tại tại " . date('Y-m-d H:i:s')); } ?>
</body>
</html>
<?php ob_end_flush(); ?>