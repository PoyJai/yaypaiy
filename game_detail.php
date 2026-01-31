<?php
session_start();
require_once 'db_config.php'; 

// --- 1. LOGIC: ดึงข้อมูลสินค้า ---
$toy_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT * FROM games WHERE id = ? LIMIT 1"; 
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $toy_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit;
}

// --- 2. PREPARE DATA ---
$display = [
    'name'        => htmlspecialchars($product['title'] ?? 'ไม่มีชื่อสินค้า'),
    'brand'       => htmlspecialchars($product['brand'] ?? 'YoToy Brand'),
    'price_fmt'   => number_format($product['price'] ?? 0, 2),
    'image'       => ($product['image_url'] ?? '') ?: 'https://placehold.co/800x800?text=Toy+Image',
    'desc'        => nl2br(htmlspecialchars($product['description'] ?? 'ไม่มีรายละเอียด')),
    'material'    => htmlspecialchars($product['material'] ?? 'Not specified'),
    'scale'       => htmlspecialchars($product['scale'] ?? 'Non-scale'),
    'age'         => htmlspecialchars($product['age_range'] ?? '3+'),
    'status'      => (isset($product['stock']) && $product['stock'] > 0) ? 'มีสินค้าพร้อมส่ง' : 'สินค้าหมด',
    'status_color'=> (isset($product['stock']) && $product['stock'] > 0) ? 'text-green-400' : 'text-red-400'
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $display['name'] ?> | YoToy Store ✨</title>
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
                        'toy-purple': '#E5D1FA',
                    },
                    fontFamily: { sans: ['Mitr', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #41e0fc;
            background-image: radial-gradient(#FFB4B4 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            min-height: 100vh;
        }
        .toy-card { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 4px solid white;
            border-radius: 3rem; 
            box-shadow: 0 20px 0 rgba(0,0,0,0.05); 
        }
        .badge-brand { background: #E5D1FA; color: #6b46c1; }
        /* สไตล์ Scrollbar สำหรับรายการตะกร้า */
        #cart-items-list::-webkit-scrollbar { width: 6px; }
        #cart-items-list::-webkit-scrollbar-thumb { background: #FFB4B4; border-radius: 10px; }
    </style>
</head>
<body class="py-10 text-gray-700">

    <main class="container mx-auto px-4">
        <nav class="mb-8 flex justify-between items-center text-sm font-bold text-white drop-shadow-md">
            <div class="flex items-center gap-2">
                <a href="allgame.php" class="hover:text-toy-yellow transition text-lg">คลังเกม</a> 
                <span>/</span>
                <span class="text-toy-yellow text-lg"><?= $display['name'] ?></span>
            </div>
            
            <button id="open-cart-btn" class="relative bg-white p-3 rounded-2xl shadow-lg hover:scale-110 transition active:scale-95 group">
                <svg class="w-8 h-8 text-toy-pink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span id="cart-item-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-black rounded-full h-6 w-6 flex items-center justify-center border-2 border-white shadow-sm">0</span>
            </button>
        </nav>

        <div class="toy-card overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <div class="lg:w-1/2 p-6 md:p-12 flex items-center justify-center border-b lg:border-b-0 lg:border-r-4 border-white/50">
                    <div class="relative group bg-white rounded-3xl p-8 shadow-inner">
                        <img src="<?= $display['image'] ?>" alt="<?= $display['name'] ?>" 
                             class="max-h-[450px] object-contain transition-transform duration-500 group-hover:scale-110">
                    </div>
                </div>

                <div class="lg:w-1/2 p-8 md:p-12 flex flex-col">
                    <div class="flex justify-between items-start mb-6">
                        <span class="badge-brand px-6 py-2 rounded-full text-sm font-black uppercase tracking-widest shadow-sm">
                            <?= $display['brand'] ?>
                        </span>
                        <span class="text-sm font-black px-4 py-2 bg-white rounded-xl <?= $display['status_color'] ?> shadow-sm">
                            ● <?= $display['status'] ?>
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-black text-gray-800 mb-6 leading-tight"><?= $display['name'] ?></h1>

                    <div class="mb-10">
                        <span class="text-5xl font-black text-toy-pink drop-shadow-sm">฿<?= $display['price_fmt'] ?></span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-8 text-center">
                        <div class="bg-toy-yellow/50 p-4 rounded-3xl border-2 border-white shadow-sm">
                            <p class="text-[10px] text-gray-500 uppercase font-black">Scale</p>
                            <p class="text-gray-700 font-bold"><?= $display['scale'] ?></p>
                        </div>
                        <div class="bg-toy-blue/50 p-4 rounded-3xl border-2 border-white shadow-sm">
                            <p class="text-[10px] text-gray-500 uppercase font-black">Material</p>
                            <p class="text-gray-700 font-bold"><?= $display['material'] ?></p>
                        </div>
                    </div>

                    <div class="mb-10 flex-grow">
                        <h3 class="text-gray-800 font-black mb-3 text-lg">📝 รายละเอียดสินค้า</h3>
                        <p class="text-gray-600 font-medium bg-white/50 p-6 rounded-3xl border-2 border-white leading-relaxed"><?= $display['desc'] ?></p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button id="add-to-cart-btn"
                                data-id="<?= $toy_id ?>" 
                                data-title="<?= $display['name'] ?>" 
                                data-price="<?= $product['price'] ?>"
                                class="flex-grow bg-toy-pink text-white font-black py-5 px-8 rounded-3xl shadow-[0_8px_0_#ff8a8a] hover:shadow-none hover:translate-y-1 active:scale-95 transition-all flex justify-center items-center gap-3 text-xl">
                            เพิ่มลงตะกร้า ✨
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="cart-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div class="bg-white rounded-[3rem] w-full max-w-md overflow-hidden shadow-2xl border-4 border-white">
            <div class="p-8 border-b-4 border-toy-yellow/30 flex justify-between items-center bg-toy-yellow/10">
                <h3 class="text-2xl font-black text-gray-800 flex items-center gap-3">
                    <span class="text-3xl animate-bounce">🛒</span> ตะกร้าของคุณ
                </h3>
                <button id="close-cart-modal-btn" class="text-gray-400 hover:text-red-400 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="cart-items-list" class="p-8 max-h-[350px] overflow-y-auto space-y-4">
                </div>

            <div class="p-8 bg-gray-50 border-t-4 border-toy-yellow/30">
                <div class="flex justify-between items-center mb-8">
                    <span class="text-gray-500 font-black uppercase tracking-widest text-sm">ยอดรวมทั้งหมด</span>
                    <span id="cart-total-amount" class="text-4xl font-black text-toy-pink">฿0.00</span>
                </div>
                <button id="checkout-btn" class="w-full py-5 bg-toy-blue hover:bg-blue-400 text-white font-black text-xl rounded-2xl shadow-md transition-all active:scale-95 disabled:opacity-50">
                    ไปชำระเงิน ✨
                </button>
            </div>
        </div>
    </div>

<script>
    const CART_KEY = 'game_cart';
    const cartModal = document.getElementById('cart-modal');
    const cartItemCount = document.getElementById('cart-item-count');
    const cartItemsList = document.getElementById('cart-items-list');
    const cartTotalAmount = document.getElementById('cart-total-amount');
    const checkoutBtn = document.getElementById('checkout-btn');
    const addToCartBtn = document.getElementById('add-to-cart-btn');

    // ฟังก์ชันจัดการ Storage
    const getCart = () => JSON.parse(localStorage.getItem(CART_KEY)) || [];
    const saveCart = (cart) => {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        updateUI();
    };

    // ปรับจำนวนสินค้า (บวกลบภายใน Modal ตะกร้า)
    window.updateQtyInModal = (id, delta) => {
        let cart = getCart();
        const itemIndex = cart.findIndex(item => item.id == id);
        if (itemIndex > -1) {
            cart[itemIndex].quantity = (cart[itemIndex].quantity || 1) + delta;
            if (cart[itemIndex].quantity <= 0) {
                cart.splice(itemIndex, 1); // ถ้าลดเหลือ 0 ให้ลบออก
            }
            saveCart(cart);
        }
    };

    // ลบสินค้าออกทันที
    window.removeItem = (index) => {
        let cart = getCart();
        cart.splice(index, 1);
        saveCart(cart);
    };

    // อัปเดต UI ของตะกร้า
    const updateUI = () => {
        const cart = getCart();
        let total = 0;
        let totalQty = 0;
        
        cartItemsList.innerHTML = cart.length ? '' : '<div class="text-center py-10 text-gray-400 font-bold">ตะกร้าว่างเปล่าเยย~ 🧸</div>';
        checkoutBtn.disabled = cart.length === 0;
        
        cart.forEach((item, index) => {
            const qty = item.quantity || 1;
            const price = parseFloat(item.price || 0);
            total += price * qty;
            totalQty += qty;

            cartItemsList.innerHTML += `
                <div class="flex justify-between items-center bg-white p-4 rounded-3xl border-2 border-gray-50 shadow-sm">
                    <div class="flex flex-col flex-1">
                        <span class="text-gray-800 font-black truncate max-w-[140px]">${item.title}</span>
                        <span class="text-toy-pink font-bold text-sm">฿${price.toLocaleString()}</span>
                    </div>
                    
                    <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-2 py-1 mx-2">
                        <button onclick="updateQtyInModal('${item.id}', -1)" class="w-6 h-6 bg-white rounded-lg shadow-sm font-bold hover:bg-red-100">-</button>
                        <span class="font-black text-xs w-4 text-center">${qty}</span>
                        <button onclick="updateQtyInModal('${item.id}', 1)" class="w-6 h-6 bg-white rounded-lg shadow-sm font-bold hover:bg-blue-100">+</button>
                    </div>

                    <button onclick="removeItem(${index})" class="text-red-300 hover:text-red-500 transition p-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>`;
        });

        cartItemCount.textContent = totalQty;
        cartItemCount.style.display = totalQty > 0 ? 'flex' : 'none';
        cartTotalAmount.textContent = `฿${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
    };

    // LOGIC: เมื่อกดปุ่ม "เพิ่มลงตะกร้า"
    addToCartBtn.addEventListener('click', () => {
        let cart = getCart();
        const productId = addToCartBtn.dataset.id;
        
        // ค้นหาว่ามีสินค้าชิ้นนี้ในตะกร้าหรือยัง
        const existingItem = cart.find(item => item.id == productId);

        if (existingItem) {
            // ถ้ามีแล้ว ให้บวกจำนวนเพิ่ม
            existingItem.quantity = (existingItem.quantity || 1) + 1;
        } else {
            // ถ้ายังไม่มี ให้เพิ่มชิ้นใหม่เข้าไป
            cart.push({
                id: productId,
                title: addToCartBtn.dataset.title,
                price: addToCartBtn.dataset.price,
                quantity: 1
            });
        }
        
        saveCart(cart);
        
        // เอฟเฟกต์แจ้งเตือนแบบน่ารัก
        const originalText = addToCartBtn.innerHTML;
        addToCartBtn.innerHTML = 'สำเร็จ! ✨';
        addToCartBtn.classList.replace('bg-toy-pink', 'bg-green-400');
        
        setTimeout(() => {
            addToCartBtn.innerHTML = originalText;
            addToCartBtn.classList.replace('bg-green-400', 'bg-toy-pink');
            cartModal.classList.remove('hidden'); // เปิดตะกร้าโชว์
        }, 800);
    });

    document.getElementById('open-cart-btn').onclick = () => {
        updateUI();
        cartModal.classList.remove('hidden');
    };

    document.getElementById('close-cart-modal-btn').onclick = () => cartModal.classList.add('hidden');
    checkoutBtn.onclick = () => window.location.href = 'checkout.php';
    window.onclick = (e) => { if (e.target === cartModal) cartModal.classList.add('hidden'); };

    document.addEventListener('DOMContentLoaded', updateUI);
</script>
</body>
</html>