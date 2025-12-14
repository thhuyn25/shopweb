<?php
session_start();
include 'database/config.php';
include 'database/dbhelper.php';

$conn = createConnection();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM products";
$products = executeResult($conn, $sql);

$sql_best_sellers = "SELECT * FROM products ORDER BY RAND() LIMIT 4";
$best_sellers = executeResult($conn, $sql_best_sellers);

$sql_new_products = "SELECT * FROM products ORDER BY id DESC LIMIT 4";
$new_products = executeResult($conn, $sql_new_products);

$banners = [
    [
        'image' => '/shopweb/images/banner1.png',
        'title' => 'The Future is Bright'
    ],
    [
        'image' => '/shopweb/images/banner2.png',
        'title' => 'Summer Vibes 2025'
    ],
];

function renderProductItem($product) {
    $rating = isset($product['rating']) ? (float)$product['rating'] : 0;
    $reviewCount = isset($product['review_count']) ? (int)$product['review_count'] : 0;
    ?>
    <div class="product-item">
        <a href="/shopweb/fontend/product_detail.php?id=<?php echo htmlspecialchars($product['id']); ?>">
            <div class="product-content">
                <div class="product-image-container">
                    <img src="/shopweb/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <div class="product-rating">
                    <div class="rating-stars">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= round($rating)) {
                                echo '<svg viewBox="0 0 24 24" fill="#FFD700" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2l2.834 6.036L22 9.27l-5 4.73L18.668 22 12 18.26 5.332 22 7 14 2 9.27l7.166-1.234L12 2z"/>
                                </svg>';
                            } else {
                                echo '<svg viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2l2.834 6.036L22 9.27l-5 4.73L18.668 22 12 18.26 5.332 22 7 14 2 9.27l7.166-1.234L12 2z"/>
                                </svg>';
                            }
                        }
                        ?>
                    </div>
                    <span class="rating-count">(<?php echo $reviewCount; ?> đánh giá)</span>
                </div>
                <div class="product-prices">
                    <span class="price">
                        <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                    </span>
                    <svg class="cart-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.29977 5H21L19 12H7.37671M20 16H8L6 3H3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z"
                              stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <?php if (!empty($product['price_original']) && $product['price_original'] > $product['price']): ?>
                        <span class="product-price-original"><?php echo number_format($product['price_original'], 0, ',', '.'); ?> VNĐ</span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>
    <?php
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Shopweb - Mua sắm trực tuyến với các sản phẩm mới nhất và bán chạy nhất.">
    <title>Shopweb - Trang chủ</title>
    <link rel="stylesheet" href="/shopweb/css/index.css">
</head>
<body>
<?php include 'fontend/header.php'; ?>

<!-- Main Banner -->
<div class="banner-container">
    <div class="banner-scroll" id="bannerScroll">
        <?php foreach ($banners as $banner): ?>
            <div class="banner-slide">
                <img src="<?php echo htmlspecialchars($banner['image']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>">
                <div class="banner-title"><?php echo htmlspecialchars($banner['title']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <button onclick="scrollBanner(-1)">←</button>
    <button onclick="scrollBanner(1)">→</button>
</div>

<section class="service-features">
    <div class="service-box">
        <img src="/shopweb/icons/delivery.svg" alt="Giao hàng miễn phí">
        <h4>Giao hàng miễn phí</h4>
        <p>với đơn hàng từ 500k trở lên</p>
    </div>
    <div class="service-box">
        <img src="/shopweb/icons/support.svg" alt="Hỗ trợ 24/7">
        <h4>Hỗ trợ 24/7</h4>
        <p>Hỗ trợ online / offline 24/7</p>
    </div>
    <div class="service-box">
        <img src="/shopweb/icons/return.svg" height="80px" width="80px" alt="Miễn phí đổi trả">
        <h4>Miễn phí đổi trả</h4>
        <p>Trong vòng 7 ngày</p>
    </div>
    <div class="service-box">
        <img src="/shopweb/icons/order.svg" alt="Đặt hàng trực tuyến">
        <h4>Đặt hàng trực tuyến</h4>
        <p>Hotline: 0357 420 420</p>
    </div>
</section>

<!-- Section: The Future is Bright -->
<section class="future-section">
    <h2 class="section-title">
        <a href="/shopweb/fontend/category.php?type=new-arrivals" style="text-decoration: none; color: inherit;">
            THE FUTURE IS BRIGHT
        </a>
    </h2>
    <div class="product-grid">
        <?php if (empty($new_products)): ?>
            <p class="text-center">Hiện chưa có sản phẩm mới nào.</p>
        <?php else: ?>
            <?php foreach ($new_products as $product): ?>
                <?php renderProductItem($product); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Section: Best-Selling Items -->
<section class="best-sellers-section">
    <h2 class="section-title">
        <a href="/shopweb/fontend/category.php?type=best-sellers" style="text-decoration: none; color: inherit;">
            BEST-SELLING ITEMS
        </a>
    </h2>
    <div class="product-grid">
        <?php if (empty($best_sellers)): ?>
            <p class="text-center">Hiện chưa có sản phẩm bán chạy nào.</p>
        <?php else: ?>
            <?php foreach ($best_sellers as $product): ?>
                <?php renderProductItem($product); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- All Products Section -->
<section class="products">
    <h2 class="section-title">TẤT CẢ SẢN PHẨM</h2>
    <div class="product-grid">
        <?php if (empty($products)): ?>
            <p class="text-center">Không có sản phẩm nào để hiển thị.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <?php renderProductItem($product); ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'fontend/footer.php'; ?>

<!-- JavaScript -->
<script src="/shopweb/js/banner_scroll.js"></script>
<script>
    document.querySelectorAll('.cart-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            const formData = new FormData(form);

            fetch('/shopweb/fontend/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã thêm sản phẩm vào giỏ hàng!');
                } else {
                    alert('Lỗi khi thêm sản phẩm vào giỏ hàng: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã xảy ra lỗi khi thêm vào giỏ hàng.');
            });
        });
    });
</script>
</body>
</html>

<?php
$conn->close();
?>
