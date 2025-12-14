<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logged_in = isset($_SESSION['user_id']);
$user_name = $logged_in ? htmlspecialchars($_SESSION['user_name']) : '';
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Shop Web</title>
  <link rel="stylesheet" href="/shopweb/css/global.css">
  <link rel="stylesheet" href="/shopweb/css/header.css">
  <link rel="stylesheet" href="/shopweb/css/account.css">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
</head>
<body>

<header id="site-header" class="main-header" role="banner">
  <div class="top-bar">
    <div class="container">
      <div class="top-bar-content text-center">
        <p class="free-ship">Miễn phí vận chuyển với đơn hàng trên 500K.</p>
      </div>
    </div>
  </div>

  <div class="header-middle">
    <div class="container">
      <div class="flexContainer-header row-flex flexAlignCenter">

        <!-- Menu mobile -->
        <div class="header-upper-menu-mobile header-action hidden-md hidden-lg">
          <div class="header-action-toggle site-handle" id="site-menu-handle">
            <span class="hamburger-menu" aria-hidden="true">
              <span class="bar"></span>
            </span>
          </div>
          <div class="header_dropdown_content site_menu_mobile">
            <span class="box-triangle"></span>
            <div class="site-nav-container-menu">
              <div class="menu-mobile-content">
                <nav id="mb-menu" class="navbar-mainmenu">
                  <ul class="menuList-sub vertical-menu-list sub-child">
                    <li><a class="parent" href="/shopweb/index.php">Home</a></li>
                    <li><a class="parent" href="/shopweb/fontend/category.php?type=new-arrivals">new arrivals</a></li>
                    <li><a class="parent" href="/shopweb/fontend/category.php?type=best-sellers">best-sellers</a></li>
                    <li><a class="parent" href="/shopweb/fontend/category.php?type=tops">tops</a></li>
                    <li><a class="parent" href="/shopweb/fontend/category.php?type=bottoms">bottoms</a></li>
                    <li><a class="parent" href="/shopweb/fontend/category.php?type=outerwear">outerwear</a></li>
                    <li><a class="parent" href="/shopweb/fontend/category.php?type=accessories">accessories</a></li>
                    <li class="main_help">
                      <div class="mobile_menu_section">
                        <p class="mobile_menu_section-title">Liên hệ với chúng tôi</p>
                        <div class="mobile_menu_help">
                          <span class="icon icon--bi-phone"></span>
                          <a href="tel:0123456789">0123 456 789</a>
                        </div>
                        <div class="mobile_menu_help">
                          <span class="icon icon--bi-email"></span>
                          <a href="mailto:your-email@example.com">your-email@example.com</a>
                        </div>
                      </div>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <!-- Logo -->
        <div class="header-upper-logo" style="margin-right: 100px;">
          <div class="wrap-logo text-center" itemscope itemtype="http://schema.org/Organization">
            <a href="/shopweb/index.php" itemprop="url" aria-label="logo">
              <img itemprop="logo" src="/shopweb/images/logo.png" alt="Streetwear Shop" loading="lazy"/>
            </a>
            <h1 style="display:none"><a href="/shopweb/index.php" itemprop="url">Streetwear Shop</a></h1>
          </div>
        </div>

        <!-- Tìm kiếm -->
        <div class="header-upper-search-top hidden-xs hidden-sm">
          <div class="header-search">
            <div class="search-box wpo-wrapper-search">
              <form id="search-form" class="searchform searchform-categoris ultimate-search" method="GET" action="/shopweb/fontend/search_results.php">
                <div class="wpo-search-inner">
                  <input type="text" required name="q" maxlength="40" class="searchinput input-search search-input" placeholder="Tìm kiếm sản phẩm..."/>
                </div>
                <button type="submit" class="btn-search" aria-label="Search">
                  <img src="/shopweb/images/search.png" alt="Search icon" class="search-icon-img" loading="lazy"/>
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Giỏ hàng -->
        <div class="header-upper-icon">
          <div class="header-wrap-icon">
            <div class="wrapper-cart header-action">
              <a class="header-action-toggle" href="/shopweb/fontend/cart.php" aria-label="Giỏ hàng" title="Giỏ hàng">
                <span class="box-icon">
                  <img src="/shopweb/images/shopping-cart.png" alt="Giỏ hàng" class="cart-icon-img" loading="lazy"/>
                  <?php if ($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                  <?php endif; ?>
                </span>
              </a>
            </div>
          </div>
        </div>

        <!-- Tài khoản -->
        <div class="header-action-toggle account-toggle" onclick="toggleAccountMenu()">
          <span class="box-icon">
            <img src="/shopweb/images/user.png" alt="Tài khoản" class="account-icon-img" loading="lazy"/>
          </span>
          <span class="icon-box-text">
            <?php echo $logged_in ? "Xin chào, $user_name" : 'Tài khoản'; ?>
          </span>
        </div>

        <?php if ($logged_in): ?>
<div id="account-menu" class="account-menu" style="display: none;">
  <a href="#" onclick="confirmLogout(event)">Đăng xuất</a>
</div>

<!-- Modal xác nhận đăng xuất -->
<div id="logout-modal" class="logout-modal" style="display: none;">
  <div class="logout-modal-content">
    <p>Bạn chắc chắn muốn đăng xuất chứ?</p>
    <div class="logout-modal-buttons">
      <button onclick="closeLogoutModal()">Quay lại</button>
      <a href="/shopweb/xacthuc/logout.php">Đăng xuất</a>
    </div>
  </div>
</div>

        <?php else: ?>
          <script>
            document.querySelector('.account-toggle').addEventListener('click', function() {
              window.location.href = "/shopweb/xacthuc/login.php";
            });
          </script>
        <?php endif; ?>

      </div>

      <!-- Menu chính -->
      <nav class="desktop-menu hidden-xs hidden-sm">
        <ul class="menu-list">
          <li><a href="/shopweb/index.php">home</a></li>
          <li><a href="/shopweb/fontend/category.php?type=new-arrivals">new arrivals</a></li>
          <li><a href="/shopweb/fontend/category.php?type=best-sellers">best sellers</a></li>
          <li><a class="parent" href="/shopweb/fontend/category.php?type=tops">tops</a></li>
          <li><a class="parent" href="/shopweb/fontend/category.php?type=bottoms">bottoms</a></li>
          <li><a class="parent" href="/shopweb/fontend/category.php?type=outerwear">outerwear</a></li>
          <li><a class="parent" href="/shopweb/fontend/category.php?type=accessories">accessories</a></li>
        </ul>
      </nav>
    </div>
  </div>
</header>


<script>
function toggleAccountMenu() {
  const menu = document.getElementById('account-menu');
  if (menu) {
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
  }
}
document.addEventListener('click', function(e) {
  const toggle = document.querySelector('.account-toggle');
  const menu = document.getElementById('account-menu');
  if (menu && !toggle.contains(e.target) && !menu.contains(e.target)) {
    menu.style.display = 'none';
  }
});

document.addEventListener('DOMContentLoaded', () => {
  setInterval(() => {
    fetch('/shopweb/fontend/get_cart_count.php')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          updateCartCountFromServer(data.cart_count);
        }
      })
      .catch(error => console.error('Error fetching cart count:', error));
  }, 5000);
});

function updateCartCountFromServer(count) {
  const cartCount = document.querySelector('.cart-count');
  if (cartCount) {
    cartCount.textContent = count;
  } else if (count > 0) {
    const cartIcon = document.querySelector('.wrapper-cart .box-icon');
    if (cartIcon) {
      cartIcon.innerHTML += `<span class="cart-count">${count}</span>`;
    }
  }
}

function confirmLogout(event) {
  event.preventDefault();
  document.getElementById('logout-modal').style.display = 'flex';
}
function closeLogoutModal() {
  document.getElementById('logout-modal').style.display = 'none';
}
</script>

</body>
</html>
