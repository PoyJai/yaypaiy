<?php
// ต้องเรียกใช้ session_start() ก่อนการส่งออกใด ๆ
session_start();

// !!! เพิ่มการเชื่อมต่อฐานข้อมูล !!!
require_once 'db_config.php'; 

// 1. ตรวจสอบการออกจากระบบ (Logout Logic)
if (isset($_GET['logout'])) {
    session_destroy(); // ทำลาย Session ทั้งหมด
    header('location: login.php'); 
    exit; 
}

// กำหนดตัวแปรสำหรับแสดงผลใน HTML
$is_logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$current_username = $is_logged_in ? htmlspecialchars($_SESSION["username"]) : "Guest"; 

// --- *** LOGIC สำหรับดึงข้อมูลเกม *** ---

// 1. รับ ID ของเกมจาก URL
$game_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. ตรวจสอบ ID
if ($game_id === 0) {
    // ถ้าไม่มี ID ส่งมา ให้แจ้งเตือนและ Redirect กลับไปหน้าเกมทั้งหมด
    $_SESSION["error"] = "ไม่พบ ID เกมที่ต้องการดู!";
    header("Location: allgame.php");
    exit;
}

// 3. เตรียมคำสั่ง SQL เพื่อดึงข้อมูลเกมทั้งหมด
// *** เพิ่มคอลัมน์ใหม่ๆ ที่ต้องการแสดงในหน้ารายละเอียด เช่น price, release_date, developer, long_description ***
$sql = "SELECT id, title, description, long_description, genre, image_url, price, release_date, developer, rating FROM games WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    // ถ้า prepare ล้มเหลว อาจเกิดจากปัญหาการเชื่อมต่อหรือ syntax SQL
    // ควรใช้ error_log สำหรับ production แต่ใช้ die สำหรับการ debug
    die("Prepare failed: " . $conn->error);
    
}

// 4. ผูกตัวแปรและรันคำสั่ง
$stmt->bind_param("i", $game_id); // 'i' หมายถึง integer
$stmt->execute();
$result = $stmt->get_result();

$game = null;
if ($result->num_rows === 1) {
    // ดึงข้อมูลเกม
    $game = $result->fetch_assoc();
}

// 5. จัดการกรณีไม่พบเกม
if (!$game) {
    // ใช้ die แทน redirect เพื่อดูปัญหาทันทีระหว่างพัฒนา แต่ใช้ redirect ใน production
    // die("ไม่พบเกมด้วย ID: " . $game_id); 
    $_SESSION["error"] = "ไม่พบเกมด้วย ID: " . $game_id;
    header("Location: allgame.php");
    exit;
}

// 6. ปิด statement และการเชื่อมต่อ
$stmt->close();
$conn->close();

// กำหนดตัวแปรสำหรับ HTML และทำความสะอาดข้อมูล
$game_id_html = htmlspecialchars($game['id']); // ต้องเก็บ ID จริงเพื่อใช้ใน JS
$game_title = htmlspecialchars($game['title']);
$game_short_desc = htmlspecialchars($game['description']);
$game_long_desc = nl2br(htmlspecialchars($game['long_description']));
$game_genre = htmlspecialchars($game['genre']);
$game_image = empty($game['image_url']) ? 'https://placehold.co/1200x600/374151/ffffff?text=Game+Image+Not+Available' : htmlspecialchars($game['image_url']);
$game_price_float = (float)$game['price']; // ราคาแบบ float
$game_price = number_format($game_price_float, 2); // ราคาแบบแสดงผล
$game_release = date('d F Y', strtotime($game['release_date']));
$game_developer = htmlspecialchars($game['developer']);
$game_rating = (float)$game['rating'];

// Class สำหรับ Badge ประเภทเกม (เหมือนใน allgame.php)
$genre_class = 'bg-primary/20 text-primary'; 
if (strpos($game_genre, 'Survival') !== false) $genre_class = 'bg-secondary/20 text-secondary';
if (strpos($game_genre, 'Adventure') !== false) $genre_class = 'bg-green-500/20 text-green-500';
if (strpos($game_genre, 'Racing') !== false) $genre_class = 'bg-yellow-500/20 text-yellow-500';
if (strpos($game_genre, 'Strategy') !== false) $genre_class = 'bg-amber-600/20 text-amber-600';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดเกม: <?= $game_title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // ... (โค้ด Tailwind Config และ Style เดิมจาก allgame.php) ...
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#4F46E5', // Indigo-600
                        'secondary': '#F97316', // Orange-600
                        'background': '#1F2937', // Gray-800
                        'card': '#374151', // Gray-700
                    },
                    fontFamily: {
                        sans: ['Inter', 'Tahoma', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #1F2937;
            color: #F3F4F6;
            background-image: url('https://m.media-amazon.com/images/S/pv-target-images/6fb04fc002b005a28a0d2b2bc1a1e9ca06c9dd05a7e5d006033776c05a44d706.jpg');
            background-size: cover;
            background-position: center;
        }
        .rating-star {
            color: #FBBF24; /* Amber-400 */
        }
    </style>
    
</head>
<body>

    <header class="sticky top-0 z-50 bg-background/90 backdrop-blur-sm shadow-lg">
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-primary">
                <a href="index.php">
                Yo<span class="text-secondary">Toy</span>
                </a>
            </div>
            <div class="hidden md:flex space-x-8 text-lg font-medium items-center">
                <a href="index.php" class="hover:text-primary transition duration-150">หน้าแรก</a>
                <a href="allgame.php" class="hover:text-primary transition duration-150">เกมทั้งหมด</a>
                <a href="contact.php" class="hover:text-primary transition duration-150">ติดต่อ</a>
                <button id="open-cart-btn" class="relative text-gray-300 hover:text-secondary p-2 transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span id="cart-item-count" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-secondary rounded-full">0</span>
                </button>

                <div id="auth-status-container">
                    <?php if ($is_logged_in): ?>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-white/80 hidden lg:block">สวัสดี, <?= $current_username ?></span>
                            <a href="allgame.php?logout=1" class="px-4 py-2 bg-gray-600 rounded-full text-white font-semibold hover:bg-gray-700 transition duration-300">
                                ออกจากระบบ
                            </a>
                        </div>
                    <?php else: ?>
                        <button id="auth-button-desktop" class="px-4 py-2 bg-secondary rounded-full text-white font-semibold hover:bg-orange-700 transition duration-300" onclick="window.location.href='login.php'">
                            เข้าสู่ระบบ / สมัคร
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <button id="menu-button" class="md:hidden focus:outline-none p-2 rounded-lg hover:bg-card">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden bg-card/95 py-2">
            <a href="index.php" class="block px-4 py-2 text-sm hover:bg-gray-600 transition duration-150">หน้าแรก</a>
            <a href="allgame.php" class="block px-4 py-2 text-sm text-primary font-bold hover:bg-gray-600 transition duration-150">เกมทั้งหมด</a>
            <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-600 transition duration-150">บทความ</a>
            <div id="auth-mobile-status" class="px-4 py-2">
                <?php if ($is_logged_in): ?>
                    <div class="text-sm font-medium text-white/80 mb-2 text-center">สวัสดี, <?= $current_username ?></div>
                    <a href="allgame.php?logout=1" class="w-full block text-center px-4 py-2 bg-gray-600 rounded-full text-white font-semibold hover:bg-gray-700 transition duration-300">
                        ออกจากระบบ
                    </a>
                <?php else: ?>
                    <button id="auth-button-mobile" class="w-full px-4 py-2 bg-secondary rounded-full text-white font-semibold hover:bg-orange-700 transition duration-300" onclick="window.location.href='login.php'">
                        เข้าสู่ระบบ / สมัคร
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        
        <div class="max-w-6xl mx-auto bg-card rounded-2xl shadow-3xl overflow-hidden">
            <div class="relative">
                <img src="<?= $game_image ?>" alt="ปกเกม: <?= $game_title ?>" class="w-full h-[300px] md:h-[500px] object-cover object-center rounded-t-2xl">
                <div class="absolute inset-0 bg-black/50 flex items-end p-8 md:p-12">
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight">
                        <?= $game_title ?>
                    </h1>
                </div>
            </div>

            <div class="p-8 md:p-12 lg:flex lg:space-x-12">
                
                <div class="lg:w-2/3">
                    <div class="mb-8">
                        <span class="inline-block <?= $genre_class ?> text-sm font-semibold px-4 py-1 rounded-full mb-4">
                            <?= $game_genre ?>
                        </span>
                        
                        <p class="text-xl text-gray-300 mb-6">
                            <?= $game_short_desc ?>
                        </p>

                        <h2 class="text-2xl font-bold text-primary mb-4 border-b border-gray-700 pb-2">เกี่ยวกับเกมนี้</h2>
                        <div class="text-gray-400 leading-relaxed space-y-4">
                            <p>
                                <?= $game_long_desc ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-700">
                        <h3 class="text-2xl font-bold text-secondary mb-4">คะแนนรีวิว</h3>
                        <div class="flex items-center space-x-4">
                            <span class="text-5xl font-extrabold text-white"><?= number_format($game_rating, 1) ?></span>
                            <div>
                                <div class="flex text-amber-400">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <svg class="w-6 h-6 <?= ($i <= floor($game_rating)) ? 'fill-current' : 'text-gray-600' ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    <?php endfor; ?>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">อ้างอิงจากรีวิวผู้เล่น (สมมติ)</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="lg:w-1/3 mt-10 lg:mt-0">
                    <div class="bg-gray-700 p-6 rounded-xl shadow-lg border border-primary/50 sticky top-28">
                        <div class="text-4xl font-extrabold text-white mb-4 flex justify-between items-center">
                            <span>ราคา:</span> 
                            <span class="text-secondary">฿<?= $game_price ?></span>
                        </div>
                        
                        <button id="add-to-cart-btn" 
                                data-id="<?= $game_id_html ?>" 
                                data-title="<?= $game_title ?>"
                                data-price="<?= $game_price_float ?>"
                                class="w-full px-6 py-3 bg-primary rounded-lg text-white font-bold text-lg hover:bg-indigo-700 transition duration-300 shadow-xl">
                            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            เพิ่มลงในตะกร้า
                        </button>
                        
                        <div class="mt-6 space-y-3 text-sm border-t border-gray-600 pt-4">
                            <h3 class="text-lg font-bold text-white mb-3">ข้อมูลจำเพาะ</h3>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-400">นักพัฒนา:</span>
                                <span class="text-white font-medium"><?= $game_developer ?></span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-400">วันวางจำหน่าย:</span>
                                <span class="text-white font-medium"><?= $game_release ?></span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-400">ประเภท:</span>
                                <span class="text-white font-medium"><?= $game_genre ?></span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-400">รหัสเกม (ID):</span>
                                <span class="text-white font-mono"><?= $game_id_html ?></span>
                            </div>
                            
                            </div>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <footer class="bg-card border-t border-gray-700 mt-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-gray-400">
            <div class="flex flex-col md:flex-row justify-center space-y-2 md:space-y-0 md:space-x-8 mb-4">
                <a href="#" class="hover:text-primary transition duration-150">นโยบายความเป็นส่วนตัว</a>
                <a href="#" class="hover:text-primary transition duration-150">ข้อกำหนดการใช้งาน</a>
            </div>
            <p>&copy; 2025 โลกแห่งเกมอันงดงาม (AESTHETIC.GAMES) | สงวนลิขสิทธิ์</p>
        </div>
    </footer>
    
    <div id="cart-modal" class="fixed inset-0 bg-black bg-opacity-80 z-[110] hidden flex items-center justify-center p-4">
        <div class="bg-card w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 rounded-xl shadow-2xl relative border border-secondary/50">
            <button id="close-cart-modal-btn" class="absolute top-4 right-4 text-gray-400 hover:text-white transition duration-150">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h2 class="text-3xl font-bold text-secondary mb-6 text-center">🛒 รายการสั่งซื้อ (Cart)</h2>
            
            <div id="cart-items-list" class="space-y-4 min-h-[100px]">
                <p class="text-center text-gray-500 py-10">ตะกร้าว่างเปล่า</p>
            </div>

            <div id="cart-summary" class="mt-8 pt-4 border-t border-gray-700">
                <div class="flex justify-between items-center text-xl font-bold mb-4">
                    <span class="text-white">ราคารวม:</span>
                    <span id="cart-total-amount" class="text-secondary">฿0.00</span>
                </div>
                <button id="checkout-btn" class="w-full px-4 py-3 bg-primary rounded-lg text-white font-bold hover:bg-indigo-700 transition duration-300 disabled:opacity-50" disabled>
                    ดำเนินการชำระเงิน
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // **************** Cart UI & Logic Variables (คัดลอกมาจาก allgame.php) ****************
        const cartModal = document.getElementById('cart-modal');
        const openCartBtn = document.getElementById('open-cart-btn');
        const closeCartModalBtn = document.getElementById('close-cart-modal-btn');
        const cartItemCount = document.getElementById('cart-item-count');
        const cartItemsList = document.getElementById('cart-items-list');
        const cartTotalAmount = document.getElementById('cart-total-amount');
        const checkoutBtn = document.getElementById('checkout-btn');
        const addToCartBtn = document.getElementById('add-to-cart-btn'); // ปุ่มใหม่

        // 1. ฟังก์ชัน Render Cart
        // ใช้ Local Storage เป็นตะกร้าจำลอง
        const renderCart = (cart) => {
            let total = 0;
            cartItemsList.innerHTML = '';

            if (cart.length === 0) {
                cartItemsList.innerHTML = '<p class="text-center text-gray-500 py-10">ตะกร้าว่างเปล่า</p>';
                checkoutBtn.disabled = true;
            } else {
                checkoutBtn.disabled = false;
                cart.forEach(item => {
                    const price = item.price ? parseFloat(item.price) : 0.00; 
                    total += price;
                    
                    const itemHtml = `
                        <div class="flex justify-between items-center bg-gray-700 p-3 rounded-lg border border-gray-600">
                            <span class="text-white font-medium">${item.title}</span>
                            <div class="flex items-center space-x-3">
                                <span class="text-secondary font-bold">฿${price.toFixed(2)}</span>
                                <button data-id="${item.id}" class="remove-from-cart-btn text-red-400 hover:text-red-500 transition duration-150" title="ลบออกจากตะกร้า">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    `;
                    cartItemsList.innerHTML += itemHtml;
                });
            }
            
            // อัปเดตจำนวนสินค้าบนไอคอน
            cartItemCount.textContent = cart.length > 99 ? '99+' : cart.length.toString(); 
            cartTotalAmount.textContent = `฿${total.toFixed(2)}`;
            attachRemoveListeners(cart); 
        };

        // 2. ดึง/อัปเดตสถานะตะกร้าจาก Local Storage (แทนการใช้ Server Side Logic)
        const getCartFromStorage = () => {
            const cartString = localStorage.getItem('game_cart');
            return cartString ? JSON.parse(cartString) : [];
        };

        const saveCartToStorage = (cart) => {
            localStorage.setItem('game_cart', JSON.stringify(cart));
        };
        
        // 3. ฟังก์ชันเพิ่มสินค้าลงตะกร้า (Local Storage)
        const handleAddToCart = async (e) => {
            e.preventDefault();
            
            const gameId = addToCartBtn.dataset.id;
            const gameTitle = addToCartBtn.dataset.title;
            const gamePrice = addToCartBtn.dataset.price;
            
            const newItem = {
                id: gameId,
                title: gameTitle,
                price: gamePrice
            };
            
            let cart = getCartFromStorage();

            // ตรวจสอบว่ามีสินค้านี้ในตะกร้าแล้วหรือไม่ (ป้องกันการเพิ่มซ้ำ)
            const exists = cart.some(item => item.id === gameId);

            if (!exists) {
                cart.push(newItem);
                saveCartToStorage(cart);
                
                // อัพเดท UI ทันที
                renderCart(cart); 
                
                // แจ้งเตือนผู้ใช้และแสดง Modal
                alert(`"${gameTitle}" ถูกเพิ่มลงในตะกร้าแล้ว!`);
                if(cartModal) cartModal.classList.remove('hidden');
            } else {
                alert(`"${gameTitle}" มีอยู่ในตะกร้าแล้ว!`);
                if(cartModal) cartModal.classList.remove('hidden');
            }
        };

        // 4. จัดการการลบสินค้า (Local Storage)
        const handleRemove = (e) => {
            e.preventDefault();
            e.stopPropagation(); // หยุดไม่ให้ event ไปถึงองค์ประกอบแม่
            const removeBtn = e.currentTarget;
            const gameId = removeBtn.dataset.id;
            
            if (confirm('คุณต้องการลบสินค้านี้ออกจากตะกร้าใช่หรือไม่?')) {
                let cart = getCartFromStorage();
                cart = cart.filter(item => item.id !== gameId);
                saveCartToStorage(cart);
                renderCart(cart); 
            }
        };

        // 5. แนบ Event Listener ให้ปุ่มลบ (ต้องเรียกซ้ำทุกครั้งที่ renderCart)
        const attachRemoveListeners = (cart) => {
            document.querySelectorAll('.remove-from-cart-btn').forEach(button => {
                button.removeEventListener('click', handleRemove); 
                button.addEventListener('click', handleRemove);
            });
        };
            
        // **************** Event Listeners และ Initialization ****************
        document.addEventListener('DOMContentLoaded', () => {

            const menuButton = document.getElementById('menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            menuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
            
            // Event: เปิด Modal ตะกร้า
            if (openCartBtn) {
                openCartBtn.addEventListener('click', () => {
                    renderCart(getCartFromStorage()); // อัพเดทก่อนเปิด Modal เสมอ
                    if(cartModal) cartModal.classList.remove('hidden');
                });
            }
            
            // Event: ปิด Modal ตะกร้า
            if (closeCartModalBtn) {
                closeCartModalBtn.addEventListener('click', () => {
                    if(cartModal) cartModal.classList.add('hidden');
                });
            }
            
            // Event: ปิด Modal ตะกร้า เมื่อคลิกนอกกรอบ
            if (cartModal) {
                cartModal.addEventListener('click', (e) => {
                    if (e.target === cartModal) {
                        cartModal.classList.add('hidden');
                    }
                });
            }

            // Event สำหรับปุ่ม Checkout (ยังไม่มี Logic จริง)
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', () => {
                    alert('อยู่ระหว่างการพัฒนา: หน้ารายละเอียดการชำระเงิน');
                });
            }
            
            // *** Event สำหรับปุ่มเพิ่มลงในตะกร้า ***
            if (addToCartBtn) {
                addToCartBtn.addEventListener('click', handleAddToCart);
            }


            // *** สำคัญ: เรียกใช้ฟังก์ชันนี้เมื่อโหลดหน้าเสร็จเพื่อแสดงจำนวนสินค้าตั้งแต่แรก ***
            renderCart(getCartFromStorage());
            // Event สำหรับปุ่ม Checkout
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', () => {
                const cart = getCartFromStorage(); // ตรวจสอบตะกร้าอีกครั้งก่อนไป
                if (cart.length > 0) {
                    window.location.href = 'checkout.php'; // <--- เปลี่ยนให้ Redirect ไปหน้า checkout
                } else {
                    alert('ตะกร้าสินค้าว่างเปล่า ไม่สามารถดำเนินการชำระเงินได้!');
                }
            });
        }
        });
    </script>
    </body>
</html>