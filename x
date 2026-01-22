<?php
// --- 1. SETTINGS & SESSION ---
session_start();
require_once 'db_config.php'; 

// --- 2. LOGIC: AUTHENTICATION ---
if (isset($_GET['logout'])) {
    session_destroy();
    header('location: login.php'); 
    exit; 
}

$is_logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$current_username = $is_logged_in ? htmlspecialchars($_SESSION["username"]) : "Guest"; 

// --- 3. LOGIC: DATA FETCHING ---
$game_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($game_id === 0) {
    $_SESSION["error"] = "ไม่พบ ID เกมที่ต้องการดู!";
    header("Location: allgame.php");
    exit;
}

$sql = "SELECT * FROM games WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $game_id);
$stmt->execute();
$game = $stmt->get_result()->fetch_assoc();

if (!$game) {
    $_SESSION["error"] = "ไม่พบข้อมูลเกม";
    header("Location: allgame.php");
    exit;
}

// --- 4. PREPARE DISPLAY DATA ---
$display = [
    'title'       => htmlspecialchars($game['title']),
    'short_desc'  => htmlspecialchars($game['description']),
    'long_desc'   => nl2br(htmlspecialchars($game['long_description'])),
    'genre'       => htmlspecialchars($game['genre']),
    'image'       => !empty($game['image_url']) ? $game['image_url'] : 'https://placehold.co/1200x600/374151/ffffff?text=No+Image',
    'price_raw'   => (float)$game['price'],
    'price_fmt'   => number_format((float)$game['price'], 2),
    'rating'      => (float)$game['rating'],
    'release'     => date('d F Y', strtotime($game['release_date'])),
    'developer'   => htmlspecialchars($game['developer'])
];

// Genre Styling Logic
$genre_map = [
    'Survival'  => 'bg-orange-500/20 text-orange-500',
    'Adventure' => 'bg-green-500/20 text-green-500',
    'Racing'    => 'bg-yellow-500/20 text-yellow-500',
    'Strategy'  => 'bg-amber-600/20 text-amber-600'
];
$genre_class = $genre_map[$display['genre']] ?? 'bg-indigo-500/20 text-indigo-400';

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $display['title'] ?> | YoToy Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f1f5f9; }
        .glass { background: rgba(31, 41, 55, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="bg-fixed bg-cover bg-center" style="background-image: url('<?= $display['image'] ?>');">
    
    <div class="min-h-screen bg-slate-950/80 flex flex-col">
        
        <header class="sticky top-0 z-50 glass">
            <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
                <a href="index.php" class="text-2xl font-black text-indigo-500 uppercase tracking-tighter">
                    Yo<span class="text-orange-500">Toy</span>
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="allgame.php" class="hover:text-indigo-400 transition">เกมทั้งหมด</a>
                    <button id="open-cart-btn" class="relative p-2 text-gray-300 hover:text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span id="cart-item-count" class="absolute -top-1 -right-1 bg-orange-600 text-[10px] font-bold px-1.5 rounded-full">0</span>
                    </button>
                    <?php if ($is_logged_in): ?>
                        <a href="?logout=1" class="text-sm bg-gray-700 px-4 py-2 rounded-full hover:bg-gray-600 transition">ออกจากระบบ</a>
                    <?php else: ?>
                        <a href="login.php" class="text-sm bg-orange-600 px-4 py-2 rounded-full hover:bg-orange-700 transition">เข้าสู่ระบบ</a>
                    <?php endif; ?>
                </div>
            </nav>
        </header>

        <main class="container mx-auto px-4 py-12 flex-grow">
            <article class="max-w-6xl mx-auto glass rounded-3xl overflow-hidden shadow-2xl">
                
                <div class="relative h-[300px] md:h-[500px]">
                    <img src="<?= $display['image'] ?>" alt="<?= $display['title'] ?>" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 md:p-12 w-full">
                        <span class="<?= $genre_class ?> px-3 py-1 rounded text-xs font-bold uppercase tracking-widest mb-4 inline-block">
                            <?= $display['genre'] ?>
                        </span>
                        <h1 class="text-4xl md:text-6xl font-extrabold text-white"><?= $display['title'] ?></h1>
                    </div>
                </div>

                <div class="p-8 md:p-12 grid grid-cols-1 lg:grid-cols-3 gap-12">
                    
                    <div class="lg:col-span-2 space-y-8">
                        <section>
                            <h2 class="text-indigo-400 text-sm font-bold uppercase tracking-[0.2em] mb-4">เกี่ยวกับเกม</h2>
                            <p class="text-xl text-slate-200 font-light italic mb-6">"<?= $display['short_desc'] ?>"</p>
                            <div class="text-slate-400 leading-relaxed space-y-4">
                                <?= $display['long_desc'] ?>
                            </div>
                        </section>

                        <section class="pt-8 border-t border-slate-700/50">
                            <h2 class="text-orange-500 text-sm font-bold uppercase tracking-[0.2em] mb-6">คะแนนรีวิวผู้เล่น</h2>
                            <div class="flex items-end space-x-4">
                                <span class="text-7xl font-black leading-none"><?= number_format($display['rating'], 1) ?></span>
                                <div class="mb-1">
                                    <div class="flex text-orange-400 mb-1">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <svg class="w-5 h-5 <?= ($i <= floor($display['rating'])) ? 'fill-current' : 'text-slate-600' ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="text-xs text-slate-500">จากคะแนนโหวตทั่วโลก</p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside>
                        <div class="bg-slate-800/40 rounded-2xl p-6 border border-white/5 sticky top-28">
                            <div class="mb-6">
                                <p class="text-slate-400 text-xs uppercase font-bold tracking-widest mb-1">ราคา</p>
                                <p class="text-4xl font-black text-white">฿<?= $display['price_fmt'] ?></p>
                            </div>

                            <button id="add-to-cart-btn" 
                                    data-id="<?= $game['id'] ?>" 
                                    data-title="<?= $display['title'] ?>"
                                    data-price="<?= $display['price_raw'] ?>"
                                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-500/20 transition-all active:scale-95 flex justify-center items-center space-x-2 mb-8">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>เพิ่มลงตะกร้า</span>
                            </button>

                            <div class="space-y-4 pt-6 border-t border-slate-700/50 text-sm">
                                <div class="flex justify-between"><span class="text-slate-500">นักพัฒนา</span><span class="font-semibold text-slate-200"><?= $display['developer'] ?></span></div>
                                <div class="flex justify-between"><span class="text-slate-500">วางจำหน่าย</span><span class="font-semibold text-slate-200"><?= $display['release'] ?></span></div>
                                <div class="flex justify-between"><span class="text-slate-500">หมวดหมู่</span><span class="font-semibold text-slate-200"><?= $display['genre'] ?></span></div>
                            </div>
                        </div>
                    </aside>

                </div>
            </article>
        </main>

        <footer class="py-10 text-center text-slate-500 text-sm border-t border-white/5 glass">
            <p>&copy; 2026 YoToy Digital Games Store. All rights reserved.</p>
        </footer>
    </div>

    <div id="cart-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4 bg-black/90 backdrop-blur-md">
        </div>
