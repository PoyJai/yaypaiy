<?php 
session_start();
include('server.php'); 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก | StunShop ✨</title>
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
                    fontFamily: {
                        sans: ['Mitr', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #FFFDF9;
            background-image: radial-gradient(#E5D1FA 0.8px, transparent 0.8px);
            background-size: 24px 24px;
        }
        .toy-card {
            background: white;
            border: 4px solid #F3F4F6;
            border-radius: 3rem;
            box-shadow: 12px 12px 0px #E5D1FA; /* เงาสีม่วงพาสเทล */
        }
        .input-pastel {
            background-color: #F9FAFB;
            border: 2px solid #F3F4F6;
            border-radius: 1.25rem;
            transition: all 0.2s ease-in-out;
        }
        .input-pastel:focus {
            border-color: #E5D1FA;
            background-color: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(229, 209, 250, 0.4);
        }
    </style>
</head>
        
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="index.php" class="text-5xl font-black tracking-tighter italic">
                <span class="text-toy-pink">Yo</span><span class="text-toy-blue">Toy</span>
            </a>
            <p class="text-gray-400 mt-2 font-medium">มาเป็นครอบครัวเดียวกันนะ! 🎈</p>
        </div>

        <div class="toy-card p-8 md:p-10 relative overflow-hidden">
            <div class="absolute -bottom-6 -left-6 w-20 h-20 bg-toy-yellow rounded-full opacity-40"></div>

            <h1 class="text-2xl font-black text-gray-700 mb-8 text-center">สร้างบัญชีใหม่</h1>

            <?php if (isset($_SESSION['error_messages']) && !empty($_SESSION['error_messages'])): ?>
                <div class="mb-6 p-4 bg-red-50 border-2 border-red-100 text-red-500 rounded-2xl text-sm font-bold">
                    <ul class="list-disc list-inside">
                        <?php foreach ($_SESSION['error_messages'] as $error): ?>
                            <li><?= htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['error_messages']); ?>
            <?php endif; ?>

            <form action="register_db.php" method="post" class="space-y-5">
                
                <div>
                    <label class="block text-sm font-bold text-gray-500 mb-2 ml-2">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" required
                           class="input-pastel w-full px-5 py-3 text-gray-700 font-medium"
                           placeholder="ตั้งชื่อเท่ๆ ของคุณ"
                           value="<?= isset($_SESSION['temp_data']['username']) ? htmlspecialchars($_SESSION['temp_data']['username']) : '' ?>">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-500 mb-2 ml-2">อีเมล</label>
                    <input type="email" name="email" required
                           class="input-pastel w-full px-5 py-3 text-gray-700 font-medium"
                           placeholder="example@mail.com"
                           value="<?= isset($_SESSION['temp_data']['email']) ? htmlspecialchars($_SESSION['temp_data']['email']) : '' ?>">
                </div>
                
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-2 ml-2">รหัสผ่าน</label>
                        <input type="password" name="password" required
                               class="input-pastel w-full px-5 py-3 text-gray-700 font-medium"
                               placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-500 mb-2 ml-2">ยืนยันรหัส</label>
                        <input type="password" name="confirm-password" required
                               class="input-pastel w-full px-5 py-3 text-gray-700 font-medium"
                               placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" name="reg_user"
                        class="w-full mt-6 py-4 bg-toy-purple text-white font-black text-xl rounded-2xl shadow-[0_6px_0_#c084fc] hover:shadow-none hover:translate-y-1 active:scale-95 transition-all">
                    สมัครเลย! ✨
                </button>
            </form>

            <div class="mt-8 text-center">
                <?php unset($_SESSION['temp_data']); ?>
                <p class="text-sm text-gray-400 font-bold mb-2">มีบัญชีอยู่แล้วใช่ไหม?</p>
                <a href="login.php" class="text-toy-blue hover:text-toy-pink font-black transition-colors">
                    เข้าสู่ระบบที่นี่ &rarr;
                </a>
            </div>
        </div>
        
        <div class="text-center mt-8">
            <a href="index.php" class="text-gray-400 hover:text-toy-pink font-bold text-sm transition-colors">
                กลับไปยังหน้าหลัก
            </a>
        </div>
    </div>
</body>
</html>s