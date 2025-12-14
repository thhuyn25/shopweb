<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false];
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$response['success'] = true;
$response['cart_count'] = $cart_count;

echo json_encode($response);
?>