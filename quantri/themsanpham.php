<?php require('includes/header.php'); ?>

<div class="container-fluid">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Thêm Sản Phẩm</h1>
                            </div>
                            <form action="process_add_product.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user"
                                           id="name" name="name" aria-describedby="emailHelp"
                                           placeholder="Tên sản phẩm" required>
                                </div>
                                <div class="form-group">
                                    <input type="number" class="form-control form-control-user"
                                           id="price" name="price" aria-describedby="emailHelp"
                                           placeholder="Giá thành" min="0" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user"
                                           id="description" name="description" aria-describedby="emailHelp"
                                           placeholder="Mô tả sản phẩm" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Danh mục:</label>
                                    <select class="form-control" name="category_id" required>
                                        <option value="">Chọn danh mục</option>
                                        <?php
                                        require_once('../database/dbhelper.php');
                                        $conn = createConnection();
                                        if (!$conn) {
                                            echo "<option value=''>Lỗi kết nối cơ sở dữ liệu</option>";
                                        } else {
                                            $sql_str = "SELECT * FROM categories ORDER BY name";
                                            $result = executeResult($conn, $sql_str);
                                            if ($result && count($result) > 0) {
                                                foreach ($result as $row) {
                                                    ?>
                                                    <option value="<?php echo htmlspecialchars($row['id']); ?>">
                                                        <?php echo htmlspecialchars($row['name']); ?>
                                                    </option>
                                                <?php }
                                            } else {
                                                echo "<option value=''>Không có danh mục</option>";
                                            }
                                            $conn->close();
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Hình ảnh:</label>
                                    <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Tạo mới</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<?php
require('includes/footer.php');
?>