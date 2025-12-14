<?php
session_start();
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'cart_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['cart'])) {
        $_SESSION['cart'] = $data['cart'];
        $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
        $response['success'] = true;
        $response['cart_count'] = $cart_count;
        $response['message'] = 'Cart updated successfully';
    } else {
        $response['message'] = 'No cart data provided';
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>