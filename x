<?php
session_start();
require_once 'db_config.php'; 

// --- 1. LOGIC: ดึงข้อมูลสินค้าของเล่น ---
$toy_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// สมมติฐานข้อมูลมีคอลัมน์: brand, material, scale, age_range, stock_status
$sql = "SELECT * FROM products WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $toy_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: all_toys.php");
    exit;
}

// --- 2. PREPARE DATA ---
$display = [
    'name'        => htmlspecialchars($product['name']),
    'brand'       => htmlspecialchars($product['brand']),
    'price_fmt'   => number_format($product['price'], 2),
    'image'       => $product['image_url'] ?: 'https://placehold.co/800x800?text=Toy+Image',
    'desc'        => nl2br(htmlspecialchars($product['description'])),
    'material'    => htmlspecialchars($product['material']), // เช่น PVC, ABS, Die-cast
    'scale'       => htmlspecialchars($product['scale']),    // เช่น 1/6, Non-scale
    'age'         => htmlspecialchars($product['age_range']), // เช่น 15+
    'status'      => ($product['stock'] > 0) ? 'มีสินค้าพร้อมส่ง' : 'สินค้าหมด',
    'status_color'=> ($product['stock'] > 0) ? 'text-green-400' : 'text-red-400'
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $display['name'] ?> | YoToy Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;500;700&display=swap');
        body { font-family: 'Kanit', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .toy-card { background: white; border-radius: 2rem; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.1); }
        .badge-brand { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
    </style>
</head>
<body class="bg-gray-50">

    <main class="container mx-auto px-4 py-8 md:py-16">
        
        <nav class="mb-8 text-sm text-gray-500">
            <a href="index.php" class="hover:text-red-500">หน้าแรก</a> / 
            <a href="all_toys.php" class="hover:text-red-500">ของเล่นทั้งหมด</a> / 
            <span class="text-gray-800"><?= $display['name'] ?></span>
        </nav>

        <div class="toy-card overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                
                <div class="lg:w-1/2 bg-white p-4 md:p-12 flex items-center justify-center border-b lg:border-b-0 lg:border-r border-gray-100">
                    <div class="relative group">
                        <img src="<?= $display['image'] ?>" alt="<?= $display['name'] ?>" 
                             class="max-h-[500px] object-contain transition-transform duration-500 group-hover:scale-105">
                        
                        <div class="absolute bottom-0 right-0 bg-gray-100/80 p-2 rounded-full">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2 p-8 md:p-12 flex flex-col">
                    
                    <div class="flex justify-between items-start mb-4">
                        <span class="badge-brand px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest">
                            <?= $display['brand'] ?>
                        </span>
                        <span class="text-sm font-bold <?= $display['status_color'] ?>">
                            ● <?= $display['status'] ?>
                        </span>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                        <?= $display['name'] ?>
                    </h1>

                    <div class="mb-8">
                        <span class="text-4xl font-bold text-red-500">฿<?= $display['price_fmt'] ?></span>
                        <span class="text-sm text-gray-400 ml-2">รวมภาษีมูลค่าเพิ่มแล้ว</span>
                    </div>

                    <div class="space-y-6 flex-grow">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-xs text-gray-400 uppercase font-bold">สเกล / ขนาด</p>
                                <p class="text-gray-800 font-medium"><?= $display['scale'] ?></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-xs text-gray-400 uppercase font-bold">วัสดุหลัก</p>
                                <p class="text-gray-800 font-medium"><?= $display['material'] ?></p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-gray-900 font-bold mb-2">รายละเอียดสินค้า</h3>
                            <p class="text-gray-600 leading-relaxed text-sm">
                                <?= $display['desc'] ?>
                            </p>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500">
                                <strong>เหมาะสำหรับอายุ:</strong> <?= $display['age'] ?> ปีขึ้นไป
                            </p>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4">
                        <button class="flex-grow bg-red-500 hover:bg-red-600 text-white font-bold py-4 px-8 rounded-2xl shadow-lg shadow-red-200 transition-all active:scale-95 flex justify-center items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            เพิ่มลงตะกร้า
                        </button>
                        <button class="bg-gray-900 hover:bg-black text-white p-4 rounded-2xl transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <section class="mt-16">
            <h2 class="text-2xl font-bold mb-8">สินค้าที่ใกล้เคียงกัน</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <img src="https://placehold.co/200x200" class="w-full mb-4">
                    <p class="text-xs text-gray-400 font-bold uppercase">Bandai</p>
                    <p class="font-bold text-gray-800 truncate">HG 1/144 Gundam Aerial</p>
                    <p class="text-red-500 font-bold">฿550.00</p>
                </div>
            </div>
        </section>

    </main>
