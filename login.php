<?php
session_start();
include('server.php');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | YoToy ✨</title>
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
            background-color: #41e0fc; /* สีครีมสว่างแบบหน้า Index */
            background-image: radial-gradient(#FFB4B4 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }
        .toy-card {
            background: white;
            border: 4px solid #F3F4F6;
            border-radius: 3rem;
            box-shadow: 12px 12px 0px #B4E4FF; /* เงาสีฟ้าสไตล์การ์ตูน */
        }
        .input-pastel {
            background-color: #F9FAFB;
            border: 2px solid #F3F4F6;
            border-radius: 1.25rem;
            transition: all 0.2s ease-in-out;
        }
        .input-pastel:focus {
            border-color: #FFB4B4;
            background-color: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 180, 180, 0.3);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <a href="index.php" class="text-5xl font-black tracking-tighter italic">
                <span class="text-toy-pink">Yo</span><span class="text-toy-blue">Toy</span>
            </a>
            <div class="mt-2 flex justify-center gap-1">
                <span class="w-2 h-2 rounded-full bg-toy-pink animate-bounce"></span>
                <span class="w-2 h-2 rounded-full bg-toy-blue animate-bounce [animation-delay:-0.15s]"></span>
                <span class="w-2 h-2 rounded-full bg-toy-purple animate-bounce [animation-delay:-0.3s]"></span>
            </div>
        </div>

        <form action="login_db.php" method="post" class="toy-card p-10 relative overflow-hidden">
            <div class="absolute -top-6 -right-6 w-16 h-16 bg-toy-yellow rounded-full opacity-50"></div>
            
            <h2 class="text-3xl font-black text-gray-700 mb-8 text-center">ยินดีต้อนรับ! 👋</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-50 border-2 border-red-100 text-red-500 rounded-2xl text-center text-sm font-bold animate-pulse">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-500 mb-2 ml-2">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" required
                           class="input-pastel w-full px-5 py-4 text-gray-700 font-medium"
                           placeholder="Username">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-500 mb-2 ml-2">รหัสผ่าน</label>
                    <input type="password" name="password" required
                           class="input-pastel w-full px-5 py-4 text-gray-700 font-medium"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <a href="forgot_password.php" class="text-xs font-bold text-gray-400 hover:text-toy-pink transition-colors">
                    ลืมรหัสผ่านใช่ไหม?
                </a>
            </div>

            <button type="submit" name="login_user"
                    class="w-full mt-10 py-5 bg-toy-blue text-white font-black text-2xl rounded-2xl shadow-[0_6px_0_#93c5fd] hover:shadow-none hover:translate-y-1 active:scale-95 transition-all">
                เข้าสู่ระบบ 🚀
            </button>

            <div class="mt-10 text-center space-y-4">
                <p class="text-sm text-gray-400 font-bold tracking-wide uppercase">หรือ</p>
                <a href="register.php" class="inline-block w-full py-3 border-2 border-toy-purple text-toy-purple font-black rounded-2xl hover:bg-toy-purple hover:text-white transition-all">
                    สร้างบัญชีใหม่ ✨
                </a>
            </div>
        </form>
        
        <div class="text-center mt-10">
            <a href="index.php" class="text-gray-400 hover:text-toy-pink font-bold text-sm flex items-center justify-center gap-2 transition-colors">
                <span>&larr;</span> กลับไปยังหน้าหลัก
            </a>
        </div>
    </div>

</body>
</html>