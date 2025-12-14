<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../includes/header.php');
?>

<div>
    <div class="dashboard-header animate-fadeIn">
        <h2 class="dashboard-title"></i>Quản lý sản phẩm Bottoms</h2>
        <p class="dashboard-subtitle">Danh sách sản phẩm thuộc danh mục Bottoms</p>
    </div>

    <?php
    require_once('../../database/dbhelper.php');

    $conn = createConnection();
    if ($conn->connect_error) {
        echo '<div class="alert alert-danger alert-dismissible fade show animate-fadeIn" role="alert">';
        echo 'Kết nối thất bại: ' . htmlspecialchars($conn->connect_error);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    } else {
        $sql_str = "SELECT * FROM products WHERE category_id = 2 ORDER BY name";

        $result = executeResult($conn, $sql_str);
        if ($result === false) {
            echo '<div class="alert alert-danger alert-dismissible fade show animate-fadeIn" role="alert">';
            echo 'Lỗi truy vấn: ' . htmlspecialchars($conn->error);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        } else {
            ?>
            <div class="dashboard-section animate-fadeIn">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Danh sách sản phẩm Bottoms</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Mô tả</th>
                                        <th>Hình ảnh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($result)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="fas fa-tshirt fa-3x text-muted mb-3"></i>
                                                <p class="text-muted fs-5">Không có sản phẩm nào</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($result as $row): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><?php echo number_format($row['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                                <td>
                                                    <img src="../../images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" width="100" onerror="this.src='../../images/default.jpg';">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        $conn->close();
    }
    ?>
</div>

<?php require('../includes/footer.php'); ?>