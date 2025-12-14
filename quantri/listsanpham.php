<?php require('includes/header.php'); ?>
    <?php
    require_once('../database/dbhelper.php');
    $conn = createConnection();
    $sql_str = "SELECT * FROM products ORDER BY id";
    $result = executeResult($conn, $sql_str);
    if ($result) {
        ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Mô tả</th>
                                <th>Hình ảnh</th>
                                <th>Operation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($result as $row) {
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= number_format($row['price'], 0, ',', '.') ?> VNĐ</td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><img src="../images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" width="100"></td>
                                <td>
                                    <a class="btn btn-warning" href="editsp.php?id=<?= $row['id'] ?>">Edit</a>
                                    <a class="btn btn-danger" href="deletesp.php?id=<?= $row['id'] ?>" onclick="return confirm('Bạn chắc chắn xóa mục này?');">Delete</a>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo '<div class="alert alert-warning">Không có sản phẩm nào được tìm thấy.</div>';
    }
    $conn->close();
    ?>
</div>


<style>

.btn-warning {
    background-color: #ffc107 !important; 
    color: #212529 !important;
    border-color: #ffc107 !important; 
}

.btn-warning:hover {
    background-color: #e0a800 !important;
    border-color: #e0a800 !important;
}
</style>

<?php require('includes/footer.php'); ?>