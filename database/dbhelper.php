<?php
include_once __DIR__ . '/config.php';

function createConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function execute($conn, $sql) {
    $result = $conn->query($sql);
    if (!$result) {
        error_log("SQL Error: " . $conn->error . " in query: " . $sql);
    }
    return $result;
}

function executeResult($conn, $sql) {
    $result = $conn->query($sql);
    if (!$result) {
        error_log("SQL Error: " . $conn->error . " in query: " . $sql);
        return [];
    }
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

function executeInsert($conn, $sql) {
    $result = $conn->query($sql);
    if (!$result) {
        error_log("SQL Error: " . $conn->error . " in query: " . $sql);
        return 0;
    }
    return $conn->insert_id;
}

function executePrepared($conn, $sql, $params, $returnId = false) {
    error_log("Executing query: $sql with params: " . json_encode($params));
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare Error: " . $conn->error . " in query: " . $sql);
        return false;
    }
    if (!empty($params)) {
        $types = '';
        foreach ($params as $param) {
            $types .= is_int($param) ? 'i' : (is_float($param) ? 'd' : 's');
        }
        $stmt->bind_param($types, ...$params);
    }
    $result = $stmt->execute();
    if ($stmt->error) {
        error_log("Execute Error: " . $stmt->error . " in query: " . $sql);
        $stmt->close();
        return false;
    }
    if (stripos($sql, 'SELECT') === 0) {
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        error_log("Query result: " . json_encode($data));
        $stmt->close();
        return $data;
    }
    $insertId = $conn->insert_id;
    $stmt->close();
    return $returnId ? $insertId : true;
}

function ensureTableExists($conn, $tableName, $createSql) {
    $checkSql = "SHOW TABLES LIKE '$tableName'";
    $result = $conn->query($checkSql);
    if ($result->num_rows == 0) {
        if ($conn->query($createSql) === TRUE) {
            error_log("Table $tableName được tạo thành công.");
        } else {
            error_log("Lỗi khi tạo bảng $tableName: " . $conn->error);
        }
    }
}

function currency_format($number) {
    return number_format($number, 0, ',', '.') . 'đ';
}

$conn = createConnection();

$createUsersTable = 'CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    role ENUM("user", "admin") DEFAULT "user",
    gender VARCHAR(10) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    birthday DATE DEFAULT NULL
)';
ensureTableExists($conn, 'users', $createUsersTable);

$createProductsTable = 'CREATE TABLE IF NOT EXISTS products (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    stock_quantity INT(11) NOT NULL DEFAULT 0,
    description TEXT DEFAULT NULL,
    categogy_id INT(11) DEFAULT NULL,
    has_size TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP(),
    sold_count INT(11) DEFAULT 0,
    PRIMARY KEY (id),
    KEY fk_products_categogy_id (categogy_id),
    CONSTRAINT fk_products_categogy_id FOREIGN KEY (categogy_id) REFERENCES category (id) ON DELETE SET NULL
)';
ensureTableExists($conn, 'products', $createProductsTable);

$createOrdersTable = 'CREATE TABLE IF NOT EXISTS orders (
    id INT(11) NOT NULL AUTO_INCREMENT,
    customer_id INT(11) DEFAULT NULL,
    user_id INT(11) DEFAULT NULL,
    customer_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM("pending", "processing", "shipping", "completed", "cancelled") DEFAULT "pending",
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP(),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    KEY customer_id (customer_id),
    KEY user_id (user_id),
    CONSTRAINT fk_orders_customer_id FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE SET NULL,
    CONSTRAINT fk_orders_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
)';
ensureTableExists($conn, 'orders', $createOrdersTable);

$createOrderDetailsTable = 'CREATE TABLE IF NOT EXISTS order_details (
    id INT(11) NOT NULL AUTO_INCREMENT,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    size VARCHAR(10) NOT NULL DEFAULT "S",
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    KEY fk_order_details_order_id (order_id),
    KEY fk_order_details_product_id (product_id),
    CONSTRAINT fk_order_details_order_id FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT fk_order_details_product_id FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
)';
ensureTableExists($conn, 'order_details', $createOrderDetailsTable);

$createCategoryTable = 'CREATE TABLE IF NOT EXISTS category (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
)';
ensureTableExists($conn, 'category', $createCategoryTable);

$createProductReviewsTable = 'CREATE TABLE IF NOT EXISTS product_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    customer_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reply_status VARCHAR(20) DEFAULT "unreplied"
)';
ensureTableExists($conn, 'product_reviews', $createProductReviewsTable);

$createPasswordResetsTable = 'CREATE TABLE IF NOT EXISTS password_resets (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (email),
    INDEX idx_token (token)
)';
ensureTableExists($conn, 'password_resets', $createPasswordResetsTable);

$dropReviewsTable = 'DROP TABLE IF EXISTS reviews';
execute($conn, $dropReviewsTable);