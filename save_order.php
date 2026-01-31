<?php
session_start();
require_once 'db_config.php';

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
    exit;
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';
$address = $conn->real_escape_string($data['address'] . ' ' . $data['province'] . ' ' . $data['zipcode']);
$payment_method = $conn->real_escape_string($data['payment-method']);
$total_amount = $data['total_amount'];
$cart = $data['cart'];

// เริ่ม Transaction เพื่อป้องกันข้อมูลบันทึกไม่ครบ
$conn->begin_transaction();

try {
    // 1. บันทึกลงตาราง orders
    $stmt = $conn->prepare("INSERT INTO orders (username, total_amount, address, payment_method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $username, $total_amount, $address, $payment_method);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // 2. บันทึกลงตาราง order_items
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, game_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart as $item) {
        $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt_item->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'บันทึกคำสั่งซื้อเรียบร้อยแล้ว!']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}

$conn->close();
?>