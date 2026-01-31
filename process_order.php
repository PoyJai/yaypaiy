<?php
session_start();
require_once 'db_config.php';

// ตั้งค่า Header ให้ส่งกลับเป็น JSON
header('Content-Type: application/json');

// 1. รับข้อมูล JSON จาก fetch
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลสินค้าในตะกร้า หรือข้อมูลผิดพลาด']);
    exit;
}

// 2. เริ่ม Transaction (ป้องกันข้อมูลเข้าไม่ครบ ถ้าพังจะยกเลิกทั้งหมด)
$conn->begin_transaction();

try {
    // 3. เตรียมข้อมูลบันทึกลงตาราง orders
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, email, address, province, zipcode, phone, payment_method, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Prepare failed (orders): " . $conn->error);
    }

    // แมปค่าจาก JSON ให้ตรงกับฟิลด์ฐานข้อมูล
    $customer_name = $data['name'] ?? 'Guest';
    $email = $data['email'] ?? '';
    $address = $data['address'] ?? '';
    $province = $data['province'] ?? '';
    $zipcode = $data['zipcode'] ?? '';
    $phone = $data['phone'] ?? '';
    $payment_method = $data['payment-method'] ?? 'Not Specified';
    $total_price = (float)($data['total_price'] ?? 0);

    $stmt->bind_param("sssssssd", 
        $customer_name, 
        $email, 
        $address, 
        $province, 
        $zipcode, 
        $phone, 
        $payment_method,
        $total_price
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed (orders): " . $stmt->error);
    }
    
    $order_id = $conn->insert_id; // ดึง ID ล่าสุดที่เพิ่งสร้าง

    // 4. บันทึกลงตาราง order_items (วนลูปตามสินค้าในตะกร้า)
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, game_id, quantity, price_per_unit) VALUES (?, ?, ?, ?)");
    
    if (!$stmt_item) {
        throw new Exception("Prepare failed (items): " . $conn->error);
    }

    foreach ($data['cart'] as $item) {
        $game_id = (int)$item['id'];
        $qty = (int)($item['quantity'] ?? 1);
        $price = (float)($item['price'] ?? 0);
        
        $stmt_item->bind_param("iiid", $order_id, $game_id, $qty, $price);
        
        if (!$stmt_item->execute()) {
            throw new Exception("Execute failed (items): " . $stmt_item->error);
        }
    }

    // หากสำเร็จทั้งหมด ให้ยืนยันการบันทึกลงฐานข้อมูล
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'บันทึกคำสั่งซื้อเรียบร้อยแล้ว ✨']);

} catch (Exception $e) {
    // หากเกิดข้อผิดพลาด ให้ยกเลิกคำสั่งซื้อทั้งหมดที่ทำค้างไว้
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}

$conn->close();
?>