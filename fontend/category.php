<?php
session_start();
include '../database/dbhelper.php';
$conn = createConnection();

// Lấy và xử lý tham số từ URL
$slug = isset($_GET['type']) ? $_GET['type'] : 'all';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$priceRange = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// Xác định danh mục
$categoryName = 'Tất cả sản phẩm';
$categoryId = null;
if ($slug !== 'all' && $slug !== 'new-arrivals' && $slug !== 'best-sellers') {
    $sqlCategory = "SELECT id, name FROM categories WHERE slug = ?";
    $stmt = $conn->prepare($sqlCategory);
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if (!empty($result)) {
        $categoryId = $result[0]['id'];
        $categoryName = ucfirst($result[0]['name']);
    } else {
        $categoryName = "Không xác định";
    }
} elseif ($slug === 'new-arrivals') {
    $categoryName = 'Sản phẩm mới';
} elseif ($slug === 'best-sellers') {
    $categoryName = 'Sản phẩm bán chạy';
}

// Xác định phạm vi giá
$priceMin = null;
$priceMax = null;
switch ($priceRange) {
    case 'under_150000':
        $priceMax = 150000;
        break;
    case '150000_250000':
        $priceMin = 150000;
        $priceMax = 250000;
        break;
    case '250000_350000':
        $priceMin = 250000;
        $priceMax = 350000;
        break;
    case '350000_500000':
        $priceMin = 350000;
        $priceMax = 500000;
        break;
    case 'over_500000':
        $priceMin = 500000;
        break;
}

// Kiểm tra sự tồn tại của cột created_at và sold_count
$columns = $conn->query("SHOW COLUMNS FROM products");
$hasCreatedAt = false;
$hasSoldCount = false;
while ($column = $columns->fetch_assoc()) {
    if ($column['Field'] === 'created_at') $hasCreatedAt = true;
    if ($column['Field'] === 'sold_count') $hasSoldCount = true;
}

// Xây dựng điều kiện truy vấn chung cho filter
$where = [];
$params = [];
$paramTypes = '';

if ($categoryId !== null) {
    $where[] = "category_id = ?";
    $params[] = $categoryId;
    $paramTypes .= 'i';
}
if ($slug === 'new-arrivals' && $hasCreatedAt) {
    $where[] = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
}
if ($keyword !== '') {
    $where[] = "name LIKE ?";
    $params[] = "%$keyword%";
    $paramTypes .= 's';
}
if ($priceMin !== null) {
    $where[] = "price >= ?";
    $params[] = $priceMin;
    $paramTypes .= 'i';
}
if ($priceMax !== null) {
    $where[] = "price <= ?";
    $params[] = $priceMax;
    $paramTypes .= 'i';
}
$whereSql = count($where) > 0 ? ' WHERE ' . implode(' AND ', $where) : '';

// Truy vấn đếm tổng số sản phẩm
$sqlCount = "SELECT COUNT(*) as total FROM products" . $whereSql;
$stmtCount = $conn->prepare($sqlCount);
if (!empty($params)) {
    $stmtCount->bind_param($paramTypes, ...$params);
}
$stmtCount->execute();
$totalItems = $stmtCount->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $perPage);

// Xác định sắp xếp
$orderBy = "ORDER BY id DESC";
if ($slug === 'best-sellers' && $hasSoldCount) {
    $orderBy = "ORDER BY sold_count DESC";
} else {
    switch ($sort) {
        case 'name_asc':
            $orderBy = "ORDER BY name ASC";
            break;
        case 'name_desc':
            $orderBy = "ORDER BY name DESC";
            break;
    }
}

// Truy vấn lấy danh sách sản phẩm
$offset = ($page - 1) * $perPage;
$paramsProducts = $params;
$paramTypesProducts = $paramTypes;
$paramsProducts[] = $offset;
$paramsProducts[] = $perPage;
$paramTypesProducts .= 'ii';

$sqlProducts = "SELECT * FROM products" . $whereSql . " $orderBy LIMIT ?, ?";
$stmtProducts = $conn->prepare($sqlProducts);
$stmtProducts->bind_param($paramTypesProducts, ...$paramsProducts);
$stmtProducts->execute();
$products = $stmtProducts->get_result()->fetch_all(MYSQLI_ASSOC);

// Hàm tính điểm đánh giá dựa trên số lượng đánh giá
function calculateReviewRating($reviewCount) {
    if ($reviewCount >= 50) return 5;
    if ($reviewCount >= 30) return 4;
    if ($reviewCount >= 15) return 3;
    if ($reviewCount >= 5) return 2;
    return 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Danh mục - <?php echo htmlspecialchars($categoryName); ?> | Streetwear Shop</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<nav class="breadcrumbs" aria-label="breadcrumb">
    <div class="breadcrumbs-container">
        <a href="../index.php">Trang chủ</a>
        <a href="category.php?type=<?php echo urlencode($slug); ?>">Danh mục</a>
        <span style="text-transform: uppercase;"><?php echo htmlspecialchars($categoryName); ?></span>
        <?php if ($keyword): ?>
            <span style="text-transform: uppercase;">Tìm kiếm: <?php echo htmlspecialchars($keyword); ?></span>
        <?php endif; ?>
    </div>
</nav>

<main class="content <?php echo (count($products) === 0) ? 'empty-page' : ''; ?>">
    <section class="container">
        <div class="category-header">
            <h2 class="category-title">
                <?php echo ($sort == 'best_selling' || $slug == 'best-sellers') ? 'Sản phẩm nổi bật' : htmlspecialchars($categoryName); ?>
            </h2>
            <form method="GET" id="filterForm-sort">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($slug); ?>">
                <input type="hidden" name="page" value="1">
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="name_asc" <?php if($sort=='name_asc') echo 'selected'; ?>>Tên: A-Z</option>
                    <option value="name_desc" <?php if($sort=='name_desc') echo 'selected'; ?>>Tên: Z-A</option>
                </select>
            </form>
        </div>
        <div class="filter-price-container">
            <form method="GET" id="filterForm-price">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($slug); ?>">
                <input type="hidden" name="page" value="1">
                <div class="filter-toggle">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
    <path d="M4 5H20V7H4V5ZM6 10H18V12H6V10ZM8 15H16V17H8V15Z" fill="#909097"/>
</svg>
                    <span class="filter-label">BỘ LỌC</span>
                    <span class="line"></span>
                </div>
                <div class="filter-options">
                    <select name="price_range" id="price_range" onchange="this.form.submit()">
                        <option value="">Giá sản phẩm</option>
                        <option value="under_150000" <?php if ($priceRange == 'under_150000') echo 'selected'; ?>>Dưới 150,000₫</option>
                        <option value="150000_250000" <?php if ($priceRange == '150000_250000') echo 'selected'; ?>>150,000₫ - 250,000₫</option>
                        <option value="250000_350000" <?php if ($priceRange == '250000_350000') echo 'selected'; ?>>250,000₫ - 350,000₫</option>
                        <option value="350000_500000" <?php if ($priceRange == '350000_500000') echo 'selected'; ?>>350,000₫ - 500,000₫</option>
                        <option value="over_500000" <?php if ($priceRange == 'over_500000') echo 'selected'; ?>>Trên 500,000₫</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="product-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <?php
                    $discount = isset($product['discount']) ? $product['discount'] : 0;
                    $originalPrice = isset($product['original_price']) ? $product['original_price'] : $product['price'];
                    $discountedPrice = $product['price'];
                    $rating = isset($product['rating']) ? $product['rating'] : 0;
                    $reviewCount = isset($product['review_count']) ? $product['review_count'] : 0;
                    $reviewRating = calculateReviewRating($reviewCount);
                    ?>
                    <div class="product-item">
                        <a href="/shopweb/fontend/product_detail.php?id=<?php echo htmlspecialchars($product['id']); ?>">
                            <?php if ($discount > 0): ?>
                                <div class="product-discount">-<?php echo $discount; ?>%</div>
                            <?php endif; ?>
                            <div class="product-image-container">
                                <img src="../images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-img">
                                <?php if (!empty($product['image_hover'])): ?>
                                    <img src="../images/<?php echo htmlspecialchars($product['image_hover']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="hover-img">
                                <?php endif; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-rating">
                                <div class="rating-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= round($rating)): ?>
                                            <svg viewBox="0 0 24 24" fill="#FFD700" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2l2.834 6.036L22 9.27l-5 4.73L18.668 22 12 18.26 5.332 22 7 14 2 9.27l7.166-1.234L12 2z"/>
                                            </svg>
                                        <?php else: ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2l2.834 6.036L22 9.27l-5 4.73L18.668 22 12 18.26 5.332 22 7 14 2 9.27l7.166-1.234L12 2z"/>
                                            </svg>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <div class="price-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $reviewRating): ?>
                                            <svg viewBox="0 0 24 24" fill="#FFD700" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2l2.834 6.036L22 9.27l-5 4.73L18.668 22 12 18.26 5.332 22 7 14 2 9.27l7.166-1.234L12 2z"/>
                                            </svg>
                                        <?php else: ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="#FFD700" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2l2.834 6.036L22 9.27l-5 4.73L18.668 22 12 18.26 5.332 22 7 14 2 9.27l7.166-1.234L12 2z"/>
                                            </svg>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <div class="rating-count">(<?php echo $reviewCount; ?> đánh giá)</div>
                                </div>
                            </div>
                            <div class="product-prices">
                                <span class="product-price-discounted"><?php echo number_format($discountedPrice, 0, ',', '.'); ?>đ</span>
                                <?php if ($discount > 0): ?>
                                    <span class="product-price-original"><?php echo number_format($originalPrice, 0, ',', '.'); ?>đ</span>
                                <?php endif; ?>
                                <a href="/shopweb/fontend/product_detail.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="add-to-cart" data-id="<?php echo htmlspecialchars($product['id']); ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo htmlspecialchars($discountedPrice); ?>" data-image="<?php echo htmlspecialchars($product['image']); ?>">
                                    <svg class="cart-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.29977 5H21L19 12H7.37671M20 16H8L6 3H3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Không có sản phẩm phù hợp.</p>
            <?php endif; ?>
        </div>
        <div class="pagination">
            <?php
            if ($page > 1) {
                echo '<a class="page-btn" href="?page=1&type=' . $slug . '&price_range=' . $priceRange . '&sort=' . $sort . '">«</a>';
            } else {
                echo '<span class="page-btn disabled">«</span>';
            }

            if ($page > 1) {
                echo '<a class="page-btn" href="?page=' . ($page - 1) . '&type=' . $slug . '&price_range=' . $priceRange . '&sort=' . $sort . '">‹</a>';
            } else {
                echo '<span class="page-btn disabled">‹</span>';
            }

            if ($page > 2) {
                echo '<a class="page-btn" href="?page=1&type=' . $slug . '&price_range=' . $priceRange . '&sort=' . $sort . '">1</a>';
                if ($page > 3) echo '<span class="dots">...</span>';
            }

            $start = max(1, $page - 1);
            $end = min($totalPages, $page + 1);

            for ($i = $start; $i <= $end; $i++) {
                if ($i == $page) {
                    echo '<span class="page-btn active">' . $i . '</span>';
                } else {
                    echo '<a class="page-btn" href="?page=' . $i . '&type=' . $slug . '&price_range=' . $priceRange . '&sort=' . $sort . '">' . $i . '</a>';
                }
            }

            if ($page < $totalPages - 1) {
                if ($page < $totalPages - 2) echo '<span class="dots">...</span>';
                echo '<a class="page-btn" href="?page=' . $totalPages . '&type=' . $slug . '&price_range=' . $priceRange . '&sort=' . $sort . '">' . $totalPages . '</a>';
            }

            if ($page < $totalPages) {
                echo '<a class="page-btn" href="?page=' . ($page + 1) . '&type=' . $slug . '&price_range=' . $priceRange . '&sort=' . $sort . '">›</a>';
            } else {
                echo '<span class="page-btn disabled">›</span>';
            }

            if ($page < $totalPages) {
                echo '<a class="page-btn" href="?page=' . $totalPages . '&type=' . $slug . '&price_range=' . $priceRange . '&sort=' . $sort . '">»</a>';
            } else {
                echo '<span class="page-btn disabled">»</span>';
            }
            ?>
        </div>
    </section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>