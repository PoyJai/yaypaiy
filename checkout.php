<?php
session_start();
require_once 'db_config.php'; 

$is_logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$current_username = $is_logged_in ? htmlspecialchars($_SESSION["username"]) : "Guest"; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดำเนินการชำระเงิน | YoToy ✨</title>
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
                    },
                    fontFamily: { sans: ['Mitr', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body { background-color: #FFFDF9; color: #4B5563; }
        .input-pastel { 
            background-color: #f9fafb; 
            border: 2px solid #eee; 
            border-radius: 1rem;
            transition: all 0.3s ease;
        }
        .input-pastel:focus {
            border-color: #FFB4B4;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 180, 180, 0.3);
            background-color: white;
        }
        #checkout-items-list::-webkit-scrollbar { width: 6px; }
        #checkout-items-list::-webkit-scrollbar-thumb { background: #B4E4FF; border-radius: 10px; }
    </style>
</head>
<body class="pb-20">

    <header class="bg-white/80 backdrop-blur-md border-b-4 border-dashed border-toy-pink mb-10 sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-3xl font-black">
                <span class="text-toy-pink">Yo</span><span class="text-toy-blue">Toy</span>
            </a>
            <a href="allgame.php" class="font-bold text-gray-400 hover:text-toy-pink transition flex items-center">
                <span class="mr-2">←</span> กลับไปเลือกเกม
            </a>
        </nav>
    </header>

    <main class="container mx-auto px-6 max-w-6xl">
        <div class="flex flex-col lg:flex-row gap-10">
            
            <div class="flex-1 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-black text-gray-700 mb-6 flex items-center">
                        <span class="bg-toy-blue w-10 h-10 rounded-2xl flex items-center justify-center text-white mr-4 shadow-sm">1</span>
                        ข้อมูลการจัดส่ง 🚚
                    </h2>
                    <form id="checkout-form" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="name" placeholder="ชื่อ-นามสกุล" required class="input-pastel w-full px-5 py-3">
                            <input type="email" name="email" placeholder="อีเมล" required class="input-pastel w-full px-5 py-3">
                        </div>
                        <textarea name="address" placeholder="ที่อยู่จัดส่ง" rows="3" required class="input-pastel w-full px-5 py-3"></textarea>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <input type="text" name="province" placeholder="จังหวัด" required class="input-pastel px-5 py-3">
                            <input type="text" name="zipcode" placeholder="รหัสไปรษณีย์" required class="input-pastel px-5 py-3">
                            <input type="text" name="phone" placeholder="เบอร์โทรศัพท์" required class="input-pastel px-5 py-3 col-span-2 md:col-span-1">
                        </div>
                    </form>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-black text-gray-700 mb-6 flex items-center">
                        <span class="bg-toy-yellow text-yellow-600 w-10 h-10 rounded-2xl flex items-center justify-center mr-4 shadow-sm">2</span>
                        วิธีชำระเงิน 💳
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-toy-pink has-[:checked]:bg-toy-pink/5">
                            <input type="radio" name="payment-method" value="promptpay" form="checkout-form" class="w-5 h-5 text-toy-pink" checked>
                            <span class="ml-3 font-bold text-gray-600">พร้อมเพย์</span>
                        </label>
                        <label class="relative flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-toy-pink has-[:checked]:bg-toy-pink/5">
                            <input type="radio" name="payment-method" value="cod" form="checkout-form" class="w-5 h-5 text-toy-pink">
                            <span class="ml-3 font-bold text-gray-600">เก็บเงินปลายทาง</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="lg:w-[450px]">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border-2 border-toy-blue sticky top-28">
                    <h2 class="text-2xl font-black mb-6 text-center text-gray-700">รายการสินค้า 🛒</h2>
                    
                    <div id="checkout-items-list" class="space-y-4 mb-8 max-h-[400px] overflow-y-auto pr-2">
                        </div>

                    <div class="space-y-3 pt-6 border-t-2 border-dashed border-gray-100">
                        <div class="flex justify-between font-bold text-gray-400">
                            <span>ยอดรวมราคาสินค้า:</span>
                            <span id="subtotal-amount">฿0.00</span>
                        </div>
                        <div class="flex justify-between text-3xl font-black text-toy-pink pt-4">
                            <span>รวมทั้งหมด:</span>
                            <span id="grand-total-amount">฿0.00</span>
                        </div>
                    </div>

                    <button id="place-order-btn" type="submit" form="checkout-form" class="w-full mt-10 py-5 bg-toy-blue text-white font-black text-2xl rounded-2xl shadow-lg hover:bg-blue-400 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-50 disabled:grayscale">
                        ยืนยันคำสั่งซื้อ ✨
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        const cartItemsList = document.getElementById('checkout-items-list');
        const subtotalAmount = document.getElementById('subtotal-amount');
        const grandTotalAmount = document.getElementById('grand-total-amount');
        const placeOrderBtn = document.getElementById('place-order-btn');

        // ฟังก์ชันดึงข้อมูลและบันทึกข้อมูล
        const getCart = () => JSON.parse(localStorage.getItem('game_cart')) || [];
        const saveCart = (cart) => localStorage.setItem('game_cart', JSON.stringify(cart));

        // ฟังก์ชันปรับจำนวนสินค้า
        window.updateQuantity = (id, delta) => {
            let cart = getCart();
            const itemIndex = cart.findIndex(item => item.id == id);
            
            if (itemIndex > -1) {
                // ถ้าไม่มี field quantity ให้มองว่าเป็น 1
                if (!cart[itemIndex].quantity) cart[itemIndex].quantity = 1;
                
                cart[itemIndex].quantity += delta;

                // ถ้าลดจนเหลือ 0 ให้ลบสินค้าออกจากตะกร้า
                if (cart[itemIndex].quantity <= 0) {
                    if(confirm('ต้องการลบสินค้านี้ออกจากตะกร้าใช่หรือไม่?')) {
                        cart.splice(itemIndex, 1);
                    } else {
                        cart[itemIndex].quantity = 1;
                    }
                }
                
                saveCart(cart);
                renderCheckout();
            }
        };

        // ฟังก์ชันแสดงรายการสินค้า
        const renderCheckout = () => {
            const cart = getCart();
            let total = 0;
            cartItemsList.innerHTML = '';

            if (cart.length === 0) {
                cartItemsList.innerHTML = '<div class="text-center py-10 text-gray-400 font-bold">ไม่มีสินค้าในตะกร้า 🥺</div>';
                placeOrderBtn.disabled = true;
                subtotalAmount.textContent = '฿0.00';
                grandTotalAmount.textContent = '฿0.00';
                return;
            }

            placeOrderBtn.disabled = false;
            cart.forEach(item => {
                const qty = item.quantity || 1;
                const price = parseFloat(item.price || 0);
                const itemTotal = price * qty;
                total += itemTotal;

                cartItemsList.innerHTML += `
                    <div class="flex items-center bg-gray-50 p-4 rounded-3xl border border-gray-100 shadow-sm">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-black text-gray-700 truncate text-sm">${item.title}</h4>
                            <p class="text-toy-pink font-bold text-xs">฿${price.toLocaleString()}</p>
                        </div>
                        
                        <div class="flex items-center space-x-3 ml-4 bg-white px-3 py-2 rounded-2xl border border-gray-100">
                            <button onclick="updateQuantity('${item.id}', -1)" class="w-6 h-6 flex items-center justify-center bg-gray-100 hover:bg-toy-pink hover:text-white rounded-lg transition text-gray-500 font-bold">-</button>
                            <span class="font-black text-gray-700 w-4 text-center">${qty}</span>
                            <button onclick="updateQuantity('${item.id}', 1)" class="w-6 h-6 flex items-center justify-center bg-gray-100 hover:bg-toy-blue hover:text-white rounded-lg transition text-gray-500 font-bold">+</button>
                        </div>
                    </div>
                `;
            });

            subtotalAmount.textContent = `฿${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            grandTotalAmount.textContent = `฿${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        };

        document.getElementById('checkout-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const cart = getCart();
    if (cart.length === 0) return;

    const formData = new FormData(e.target);
    const formProps = Object.fromEntries(formData.entries());
    
    // เตรียมข้อมูลที่จะส่ง
    const orderData = {
        ...formProps,
        total_price: parseFloat(grandTotalAmount.textContent.replace(/[^\d.-]/g, '')),
        cart: cart
    };

    // เปลี่ยนปุ่มเป็นสถานะกำลังส่ง
    placeOrderBtn.disabled = true;
    placeOrderBtn.innerText = "กำลังบันทึกข้อมูล... ⏳";

    try {
        const response = await fetch('process_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
        });

        const result = await response.json();

        if (result.success) {
            alert('🎉 ยืนยันการสั่งซื้อสำเร็จ!\nข้อมูลของคุณถูกบันทึกในระบบแล้ว ขอบคุณที่อุดหนุน YoToy ค่ะ!');
            localStorage.removeItem('game_cart'); // ล้างตะกร้า
            window.location.href = 'index.php';    // กลับหน้าแรก
        } else {
            alert('เกิดข้อผิดพลาด: ' + result.message);
            placeOrderBtn.disabled = false;
            placeOrderBtn.innerText = "ยืนยันคำสั่งซื้อ ✨";
        }
    } catch (error) {
        console.error('Error:', error);
        alert('ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
        placeOrderBtn.disabled = false;
    }
});

        document.addEventListener('DOMContentLoaded', renderCheckout);

function getCartFromStorage() {
    return JSON.parse(localStorage.getItem('game_cart') || '[]');
}

function addToCart(id, title, price) {
    let cart = getCartFromStorage();
    cart.push({ id, title, price });
    localStorage.setItem('game_cart', JSON.stringify(cart));
    
    // อัปเดตตัวเลขบนไอคอนตะกร้า (ถ้ามี)
    updateCartBadge();
    alert('เพิ่ม ' + title + ' ลงในตะกร้าแล้ว!');
}

function updateCartBadge() {
    const cart = getCartFromStorage();
    const badge = document.getElementById('cart-item-count');
    if (badge) {
        badge.textContent = cart.length;
        badge.style.display = cart.length > 0 ? 'flex' : 'none';
    }
}

// เรียกตอนโหลดหน้าเพื่อแสดงจำนวนสินค้าค้างไว้
document.addEventListener('DOMContentLoaded', updateCartBadge);
    </script>
</body>
</html>