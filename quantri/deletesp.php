<?php
$delid = $_GET['id'];
require_once('../database/dbhelper.php');
$conn = createConnection();
$sql_str = "DELETE FROM products WHERE id=$delid";
if (mysqli_query($conn, $sql_str)) {
    header("location:listsanpham.php");
} else {
    header("location:listsanpham.php");
}
$conn->close();