<?php
session_start();
require_once 'db_config.php'; 

// 1. Logout Logic
if (isset($_GET['logout'])) {
    session_destroy();
    header('location: index.php');
    exit;
}

// 2. Auth Check
$is_logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$current_username = $is_logged_in ? htmlspecialchars($_SESSION["username"]) : "Guest"; 

// 3. ดึงข้อมูลเกมเด่น
$games = [];
if (isset($conn) && $conn->ping()) {
    $sql = "SELECT id, title, genre, image_url, price FROM games LIMIT 4";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $games[] = $row;
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StunShop - อาณาจักรเกมสุดน่ารัก</title>
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
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Mitr', sans-serif;
            background-color: #FFFDF9;
            background-image: radial-gradient(#FFDEB4 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }
        .toy-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-bottom: 5px solid #B4E4FF;
        }
        .toy-card:hover {
            transform: scale(1.05) translateY(-5px);
            border-bottom: 5px solid #FFB4B4;
        }
        .floating { animation: floating 3s ease-in-out infinite; }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="text-gray-700">

    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b-4 border-dashed border-toy-orange shadow-sm">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-3xl font-black">
                <a href="index.php" class="flex items-center space-x-2">
                    <span class="bg-toy-pink text-white px-3 py-1 rounded-2xl shadow-sm rotate-[-2deg]">Yo</span>
                    <span class="text-toy-blue drop-shadow-sm">Toy</span>
                </a>
            </div>
            
            <div class="hidden md:flex space-x-8 text-lg font-semibold items-center">
                <a href="index.php" class="text-toy-pink hover:scale-110 transition">หน้าแรก</a>
                <a href="allgame.php" class="hover:text-toy-blue transition">เกมทั้งหมด</a>
                <a href="contact.php" class="hover:text-toy-orange transition">ติดต่อ</a>
        
                <button id="open-cart-btn" class="relative bg-white border-2 border-toy-blue p-2 rounded-full hover:bg-toy-blue group transition">
                    <svg class="w-6 h-6 text-toy-blue group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span id="cart-count-badge" class="absolute -top-2 -right-2 bg-toy-pink text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white">0</span>
                </button>
        
                <div id="auth-status-container">
                    <?php if ($is_logged_in): ?>
                        <div class="flex items-center space-x-3 bg-toy-pink/20 px-4 py-1 rounded-full border border-toy-pink">
                            <span class="text-sm font-bold text-gray-600">สวัสดี, <?= $current_username ?> 🧸</span>
                            <a href="index.php?logout=1" class="text-xs bg-white px-2 py-1 rounded-md shadow-sm hover:text-red-500 text-red-400 font-bold transition">ออก</a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="px-6 py-2 bg-toy-pink text-white border-b-4 border-pink-400 rounded-2xl font-bold hover:brightness-105 active:border-b-0 active:translate-y-1 transition-all">
                            เข้าสู่ระบบ 🚀
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-16">
        <div class="text-center mb-20">
            <h1 class="text-5xl md:text-7xl font-black text-gray-800 mb-6 floating">
                ยินดีต้อนรับสู่ <span class="text-toy-pink">Yo</span><span class="text-toy-blue">Toy</span>
            </h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto font-medium">
                แหล่งรวมความสนุกและเกมสุดแสนน่ารัก พร้อมให้คุณสะสมแล้ววันนี้! 🌟
            </p>
        </div>

        <div class="flex items-center justify-between mb-10 border-l-8 border-toy-pink pl-4">
            <h2 class="text-3xl font-black text-gray-800">🎮 เกมเด่นแนะนำ</h2>
            <a href="allgame.php" class="text-toy-blue font-bold hover:underline">ดูทั้งหมด ></a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <?php if (!empty($games)): ?>
                <?php foreach ($games as $game): 
                    $img = !empty($game['image_url']) ? $game['image_url'] : 'https://placehold.co/400x300/FFE9E9/FFB4B4?text=Toy+Game';
                ?>
                <div class="toy-card bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 p-3 flex flex-col">
                    <a href="game_detail.php?id=<?= $game['id'] ?>" class="flex-1">
                        <img src="<?= $img ?>" class="w-full h-48 object-cover rounded-2xl mb-4" alt="<?= $game['title'] ?>">
                        <div class="px-2 pb-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-toy-blue bg-blue-50 px-2 py-1 rounded-lg"><?= htmlspecialchars($game['genre']) ?></span>
                            <h3 class="text-lg font-black text-gray-700 mt-2 line-clamp-1"><?= htmlspecialchars($game['title']) ?></h3>
                        </div>
                    </a>
                    <div class="px-2 pb-2 flex justify-between items-center mt-3">
                        <span class="text-toy-pink font-black text-xl">฿<?= number_format($game['price'], 2) ?></span>
                        <button onclick="addToCart({id: '<?= $game['id'] ?>', title: '<?= addslashes($game['title']) ?>', price: '<?= $game['price'] ?>'})" 
                                class="p-2 bg-toy-yellow text-yellow-700 rounded-xl hover:bg-yellow-200 transition active:scale-90 shadow-sm">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 100-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"></path></svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-4 text-center py-20 bg-white rounded-3xl border-4 border-dashed border-gray-100">
                    <p class="text-gray-400 font-bold">ยังไม่มีของเล่นใหม่ๆ เข้ามาเลย รอแป๊บนะ... 🧸</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div id="cart-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[110] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md p-8 rounded-[2rem] shadow-2xl relative border-4 border-toy-blue">
            <button id="close-cart-modal-btn" class="absolute -top-4 -right-4 bg-white border-2 border-toy-blue text-toy-blue rounded-full p-2 hover:bg-toy-blue hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <h2 class="text-2xl font-black text-center mb-6 text-gray-700">🛒 ตะกร้าของคุณ</h2>
            
            <div id="cart-items-list" class="space-y-4 max-h-80 overflow-y-auto pr-2">
                </div>

            <div class="mt-8 pt-6 border-t-2 border-dashed border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <span class="font-bold text-gray-400">ยอดรวมทั้งหมด:</span>
                    <span id="cart-total-amount" class="text-3xl font-black text-toy-pink">฿0.00</span>
                </div>
                <button onclick="location.href='checkout.php'" id="checkout-btn" class="w-full py-4 bg-toy-blue text-white font-black text-xl rounded-2xl shadow-[0_4px_0_0_#8ECAE6] hover:translate-y-1 hover:shadow-none transition-all disabled:opacity-50 disabled:grayscale">
                    ไปที่หน้าชำระเงิน ✨
                </button>
            </div>
        </div>
    </div>

    <script>
    const CART_KEY = 'game_cart';
    const cartBadge = document.getElementById('cart-count-badge');
    const cartItemsList = document.getElementById('cart-items-list');
    const cartTotalAmount = document.getElementById('cart-total-amount');

    const getCart = () => JSON.parse(localStorage.getItem(CART_KEY) || '[]');
    const saveCart = (cart) => {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        updateUI();
    };

    function addToCart(product) {
        let cart = getCart();
        const existing = cart.find(item => item.id == product.id);
        if (existing) {
            existing.quantity = (existing.quantity || 1) + 1;
        } else {
            product.quantity = 1;
            cart.push(product);
        }
        saveCart(cart);
        
        // Feedback เมื่อกดปุ่ม
        const btn = event.currentTarget;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '✅';
        setTimeout(() => btn.innerHTML = originalHTML, 800);
    }

    // ฟังก์ชันปรับจำนวนสินค้า (บวก/ลบ)
    function updateQuantity(index, delta) {
        let cart = getCart();
        if (cart[index]) {
            cart[index].quantity = (cart[index].quantity || 1) + delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1); // ถ้าลดจนเหลือ 0 ให้ลบออก
            }
            saveCart(cart);
        }
    }

    function removeItem(index) {
        let cart = getCart();
        cart.splice(index, 1);
        saveCart(cart);
    }

    function updateUI() {
        const cart = getCart();
        const totalQty = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
        cartBadge.textContent = totalQty;
        cartBadge.style.display = totalQty > 0 ? 'flex' : 'none';

        if (cartItemsList) {
            let total = 0;
            cartItemsList.innerHTML = cart.length ? '' : '<div class="text-center py-10 text-gray-300 font-bold">ตะกร้าว่างเปล่า 🎈</div>';
            
            cart.forEach((item, i) => {
                const qty = item.quantity || 1;
                const price = parseFloat(item.price);
                total += price * qty;
                
                cartItemsList.innerHTML += `
                    <div class="flex justify-between items-center bg-toy-yellow/20 p-4 rounded-2xl border border-toy-yellow/50">
                        <div class="flex-1 pr-2">
                            <div class="font-bold text-gray-700 truncate w-32">${item.title}</div>
                            <div class="text-toy-pink font-black text-sm">฿${(price * qty).toLocaleString()}</div>
                        </div>
                        
                        <div class="flex items-center bg-white rounded-full border-2 border-toy-blue px-2 py-1 space-x-3">
                            <button onclick="updateQuantity(${i}, -1)" class="w-6 h-6 flex items-center justify-center bg-toy-blue/30 rounded-full font-bold hover:bg-toy-blue transition">-</button>
                            <span class="font-bold text-sm text-gray-600 min-w-[20px] text-center">${qty}</span>
                            <button onclick="updateQuantity(${i}, 1)" class="w-6 h-6 flex items-center justify-center bg-toy-blue/30 rounded-full font-bold hover:bg-toy-blue transition">+</button>
                        </div>

                        <button onclick="removeItem(${i})" class="ml-4 text-gray-300 hover:text-red-400 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>`;
            });
            cartTotalAmount.textContent = `฿${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            document.getElementById('checkout-btn').disabled = cart.length === 0;
        }
    }

    const cartModal = document.getElementById('cart-modal');
    document.getElementById('open-cart-btn').onclick = () => { updateUI(); cartModal.classList.remove('hidden'); };
    document.getElementById('close-cart-modal-btn').onclick = () => cartModal.classList.add('hidden');
    window.onclick = (e) => { if (e.target === cartModal) cartModal.classList.add('hidden'); };

    document.addEventListener('DOMContentLoaded', updateUI);
    </script>
</body>
</html>