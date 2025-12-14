<?php
session_start();
include '../database/dbhelper.php';
$conn = createConnection() or die("Lỗi kết nối DB");

// 1. Lấy sản phẩm
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Sản phẩm không tồn tại hoặc đã bị xóa.");
}

// 2. Thông tin đánh giá
$review_stmt = $conn->prepare("
    SELECT IFNULL(AVG(rating), 0) AS avg_rating, COUNT(*) AS total_reviews
    FROM product_reviews WHERE product_id = ?
");
$review_stmt->bind_param("i", $product_id);
$review_stmt->execute();
$review_result = $review_stmt->get_result()->fetch_assoc();
$rating = round($review_result['avg_rating'], 1);
$reviewCount = intval($review_result['total_reviews']);

// 3. Thông tin danh mục
$category_id = intval($product['category_id'] ?? 0);
$category_slug = null;
$has_size = 0; // mặc định: không có bảng size

if ($category_id > 0) {
    $stmt = $conn->prepare("SELECT slug, has_size FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $category = $stmt->get_result()->fetch_assoc();
    $category_slug = $category['slug'] ?? null;
    $has_size = intval($category['has_size'] ?? 0); 
}

// 4. Biến hiển thị
$name = $product['name'] ?? 'Tên sản phẩm không rõ';
$price = floatval($product['price'] ?? 0);
$original_price = floatval($product['original_price'] ?? $price);
$description = $product['description'] ?? '';
$image = $product['image'] ?? 'default.png';
$images_string = $product['images'] ?? $image;
$thumbnails = !empty($images_string) ? explode(',', $images_string) : [$image];
$sizes = ['S', 'M', 'L', 'XL'];

function getCategoryName($slug) {
    return $slug ?? 'Category not found';
}
$categoryName = getCategoryName($category_slug);

// 5. Sản phẩm liên quan
$related_stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE category_id = ? AND id != ? LIMIT 5");
$related_stmt->bind_param("ii", $category_id, $product_id);
$related_stmt->execute();
$related_products = $related_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $user_id = isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0 ? intval($_SESSION['user_id']) : null;

    $valid = true;
    if (strlen($name) < 3 || strlen($name) > 50) $valid = false;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $valid = false;
    if (strlen($title) < 3 || strlen($title) > 100) $valid = false;
    if (strlen($comment) < 3 || strlen($comment) > 1000) $valid = false;
    if ($rating < 1 || $rating > 5) $valid = false;

    if ($valid) {
        if ($user_id === null) {
            echo "<script>alert('Vui lòng đăng nhập để gửi đánh giá!'); window.location.href = '/shopweb/xacthuc/login.php';</script>";
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO product_reviews (product_id, user_id, customer_name, email, title, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisssis", $product_id, $user_id, $name, $email, $title, $rating, $comment);
        if ($stmt->execute()) {
            echo "<script>alert('Đánh giá của bạn đã được gửi!'); window.location.href = 'product_detail.php?id={$product_id}';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại. Lỗi: " . $conn->error . "');</script>";
        }
        $stmt->close();
        exit;
    } else {
        echo "<script>alert('Vui lòng nhập đúng định dạng các trường đánh giá.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Chi tiết sản phẩm - Streetwear Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="../css/details.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/details.js" defer></script>
</head>
<body>
<?php include 'header.php'; ?>

<nav class="breadcrumbs" aria-label="breadcrumb">
  <div class="breadcrumbs-container">
    <a href="/shopweb/index.php">Trang chủ</a>
    <a href="/shopweb/fontend/category.php?type=<?php echo urlencode($category_slug); ?>">
      <?php echo htmlspecialchars(strtoupper($categoryName)); ?>
    </a>
    <span><?php echo htmlspecialchars($name); ?></span>
  </div>
</nav>

<main>
  <div class="container">
    <section class="product-section">
      <div class="product-left">
        <div class="thumbnail-container">
          <?php foreach ($thumbnails as $index => $thumb): ?>
            <img src="../images/<?php echo htmlspecialchars($thumb); ?>"
                alt="<?php echo htmlspecialchars($name . ' thumbnail ' . ($index + 1)); ?>"
                class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                onclick="changeImage(this)">
          <?php endforeach; ?>
        </div>
        <div class="main-image-container">
          <img id="mainImage"
               src="../images/<?php echo htmlspecialchars($thumbnails[0]); ?>"
               alt="Main Image"
               class="main-image zoom-enabled">
        </div>
      </div>
      <div class="product-details">
        <h2 class="product-title"><?php echo htmlspecialchars($name); ?></h2>
        <?php
          $stars = str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating));
        ?>
        <div class="rating mb-1"><?php echo $stars . ' <span class="review-count">(' . $reviewCount . ' đánh giá)</span>'; ?></div>
        <div class="sku mb-2">SKU: <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></div>
        <div class="divider"></div>
        <div class="product-price mb-2"><?php echo number_format($price); ?>₫</div>
      
        <form method="post" action="/shopweb/fontend/cart.php" id="add-to-cart-form">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <?php if ($has_size): ?>
          <div class="size-selection mb-3">
            <div class="divider"></div>
            <?php foreach ($sizes as $size): ?>
              <button type="button" class="size-option" onclick="selectSize(this)">
                <?php echo $size; ?>
              </button>
            <?php endforeach; ?>
            <input type="hidden" name="size" id="selected-size" required>
          </div>
        <?php else: ?>
          <input type="hidden" name="size" value="free">
        <?php endif; ?>

          <div class="divider"></div>
          <div class="quantity-selector mb-3">
            <button type="button" onclick="changeQuantity(-1)">-</button>
            <div id="quantity" class="quantity-display">1</div>
            <button type="button" onclick="changeQuantity(1)">+</button>
          </div>
          <input type="hidden" name="quantity" id="quantity-input" value="1">
          <button type="submit" name="add_to_cart" class="btn-add-to-cart mt-2">Thêm vào giỏ</button>
        </form>

        <div class="product-desc mt-4">
          <h4>Mô tả</h4>
          <p><?php echo nl2br(htmlspecialchars($description)); ?></p>
        </div>
      </div>
    </section>
    <!-- Đánh giá sản phẩm -->
    <div class="product-review mt-5">
      <div class="review-summary">
        <div class="stars-block">
          <div class="stars-average">
            <?php echo str_repeat('★', round($rating)) . str_repeat('☆', 5 - round($rating)); ?>
          </div>
          <span class="review-count">(<?php echo $reviewCount; ?> đánh giá)</span>
        </div>
        <button class="btn-show-review-form" onclick="toggleReviewForm()">Viết đánh giá</button>
      </div>
    </div>
    <div id="reviewForm" style="display: none; margin-top: 20px;">
      <h3>Viết đánh giá mới</h3>
      <form method="post" action="">
        <div class="form-group">
          <label for="name">Tên</label>
          <input type="text" name="name" class="form-control" placeholder="Tên của bạn (>3 ký tự và < 50 ký tự)" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" class="form-control" placeholder="john.smith@example.com" required>
        </div>
        <div class="form-group">
          <label>Đánh giá</label>
          <div class="star-rating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
              <label for="star<?php echo $i; ?>">★</label>
            <?php endfor; ?>
          </div>
        </div>
        <div class="form-group">
          <label for="title">Tiêu đề</label>
          <input type="text" name="title" class="form-control" placeholder="Hãy cho một tiêu đề (>3 ký tự và < 50 ký tự)" required>
        </div>
        <div class="form-group">
          <label for="comment">Nội dung</label>
          <textarea name="comment" class="form-control" placeholder="Viết nội dung đánh giá ở đây (>3 ký tự và < 1000 ký tự)" required></textarea>
        </div>
        <div class="form-group form-submit">
          <button type="submit" name="submit_review">Gửi đánh giá</button>
        </div>
      </form>
    </div>
    <section class="related-products mt-5">
      <h3>SẢN PHẨM LIÊN QUAN</h3>
      <div class="row">
        <?php foreach ($related_products as $related): ?>
          <div class="product-item">
            <a href="product_detail.php?id=<?php echo $related['id']; ?>" class="text-decoration-none text-dark">
              <img src="../images/<?php echo htmlspecialchars($related['image']); ?>" alt="Related Product" class="img-fluid">
              <p><?php echo htmlspecialchars($related['name']); ?></p>
              <p class="price"><?php echo number_format($related['price']); ?>₫</p>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</main>

<?php include 'footer.php'; ?>
<script>
function toggleReviewForm() {
  const form = document.getElementById("reviewForm");
  form.style.display = form.style.display === "none" ? "block" : "none";
}
</script>
</body>
</html>