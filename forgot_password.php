<?php
session_start();
// ตรวจสอบชื่อไฟล์เชื่อมต่อฐานข้อมูลให้ตรงกับที่คุณใช้จริง (server.php หรือ db_config.php)
include('server.php'); 

if (isset($_POST['reset_password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $pass1 = mysqli_real_escape_string($conn, $_POST['password_1']);
    $pass2 = mysqli_real_escape_string($conn, $_POST['password_2']);

    // 1. ตรวจสอบว่ากรอกข้อมูลครบไหม
    if (empty($username) || empty($pass1) || empty($pass2)) {
        $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } 
    // 2. ตรวจสอบรหัสผ่านให้ตรงกัน
    else if ($pass1 !== $pass2) {
        $_SESSION['error'] = "รหัสผ่านไม่ตรงกัน กรุณาลองใหม่";
    } 
    else {
        // 3. ตรวจสอบว่ามี User นี้ในระบบจริงไหม
        $user_check = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $user_check);
        
        if (mysqli_num_rows($result) > 0) {
            $hashed_password = password_hash($pass1, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = '$hashed_password' WHERE username = '$username'";
            
            if (mysqli_query($conn, $sql)) {
                $_SESSION['success'] = "เปลี่ยนรหัสผ่านสำเร็จ! กรุณาเข้าสู่ระบบ";
                header("location: login.php");
                exit();
            } else {
                $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
            }
        } else {
            $_SESSION['error'] = "ไม่พบชื่อผู้ใช้งานนี้ในระบบ";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กู้คืนรหัสผ่าน | STUNSHOP ✨</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Mitr:wght@300;400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'toy-pink': '#FFB4B4',
                        'toy-blue': '#B4E4FF',
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
        }
        .toy-card {
            background: white;
            border: 4px solid #F3F4F6;
            border-radius: 3rem;
            box-shadow: 12px 12px 0px #FFB4B4;
        }
        .input-pastel {
            background-color: #F9FAFB;
            border: 2px solid #F3F4F6;
            border-radius: 1.25rem;
            transition: all 0.2s ease-in-out;
        }
        .input-pastel:focus {
            border-color: #B4E4FF;
            background-color: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(180, 228, 255, 0.3);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <a href="index.php" class="text-5xl font-black tracking-tighter italic">
                <span class="text-toy-pink">STUN</span><span class="text-toy-blue">SHOP</span>
            </a>
            <p class="text-gray-400 mt-2 font-medium italic">Reset your magic key! 🗝️</p>
        </div>

        <form action="forgot_password.php" method="post" class="toy-card p-10 relative overflow-hidden">
            <h2 class="text-2xl font-black text-gray-700 mb-6 text-center">ตั้งรหัสผ่านใหม่</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-50 border-2 border-red-100 text-red-500 rounded-2xl text-center text-sm font-bold">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-gray-500 mb-2 ml-2">ชื่อผู้ใช้งาน</label>
                    <input type="text" name="username" required class="input-pastel w-full px-5 py-4 text-gray-700" placeholder="Username ของคุณ">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-500 mb-2 ml-2">รหัสผ่านใหม่</label>
                    <input type="password" name="password_1" required class="input-pastel w-full px-5 py-4 text-gray-700" placeholder="••••••••">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-500 mb-2 ml-2">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="password_2" required class="input-pastel w-full px-5 py-4 text-gray-700" placeholder="••••••••">
                </div>
            </div>

            <button type="submit" name="reset_password"
                    class="w-full mt-10 py-5 bg-toy-pink text-white font-black text-2xl rounded-2xl shadow-[0_6px_0_#ff8a8a] hover:shadow-none hover:translate-y-1 active:scale-95 transition-all">
                บันทึกข้อมูล ✨
            </button>

            <div class="mt-8 text-center">
                <a href="login.php" class="text-sm font-bold text-toy-blue hover:text-blue-400 transition-colors flex items-center justify-center gap-2">
                    &larr; กลับไปหน้าเข้าสู่ระบบ
                </a>
            </div>
        </form>
    </div>

</body>
</html>