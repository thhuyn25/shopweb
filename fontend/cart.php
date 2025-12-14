<?php
ob_start();
session_start();
require_once '../database/dbhelper.php';

// Initialize database connection
$connection = createConnection() or die(json_encode(['success' => false, 'error' => 'Database connection failed']));

// Input sanitization
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Initialize cart if not exists
$_SESSION['cart'] = $_SESSION['cart'] ?? [];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false];

    if ($_POST['action'] === 'add_to_cart') {
        $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $size = sanitize_input($_POST['size'] ?? 'S');

        if ($product_id === false || empty($size)) {
            $response['error'] = 'Invalid product ID or size';
            echo json_encode($response);
            exit;
        }

        $key = $product_id . '-' . $size;
        $_SESSION['cart'][$key] = [
            'product_id' => $product_id,
            'quantity' => ($_SESSION['cart'][$key]['quantity'] ?? 0) + $quantity,
            'size' => $size
        ];

        $response = [
            'success' => true,
            'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))
        ];
    } elseif ($_POST['action'] === 'get_cart_count') {
        $response = [
            'success' => true,
            'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))
        ];
    } else {
        $response['error'] = 'Invalid action';
    }

    echo json_encode($response);
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $product_id = filter_var($_POST['product_id'] ?? 0, FILTER_VALIDATE_INT);
    $size = sanitize_input($_POST['size'] ?? 'S');
    $key = $product_id . '-' . $size;

    if ($product_id === false || empty($size)) {
        header("Location: cart.php");
        exit;
    }

    if (isset($_POST['add_to_cart'])) {
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $_SESSION['cart'][$key] = [
            'product_id' => $product_id,
            'quantity' => ($_SESSION['cart'][$key]['quantity'] ?? 0) + $quantity,
            'size' => $size
        ];
    } elseif (isset($_POST['change_quantity'])) {
        $change = (int)($_POST['change'] ?? 0);
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity'] = max(0, $_SESSION['cart'][$key]['quantity'] + $change);
            if ($_SESSION['cart'][$key]['quantity'] <= 0) {
                unset($_SESSION['cart'][$key]);
            }
        }
    } elseif (isset($_POST['remove'])) {
        unset($_SESSION['cart'][$key]);
    } elseif (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
    }

    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Streetwear Shop</title>
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="/shopweb/css/cart-style.css">
</head>
<body class="cart-page">
<?php include 'header.php'; ?>
<section class="cart-container">
    <h2 class="cart-title">Giỏ hàng của bạn</h2>
    <div class="cart-row">
        <div class="cart-left">
            <?php
            $total = 0;
            if (!empty($_SESSION['cart'])) {
                echo '<div class="cart-products">';

                // Collect unique product IDs and validate cart entries
                $product_ids = [];
                foreach ($_SESSION['cart'] as $key => $item) {
                    if (!isset($item['product_id']) || !isset($item['size']) || empty($item['size'])) {
                        unset($_SESSION['cart'][$key]);
                        continue;
                    }
                    $product_ids[] = $item['product_id'];
                }
                $product_ids = array_unique($product_ids);

                $products_data = [];
                if (!empty($product_ids)) {
                    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
                    $sql = "SELECT id, name, price, image FROM products WHERE id IN ($placeholders)";
                    $stmt = $connection->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param(str_repeat('i', count($product_ids)), ...array_values($product_ids));
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $products_data = $result->fetch_all(MYSQLI_ASSOC);
                        $products_data = array_column($products_data, null, 'id');
                        $stmt->close();
                    } else {
                        error_log("Prepare failed: " . $connection->error);
                    }
                }

                // Remove invalid products from cart
                foreach ($_SESSION['cart'] as $key => $item) {
                    if (!isset($products_data[$item['product_id']])) {
                        unset($_SESSION['cart'][$key]);
                    }
                }

                // If cart empty after removal, show message
                if (empty($_SESSION['cart'])) {
                    echo '<div class="cart-empty">';
                    echo '<p>Giỏ hàng của bạn hiện đang trống hoặc sản phẩm đã bị xóa.</p>';
                    echo '<a href="/shopweb/fontend/category.php?type=all" class="continue-shopping">';
                    echo '<span class="arrow-icon">←</span> Tiếp tục mua hàng';
                    echo '</a>';
                    echo '</div>';
                } else {
                    foreach ($_SESSION['cart'] as $key => $item) {
                        $pid = $item['product_id'];
                        $size = $item['size'];

                        if (!isset($products_data[$pid])) {
                            unset($_SESSION['cart'][$key]);
                            continue;
                        }

                        $product = $products_data[$pid];
                        $subtotal = $product['price'] * $item['quantity'];
                        $total += $subtotal;
                        $image_path = !empty($product['image']) 
                            ? '/shopweb/images/' . htmlspecialchars($product['image']) 
                            : '/shopweb/images/default.png';

                        $cat_stmt = $connection->prepare("
                            SELECT has_size FROM categories WHERE id = (
                                SELECT category_id FROM products WHERE id = ?
                            ) LIMIT 1
                        ");
                        $has_size = 0;
                        if ($cat_stmt) {
                            $cat_stmt->bind_param("i", $pid);
                            $cat_stmt->execute();
                            $cat_result = $cat_stmt->get_result()->fetch_assoc();
                            $has_size = isset($cat_result['has_size']) ? intval($cat_result['has_size']) : 0;
                            $cat_stmt->close();
                        }

                        ?>
                        <div class="cart-item">
                            <form method="post" class="btn-close-form" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($pid) ?>">
                                <input type="hidden" name="size" value="<?= htmlspecialchars($size) ?>">
                                <button type="submit" name="remove" class="btn-close-custom">×</button>
                            </form>
                            <a href="/shopweb/fontend/product_detail.php?id=<?= htmlspecialchars($pid) ?>">
                                <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="cart-item-img">
                            </a>
                            <div class="cart-item-info">
                                <h4 class="product-name">
                                    <a href="/shopweb/fontend/product_detail.php?id=<?= htmlspecialchars($pid) ?>">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </a>
                                </h4>
                                <p class="product-price">Giá: <?= number_format($product['price'], 0, ',', '.') ?>đ</p>
                                <?php if ($has_size): ?>
                                    <p class="product-size">Size: <?= htmlspecialchars($size) ?></p>
                                <?php endif; ?>
                                <div class="quantity-control">
                                    <form method="post" class="quantity-form">
                                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($pid) ?>">
                                        <input type="hidden" name="size" value="<?= htmlspecialchars($size) ?>">
                                        <input type="hidden" name="change_quantity" value="1">
                                        <input type="hidden" name="change" value="-1">
                                        <button type="submit" class="quantity-button">-</button>
                                    </form>
                                    <div class="quantity-display"><?= $item['quantity'] ?></div>
                                    <form method="post" class="quantity-form">
                                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($pid) ?>">
                                        <input type="hidden" name="size" value="<?= htmlspecialchars($size) ?>">
                                        <input type="hidden" name="change_quantity" value="1">
                                        <input type="hidden" name="change" value="1">
                                        <button type="submit" class="quantity-button">+</button>
                                    </form>
                                </div>
                                <div class="product-subtotal-container">
                                    <p class="product-subtotal"><?= number_format($subtotal, 0, ',', '.') ?>đ</p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    echo '</div>';
                    echo '<form method="post" class="clear-cart-form" onsubmit="return confirm(\'Bạn có chắc muốn xóa toàn bộ giỏ hàng?\');">';
                    echo '<button type="submit" name="clear_cart" class="clear-cart-button">Xóa toàn bộ</button>';
                    echo '</form>';
                }
            } else {
                echo '<div class="cart-empty">';
                echo '<p>Giỏ hàng của bạn đang trống</p>';
                echo '<a href="/shopweb/fontend/category.php?type=all" class="continue-shopping">';
                echo '<span class="arrow-icon">←</span> Tiếp tục mua hàng';
                echo '</a>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="cart-right">
            <div class="order-summary">
                <h3 class="order-summary-title">Thông tin đơn hàng</h3>
                <div class="order-summary-content">
                    <div class="order-total">
                        <span>Tổng tiền:</span>
                        <strong><?= number_format($total, 0, ',', '.') ?>đ</strong>
                    </div>
                    <a href="/shopweb/fontend/checkout.php" class="btn-checkout <?= $total == 0 ? 'disabled' : '' ?>" <?= $total == 0 ? 'aria-disabled="true"' : '' ?>>THANH TOÁN</a>
                    <?php if (!empty($_SESSION['cart'])) { ?>
                    <div class="continue-shopping-container">
                        <a href="/shopweb/fontend/category.php?type=all" class="continue-shopping">
                            <span class="arrow-icon">←</span> Tiếp tục mua hàng
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
</body>
</html>
<?php ob_end_flush(); ?>