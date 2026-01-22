<?php
session_start();
require_once 'db_config.php'; 

// 1. ตรรกะการ Logout
if (isset($_GET['logout'])) {
    session_destroy(); 
    header('location: login.php'); 
    exit;
}

$is_logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$current_username = $is_logged_in ? htmlspecialchars($_SESSION["username"]) : "Guest"; 

$status_message = "";
$status_type = ""; 
$name = $email = $subject = $message = '';

// 2. การประมวลผลฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(htmlspecialchars($_POST['name'] ?? ''));
    $email = trim(htmlspecialchars($_POST['email'] ?? ''));
    $subject = trim(htmlspecialchars($_POST['subject'] ?? ''));
    $message = trim(htmlspecialchars($_POST['message'] ?? ''));
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $status_message = "กรุณากรอกข้อมูลให้ครบถ้วนทุกช่องนะตัวเธอ! ✨";
        $status_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status_message = "รูปแบบอีเมลไม่ถูกต้อง ลองตรวจสอบอีกครั้งนะ";
        $status_type = "error";
    } else {
        // จำลองการส่งสำเร็จ
        $status_message = "ส่งข้อความเรียบร้อย! ทีมงานจะรีบตอบกลับให้ไวที่สุดเลย 🎈";
        $status_type = "success";
        $name = $email = $subject = $message = '';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ติดต่อเรา | StunShop ✨</title>
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
                        'pastel-dark': '#2D3436'
                    },
                    fontFamily: { sans: ['Mitr', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body { 
            background-color: #FFFDF9; 
            background-image: radial-gradient(#B4E4FF 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            transition: background-color 0.3s ease;
        }
        /* Dark Mode Override */
        body.dark-mode { 
            background-color: #1a1a1a; 
            background-image: radial-gradient(#2d3436 0.5px, transparent 0.5px);
            color: #f1f1f1;
        }
        .toy-card {
            background: white;
            border: 4px solid #F3F4F6;
            border-radius: 2rem;
            box-shadow: 10px 10px 0px #B4E4FF;
            transition: all 0.3s ease;
        }
        .dark-mode .toy-card {
            background: #2d2d2d;
            border-color: #3d3d3d;
            box-shadow: 10px 10px 0px #4a4e69;
        }
        .btn-3d {
            box-shadow: 0 6px 0px rgba(0,0,0,0.1);
        }
        .btn-3d:active {
            transform: translateY(4px);
            box-shadow: 0 2px 0px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="min-h-screen">

    <header class="sticky top-0 z-50 bg-white/80 dark:bg-black/80 backdrop-blur-md border-b-4 border-toy-blue/30">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="text-3xl font-black italic tracking-tighter">
                <span class="text-toy-pink">Yo</span><span class="text-toy-blue">toy</span>
            </a>
            
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="font-bold hover:text-toy-pink transition">หน้าแรก</a>
                <a href="allgame.php" class="font-bold hover:text-toy-pink transition">เกมทั้งหมด</a>
                <a href="contact.php" class="text-toy-pink font-black underline decoration-4 underline-offset-8">ติดต่อเรา</a>
                
                <div class="flex items-center gap-4 bg-gray-100 dark:bg-gray-800 p-2 rounded-full">
                    <button id="theme-toggle" class="p-2 hover:bg-white dark:hover:bg-gray-700 rounded-full transition shadow-sm">
                        <span id="theme-icon">🌙</span>
                    </button>
                    <?php if ($is_logged_in): ?>
                        <span class="text-xs font-bold">Hi, <?= $current_username ?>!</span>
                        <a href="?logout=1" class="text-xs bg-red-400 text-white px-3 py-1 rounded-full font-bold">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-xs bg-toy-blue text-white px-4 py-1 rounded-full font-bold">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

                                <main class="container mx-auto px-6 py-16">
                                    <div class="mb-20">
                                <div class="text-center mb-10">
                                    <h2 class="text-3xl font-black text-gray-700 dark:text-white italic">
                                        <span class="text-toy-purple">Meet</span> Our Team ✨
                                    </h2>
                                    <p class="text-gray-400 text-sm font-bold">ทีมงานใจดี พร้อมดูแลน้องๆ ทุกคนครับ</p>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                                    <div class="text-center group">
                                        <div class="toy-card p-2 bg-toy-pink border-toy-pink overflow-hidden mb-3 group-hover:scale-105 transition-transform">
                                            <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Felix&backgroundColor=ffb4b4" alt="Staff" class="w-full h-auto rounded-2xl">
                                        </div>
                                        <h4 class="font-black text-gray-700 dark:text-white text-sm">P' Stun</h4>
                                        <p class="text-[10px] font-bold text-toy-pink uppercase tracking-tighter">Founder / CEO</p>
                                    </div>
                                    <div class="text-center group">
                                        <div class="toy-card p-2 bg-toy-blue border-toy-blue overflow-hidden mb-3 group-hover:scale-105 transition-transform">
                                            <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Aria&backgroundColor=b4e4ff" alt="Staff" class="w-full h-auto rounded-2xl">
                                        </div>
                                        <h4 class="font-black text-gray-700 dark:text-white text-sm">Nong Gift</h4>
                                        <p class="text-[10px] font-bold text-toy-blue uppercase tracking-tighter">Customer Support</p>
                                    </div>

                                    <div class="text-center group">
                                        <div class="toy-card p-2 bg-toy-yellow border-toy-yellow overflow-hidden mb-3 group-hover:scale-105 transition-transform">
                                            <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Leo&backgroundColor=fdf7c3" alt="Staff" class="w-full h-auto rounded-2xl">
                                        </div>
                                        <h4 class="font-black text-gray-700 dark:text-white text-sm">P' Bank</h4>
                                        <p class="text-[10px] font-bold text-toy-yellow uppercase tracking-tighter">Game Expert</p>
                                    </div>

                                    <div class="text-center group">
                                        <div class="toy-card p-2 bg-toy-purple border-toy-purple overflow-hidden mb-3 group-hover:scale-105 transition-transform">
                                            <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Milo&backgroundColor=e5d1fa" alt="Staff" class="w-full h-auto rounded-2xl">
                                        </div>
                                        <h4 class="font-black text-gray-700 dark:text-white text-sm">Nong View</h4>
                                        <p class="text-[10px] font-bold text-toy-purple uppercase tracking-tighter">Payment Admin</p>
                                    </div>

                                    <div class="text-center group col-span-2 md:col-span-1">
                                        <div class="toy-card p-2 bg-green-200 border-green-200 overflow-hidden mb-3 group-hover:scale-105 transition-transform">
                                            <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Zoe&backgroundColor=bbf7d0" alt="Staff" class="w-full h-auto rounded-2xl">
                                        </div>
                                        <h4 class="font-black text-gray-700 dark:text-white text-sm">P' Jame</h4>
                                        <p class="text-[10px] font-bold text-green-400 uppercase tracking-tighter">Technical Support</p>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <footer class="mt-20 py-10 text-center border-t-4 border-toy-yellow/30">
        <p class="text-sm font-bold text-gray-400">&copy; 2026 STUNSHOP.TOY | MADE WITH LOVE AND MAGIC ✨</p>
    </footer>

    <script>
        // Theme Toggle Logic
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;

        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDark = body.classList.contains('dark-mode');
            themeIcon.innerText = isDark ? '☀️' : '🌙';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });

        // Check stored theme
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            themeIcon.innerText = '☀️';
        }
    </script>
</body>
</html>