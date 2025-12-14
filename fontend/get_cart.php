<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false];
if (isset($_SESSION['cart'])) {
    $response['success'] = true;
    $response['cart'] = $_SESSION['cart'];
}
echo json_encode($response);
?>