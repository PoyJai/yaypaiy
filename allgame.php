<?php
session_start();
require_once 'db_config.php'; 

// 1. Logout Logic
if (isset($_GET['logout'])) {
    session_destroy();
    header('location: login.php'); 
    exit;
}

// 2. Auth Check
$is_logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$current_username = $is_logged_in ? htmlspecialchars($_SESSION["username"]) : "Guest"; 

// 3. Pagination Logic
$games_per_page = 12;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$total_games = 0;
if (isset($conn) && $conn->ping()) {
    $count_res = $conn->query("SELECT COUNT(*) AS total FROM games");
    $total_games = $count_res ? $count_res->fetch_assoc()['total'] : 0;
}

$total_pages = ceil($total_games / $games_per_page);
$offset = ($current_page - 1) * $games_per_page;

// 4. Fetch Games
$games = [];
if ($total_games > 0) {
    $sql = "SELECT id, title, description, genre, image_url, price FROM games LIMIT $games_per_page OFFSET $offset";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) { $games[] = $row; }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คลังเกมสุดน่ารัก - StunShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Mitr:wght@300;400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'toy-pink': '#FFB4B4',
                        'toy-blue': '#B4E4FF',
                        'toy-yellow': '#FDF7C3',
                        'toy-orange': '#FFDEB4',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Mitr', sans-serif; background-color: #FFFDF9; }
        .game-card { transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .game-card:hover { transform: scale(1.02); }
        /* ซ่อน Scrollbar สำหรับรายการตะกร้า */
        #cart-items-list::-webkit-scrollbar { width: 4px; }
        #cart-items-list::-webkit-scrollbar-thumb { background: #FFB4B4; border-radius: 10px; }
    </style>
</head>
<body class="text-gray-700">

    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b-4 border-dashed border-toy-orange">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-3xl font-black flex items-center space-x-2">
                <span class="bg-toy-pink text-white px-3 py-1 rounded-2xl shadow-sm rotate-[-2deg]">Yo</span>
                <span class="text-toy-blue">Toy</span>
            </a>
            <div class="flex items-center space-x-6">
                <a href="index.php" class="font-bold hover:text-toy-pink transition">หน้าแรก</a>
                <button id="open-cart-btn" class="relative bg-white border-2 border-toy-blue p-2 rounded-full hover:bg-toy-blue group transition">
                    <svg class="w-6 h-6 text-toy-blue group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span id="cart-item-count" class="absolute -top-2 -right-2 bg-toy-pink text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white">0</span>
                </button>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-black text-gray-800 mb-2">🍭 คลังเกมทั้งหมด</h1>
            <p class="text-gray-400">เลือกเกมที่ชอบ แล้วไปสนุกกันเลย!</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($games as $game): ?>
                <div class="game-card bg-white rounded-[2rem] shadow-sm border border-gray-100 p-4 relative overflow-hidden">
                    <img src="<?= htmlspecialchars($game['image_url'] ?: 'https://placehold.co/400x250') ?>" class="w-full h-40 object-cover rounded-2xl mb-4">
                    <h3 class="font-bold text-lg text-gray-700 line-clamp-1"><?= htmlspecialchars($game['title']) ?></h3>
                    <div class="text-toy-pink font-black text-xl my-2">฿<?= number_format($game['price'], 2) ?></div>
                    
                    <div class="flex space-x-2 mt-4">
                        <a href="game_detail.php?id=<?= $game['id'] ?>" class="flex-1 text-center py-2 bg-gray-50 rounded-xl text-sm font-bold hover:bg-gray-100 transition">รายละเอียด</a>
                        <button 
                            onclick="addToCart({id: '<?= $game['id'] ?>', title: '<?= addslashes($game['title']) ?>', price: '<?= $game['price'] ?>'})"
                            class="px-4 py-2 bg-toy-yellow text-yellow-700 rounded-xl font-bold hover:bg-yellow-200 transition active:scale-90 shadow-sm">
                            🛒
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="mt-16 flex justify-center space-x-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="allgame.php?page=<?= $i ?>" 
                   class="px-5 py-2 rounded-xl font-bold transition <?= $i == $current_page ? 'bg-toy-pink text-white shadow-md' : 'bg-white text-gray-400 hover:bg-toy-pink/10' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </main>

    <div id="cart-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[110] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md p-8 rounded-[2rem] shadow-2xl relative">
            <h2 class="text-2xl font-black text-center mb-6">🛒 ตะกร้าของเล่น</h2>
            <div id="cart-items-list" class="space-y-4 max-h-60 overflow-y-auto pr-2">
                </div>
            <div class="mt-8 pt-6 border-t-2 border-dashed border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <span class="font-bold text-gray-400">ราคารวม:</span>
                    <span id="cart-total-amount" class="text-3xl font-black text-toy-pink">฿0.00</span>
                </div>
                <button onclick="location.href='checkout.php'" id="checkout-btn" class="w-full py-4 bg-toy-blue text-white font-black text-xl rounded-2xl shadow-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-blue-400 transition">ชำระเงิน ✨</button>
                <button onclick="document.getElementById('cart-modal').classList.add('hidden')" class="w-full mt-2 text-gray-400 font-bold text-sm hover:text-gray-600 transition">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>

<script>
    const CART_KEY = 'game_cart';

    // ดึงข้อมูลจาก LocalStorage
    function getCart() { 
        return JSON.parse(localStorage.getItem(CART_KEY) || '[]'); 
    }

    // บันทึกข้อมูลและอัปเดตหน้าจอ
    function saveCart(cart) { 
        localStorage.setItem(CART_KEY, JSON.stringify(cart)); 
        updateUI(); 
    }

    // เพิ่มสินค้า (ปรับปรุง: ถ้ามีอยู่แล้วให้ +1 จำนวน)
    function addToCart(product) {
        let cart = getCart();
        const existingItem = cart.find(item => item.id == product.id);

        if (existingItem) {
            // ถ้ามีเกมนี้ในตะกร้าแล้ว ให้เพิ่มจำนวน (quantity)
            existingItem.quantity = (existingItem.quantity || 1) + 1;
        } else {
            // ถ้ายังไม่มี ให้เพิ่มเข้าตะกร้าโดยเริ่มที่ 1 ชิ้น
            product.quantity = 1;
            cart.push(product);
        }
        
        saveCart(cart);
        
        // แจ้งเตือนแบบน่ารักๆ
        const msg = document.createElement('div');
        msg.className = "fixed bottom-5 right-5 bg-toy-pink text-white px-6 py-3 rounded-2xl shadow-lg z-[200] animate-bounce font-bold";
        msg.innerHTML = `✨ เพิ่ม ${product.title} แล้วน้า!`;
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 2000);
    }

    // ฟังก์ชันบวกลบจำนวนใน Modal
    window.updateQty = (id, delta) => {
        let cart = getCart();
        const index = cart.findIndex(item => item.id == id);
        if (index > -1) {
            cart[index].quantity = (cart[index].quantity || 1) + delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            saveCart(cart);
        }
    };

    // ลบสินค้าออกทันที
    function removeItem(index) {
        let cart = getCart();
        cart.splice(index, 1);
        saveCart(cart);
    }

    // อัปเดตการแสดงผล UI
    function updateUI() {
        const cart = getCart();
        const list = document.getElementById('cart-items-list');
        const totalEl = document.getElementById('cart-total-amount');
        const countEl = document.getElementById('cart-item-count');
        const checkoutBtn = document.getElementById('checkout-btn');
        
        // นับจำนวนชิ้นรวมทั้งหมด
        const totalQty = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        
        if (countEl) {
            countEl.textContent = totalQty;
            countEl.style.display = totalQty > 0 ? 'flex' : 'none';
        }

        if (list) {
            let total = 0;
            list.innerHTML = cart.length ? '' : '<div class="text-center py-10 text-gray-300 font-bold">ตะกร้ายังว่างอยู่นะ... 🧸</div>';
            
            cart.forEach((item, i) => {
                const qty = item.quantity || 1;
                const price = parseFloat(item.price);
                total += price * qty;
                
                list.innerHTML += `
                    <div class="flex flex-col bg-toy-yellow/20 p-4 rounded-3xl border border-toy-yellow/50 shadow-sm mb-3">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-gray-700 truncate w-40">${item.title}</span>
                            <button onclick="removeItem(${i})" class="text-red-300 hover:text-red-500 transition">✕</button>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-toy-pink font-black text-sm">฿${price.toLocaleString()}</span>
                            
                            <div class="flex items-center gap-2 bg-white rounded-xl px-2 py-1 border border-gray-100">
                                <button onclick="updateQty('${item.id}', -1)" class="w-6 h-6 flex items-center justify-center bg-gray-50 rounded-lg font-bold hover:bg-red-50">-</button>
                                <span class="font-black text-xs w-4 text-center">${qty}</span>
                                <button onclick="updateQty('${item.id}', 1)" class="w-6 h-6 flex items-center justify-center bg-gray-50 rounded-lg font-bold hover:bg-blue-50">+</button>
                            </div>
                        </div>
                    </div>`;
            });
            
            if (totalEl) totalEl.textContent = `฿${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            if (checkoutBtn) checkoutBtn.disabled = cart.length === 0;
        }
    }

    // เปิด Modal
    document.getElementById('open-cart-btn').onclick = () => {
        updateUI();
        document.getElementById('cart-modal').classList.remove('hidden');
    };

    // ปิด Modal เมื่อคลิกข้างนอก
    window.onclick = (e) => {
        const modal = document.getElementById('cart-modal');
        if (e.target === modal) modal.classList.add('hidden');
    };

    document.addEventListener('DOMContentLoaded', updateUI);
</script>
</body>
</html>