<?php
function updateProductStock($conn, $product_id) {
    $sql_update_stock = "UPDATE products SET stock = (SELECT COALESCE(SUM(quantity), 0) FROM inventory WHERE product_id = ?) WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update_stock);
    $stmt_update->bind_param("ii", $product_id, $product_id);
    return $stmt_update->execute();
}
?>