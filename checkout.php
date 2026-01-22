<?php
// ต้องเรียกใช้ session_start() ก่อนการส่งออกใด ๆ
session_start();

// !!! เพิ่มการเชื่อมต่อฐานข้อมูล !!!
// ในหน้า checkout จริง อาจจำเป็นต้องใช้ฐานข้อมูลเพื่อดึงข้อมูลลูกค้า หรือบันทึกออร์เดอร์
require_once 'db_config.php'; 

// 1. ตรวจสอบการออกจากระบบ (Logout Logic)
if (isset($_GET['logout'])) {
    session_destroy(); 
    header('location: login.php'); 
    exit;
}

// 2. ตรวจสอบสถานะการเข้าสู่ระบบ
$is_logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$current_username = $is_logged_in ? htmlspecialchars($_SESSION["username"]) : "Guest"; 

// !!! ปิดการเชื่อมต่อฐานข้อมูลเมื่อเสร็จสิ้นการใช้งาน PHP ด้านบน !!!
if (isset($conn)) $conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดำเนินการชำระเงิน</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#4F46E5', 
                        'secondary': '#F97316', 
                        'background': '#1F2937', 
                        'card': '#374151', 
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
        }
        input[type="text"], input[type="email"], textarea, select {
            background-color: #1F2937;
            border: 1px solid #4B5563; /* Gray-600 */
            color: #F3F4F6;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        input[type="text"]:focus, input[type="email"]:focus, textarea:focus, select:focus {
            border-color: #4F46E5; /* Primary */
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.5);
            outline: none;
        }
        /* Custom scrollbar for checkout list */
        .checkout-list::-webkit-scrollbar {
            width: 8px;
        }
        .checkout-list::-webkit-scrollbar-thumb {
            background-color: #4B5563;
            border-radius: 10px;
        }
    </style>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Mitr:wght@300;400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'toy-pink': '#FFB4B4',
                        'toy-blue': '#B4E4FF',
                        'toy-yellow': '#FDF7C3',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Mitr', sans-serif; background-color: #FFFDF9; color: #4B5563; }
        .input-pastel { 
            background-color: #f9fafb; 
            border: 2px solid #f3f4f6; 
            border-radius: 1rem;
            color: #374151;
        }
        .input-pastel:focus {
            border-color: #FFB4B4;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 180, 180, 0.3);
        }
    </style>
</head>
<body class="pb-20">

    <header class="bg-white/80 backdrop-blur-md border-b-4 border-dashed border-toy-pink mb-10">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-3xl font-black">
                <span class="text-toy-pink">Yo</span><span class="text-toy-blue">Toy</span>
            </a>
            <a href="allgame.php" class="font-bold text-gray-400 hover:text-toy-pink transition">&larr; กลับไปเลือกเกม</a>
        </nav>
    </header>

    <main class="container mx-auto px-6 max-w-6xl">
        <div class="flex flex-col lg:flex-row gap-10">
            
            <div class="flex-1 space-y-6">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-black text-gray-700 mb-6 flex items-center">
                        <span class="bg-toy-blue w-8 h-8 rounded-full inline-flex items-center justify-center text-white mr-3 text-sm">1</span>
                        ข้อมูลการจัดส่ง
                    </h2>
                    <form id="checkout-form" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="name" placeholder="ชื่อ-นามสกุล" required class="input-pastel w-full px-4 py-3">
                            <input type="email" name="email" placeholder="อีเมล" required class="input-pastel w-full px-4 py-3">
                        </div>
                        <textarea name="address" placeholder="ที่อยู่จัดส่ง" rows="3" required class="input-pastel w-full px-4 py-3"></textarea>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <input type="text" name="province" placeholder="จังหวัด" required class="input-pastel px-4 py-3">
                            <input type="text" name="zipcode" placeholder="รหัสไปรษณีย์" required class="input-pastel px-4 py-3">
                            <input type="text" name="phone" placeholder="เบอร์โทรศัพท์" required class="input-pastel px-4 py-3 col-span-2 md:col-span-1">
                        </div>
                    </form>
                </div>

                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-black text-gray-700 mb-6 flex items-center">
                        <span class="bg-toy-yellow text-yellow-600 w-8 h-8 rounded-full inline-flex items-center justify-center mr-3 text-sm font-bold">2</span>
                        วิธีชำระเงิน
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label class="relative flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition has-[:checked]:border-toy-pink has-[:checked]:bg-toy-pink/5">
                            <input type="radio" name="payment-method" value="promptpay" class="w-4 h-4 text-toy-pink focus:ring-toy-pink" checked>
                            <span class="ml-3 font-bold text-gray-600">พร้อมเพย์</span>
                        </label>
                        </div>
                </div>
            </div>

            <div class="lg:w-96">
                <div class="bg-white p-8 rounded-[2rem] shadow-lg border-2 border-toy-blue sticky top-28">
                    <h2 class="text-xl font-black mb-6 border-b-2 border-dashed border-gray-100 pb-4 text-center">สรุปตะกร้าสินค้า</h2>
                    <div id="checkout-items-list" class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2">
                        </div>
                    <div class="space-y-2 pt-4 border-t-2 border-gray-50 font-bold">
                        <div class="flex justify-between text-gray-400">
                            <span>ราคารวม:</span>
                            <span id="subtotal-amount">฿0.00</span>
                        </div>
                        <div class="flex justify-between text-3xl font-black text-toy-pink pt-2">
                            <span>ยอดชำระ:</span>
                            <span id="grand-total-amount">฿0.00</span>
                        </div>
                    </div>
                    <button id="place-order-btn" type="submit" form="checkout-form" class="w-full mt-8 py-4 bg-toy-blue text-white font-black text-xl rounded-2xl shadow-md hover:bg-blue-400 transition transform hover:scale-[1.02] active:scale-95 disabled:opacity-50">
                        ยืนยันคำสั่งซื้อ ✨
                    </button>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-card border-t border-gray-700 mt-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-gray-400">
            <p>&copy; 2025 โลกแห่งเกมอันงดงาม | Checkout</p>
        </div>
    </footer>
    
    <script>
        // **************** Cart UI & Logic Variables (ใช้ Local Storage) ****************
        const cartItemsList = document.getElementById('checkout-items-list');
        const subtotalAmount = document.getElementById('subtotal-amount');
        const grandTotalAmount = document.getElementById('grand-total-amount');
        const placeOrderBtn = document.getElementById('place-order-btn');
        const checkoutForm = document.getElementById('checkout-form');
        const emptyCartMessage = document.getElementById('empty-cart-message');

        // 1. ดึงข้อมูลตะกร้าจาก Local Storage
        const getCartFromStorage = () => {
            const cartString = localStorage.getItem('game_cart');
            return cartString ? JSON.parse(cartString) : [];
        };

        // 2. ฟังก์ชัน Render รายการสินค้าและคำนวณยอดรวม
        const renderCartAndCalculate = () => {
            const cart = getCartFromStorage();
            let subTotal = 0;
            const shippingFee = 0; // สมมติว่าจัดส่งฟรี
            cartItemsList.innerHTML = '';
            
            if (cart.length === 0) {
                cartItemsList.innerHTML = '<p class="text-center text-gray-500 py-10">ตะกร้าว่างเปล่า</p>';
                placeOrderBtn.disabled = true;
                emptyCartMessage.classList.remove('hidden');
            } else {
                emptyCartMessage.classList.add('hidden');
                placeOrderBtn.disabled = false;
                
                cart.forEach(item => {
                    const price = item.price ? parseFloat(item.price) : 0.00; 
                    subTotal += price;
                    
                    const itemHtml = `
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-300 truncate pr-2">${item.title}</span>
                            <span class="text-white font-medium">฿${price.toFixed(2)}</span>
                        </div>
                    `;
                    cartItemsList.innerHTML += itemHtml;
                });
            }
            
            const grandTotal = subTotal + shippingFee;
            
            subtotalAmount.textContent = `฿${subTotal.toFixed(2)}`;
            grandTotalAmount.textContent = `฿${grandTotal.toFixed(2)}`;
        };
        
        // 3. จัดการการส่งคำสั่งซื้อ (จำลอง)
        const handlePlaceOrder = (e) => {
            e.preventDefault();
            
            if (getCartFromStorage().length === 0) {
                alert("ตะกร้าสินค้าว่างเปล่า กรุณาเพิ่มสินค้าก่อน");
                return;
            }
            
            // 1. รวบรวมข้อมูล
            const formData = new FormData(checkoutForm);
            const orderData = {};
            formData.forEach((value, key) => (orderData[key] = value));
            
            const cartItems = getCartFromStorage();
            
            // 2. แสดงผลสรุป (แทนการส่งไป Server จริง)
            console.log("Order Data:", orderData);
            console.log("Cart Items:", cartItems);
            
            const total = parseFloat(grandTotalAmount.textContent.replace('฿', ''));
            
            alert(`
                ✅ การสั่งซื้อสำเร็จ (จำลอง)!
                ยอดชำระ: ฿${total.toFixed(2)}
                วิธีการชำระ: ${orderData['payment-method']}
                ที่อยู่: ${orderData.address}, ${orderData.province} ${orderData.zipcode}
                
                *** หากมีการเชื่อมต่อฐานข้อมูลจริง โค้ดส่วนนี้จะทำการส่งข้อมูลไปยัง Server เพื่อบันทึกออร์เดอร์ ***
            `);
            
            // 3. ล้างตะกร้าสินค้าใน Local Storage และ Redirect กลับหน้าหลัก
            localStorage.removeItem('game_cart');
            window.location.href = 'index.php'; 
        };

        // **************** Event Listeners และ Initialization ****************
        document.addEventListener('DOMContentLoaded', () => {
            // โหลดรายการสินค้าในตะกร้าเมื่อโหลดหน้าเสร็จ
            renderCartAndCalculate(); 
            
            // แนบ Event Listener ให้ปุ่มยืนยันการสั่งซื้อ
            checkoutForm.addEventListener('submit', handlePlaceOrder);
        });
    </script>
</body>
</html>