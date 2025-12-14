<div class="sidebar">
    <div class="sidebar-header">
        <h4 class="sidebar-logo">SWESHOP</h4>
    </div>
    <div class="sidebar-content">
        <ul class="sidebar-menu" id="accordionSidebar">
            <li class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <a class="nav-link" href="/shopweb/quantri/index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider" />

            <!-- Heading -->
            <div class="sidebar-heading">Chức năng chính</div>

            <!-- Nav Item - Danh mục sản phẩm -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                    <i class="fas fa-list"></i>
                    <span>Danh mục sản phẩm</span>
                </a>
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-bs-parent="#accordionSidebar">
                    <div class="collapse-inner">
                        <h6 class="collapse-header">Danh mục</h6>
                        <a class="collapse-item" href="/shopweb/quantri/products/tops.php">Tops</a>
                        <a class="collapse-item" href="/shopweb/quantri/products/bottoms.php">Bottoms</a>
                        <a class="collapse-item" href="/shopweb/quantri/products/outerwear.php">Outerwear</a>
                        <a class="collapse-item" href="/shopweb/quantri/products/accessories.php">Accessories</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Sản phẩm -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    <i class="fas fa-tshirt"></i>
                    <span>Sản phẩm</span>
                </a>
                <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-bs-parent="#accordionSidebar">
                    <div class="collapse-inner">
                        <h6 class="collapse-header">Lựa chọn</h6>
                        <a class="collapse-item" href="/shopweb/quantri/listsanpham.php">Tất Cả Sản Phẩm</a>
                        <a class="collapse-item" href="/shopweb/quantri/themsanpham.php">Thêm Sản Phẩm Mới</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Đơn hàng -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <i class="fas fa-shopping-basket"></i>
                    <span>Đơn hàng</span>
                </a>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-bs-parent="#accordionSidebar">
                    <div class="collapse-inner">
                        <h6 class="collapse-header">Quản lý đơn hàng</h6>
                        <a class="collapse-item" href="/shopweb/quantri/orders/order_list.php">Danh sách đơn hàng</a>
                        <a class="collapse-item" href="/shopweb/quantri/orders/print_invoice.php">In hóa đơn</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Quản lý người dùng -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    <i class="fas fa-users"></i>
                    <span>Quản lý người dùng</span>
                </a>
                <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-bs-parent="#accordionSidebar">
                    <div class="collapse-inner">
                        <h6 class="collapse-header">Chức năng</h6>
                        <a class="collapse-item" href="/shopweb/quantri/user_management/customer_management.php">Quản lý khách hàng</a>
                        <a class="collapse-item" href="/shopweb/quantri/user_management/admin_management.php">Phân quyền</a>
                        <a class="collapse-item" href="/shopweb/quantri/user_management/information.php">Thông tin tổng quan</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider" />

            <!-- Heading -->
            <div class="sidebar-heading">Tiện ích</div>

            <!-- Nav Item - Quản lý kho -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseInventory" aria-expanded="false" aria-controls="collapseInventory">
                    <i class="fas fa-boxes"></i>
                    <span>Quản lý kho</span>
                </a>
                <div id="collapseInventory" class="collapse" aria-labelledby="headingInventory" data-bs-parent="#accordionSidebar">
                    <div class="collapse-inner">
                        <h6 class="collapse-header">Quản lý kho</h6>
                        <a class="collapse-item" href="/shopweb/quantri/inventory/management.php">Quản lý tồn kho</a>
                        <a class="collapse-item" href="/shopweb/quantri/inventory/stock_check.php">Kiểm kho</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Đánh giá & Phản hồi -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFeedback" aria-expanded="false" aria-controls="collapseFeedback">
                    <i class="fas fa-comments"></i>
                    <span>Đánh giá & Phản hồi</span>
                </a>
                <div id="collapseFeedback" class="collapse" aria-labelledby="headingFeedback" data-bs-parent="#accordionSidebar">
                    <div class="collapse-inner">
                        <h6 class="collapse-header">Quản lý phản hồi</h6>
                        <a class="collapse-item" href="/shopweb/quantri/feedback/reviews.php">Quản lý đánh giá</a>
                        <a class="collapse-item" href="/shopweb/quantri/feedback/customer_feedback.php">Phản hồi khách hàng</a>
                    </div>
                </div>
            </li>
            <!-- Nav Item - Page -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePage" aria-expanded="false" aria-controls="collapsePage">
        <i class="fas fa-file"></i>
        <span>Page</span>
    </a>
    <div id="collapsePage" class="collapse" aria-labelledby="headingPage" data-bs-parent="#accordionSidebar">
        <div class="collapse-inner">
            <h6 class="collapse-header">Quản lý trang</h6>
            <a class="collapse-item" href="/shopweb/index.php">Trang chủ</a>
        </div>
    </div>
</li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block" />
        </ul>
    </div>
</div>