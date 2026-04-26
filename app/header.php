<?php
// Ensure session is started and variables like $kyc_status are loaded
if (session_status() === PHP_SESSION_NONE) {
    include '../app/session.php';
}

// Fallback for avatar
$avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($fullname) . "&background=random";

// --- DYNAMIC STATUS LOGIC ---
$status_label = "Unverified";
$status_color = "text-slate-500 dark:text-slate-400"; // Default Gray

if (isset($kyc_status)) {
    switch ($kyc_status) {
        case 'approved':
            $status_label = "Verified Pro";
            $status_color = "text-green-500 dark:text-green-400";
            break;
        case 'pending':
            $status_label = "Pending Review";
            $status_color = "text-yellow-500 dark:text-yellow-400";
            break;
        case 'rejected':
            $status_label = "Action Required";
            $status_color = "text-red-500 dark:text-red-400";
            break;
        default: // unverified
            $status_label = "Unverified";
            $status_color = "text-slate-500 dark:text-slate-400";
            break;
    }
}

// --- CHECK WALLET CONNECTION STATUS ---
$wallet_connected = false;
$wallet_query = mysqli_query($link, "SELECT id FROM crypto_wallets WHERE user_id = '$user_id' LIMIT 1");
if (mysqli_num_rows($wallet_query) > 0) {
    $wallet_connected = true;
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $sitename; ?> - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        dark: { bg: '#02040a', panel: '#0B0F19', border: '#1E293B' },
                        light: { bg: '#F8FAFC', panel: '#FFFFFF', border: '#E2E8F0' }
                    },
                    boxShadow: {
                        'neon': '0 0 20px rgba(99, 102, 241, 0.4)',
                        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.1)',
                    }
                }
            }
        }
    </script>
    <style>
        body { transition: background-color 0.3s ease, color 0.3s ease; }
        .glass-panel {
            @apply bg-white/70 dark:bg-[#121826]/70;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            @apply border border-slate-200 dark:border-white/5;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }
        .glass-header {
            @apply bg-white/90 dark:bg-[#02040a]/85;
            backdrop-filter: blur(12px);
            @apply border-b border-slate-200 dark:border-white/5;
        }
        .dropdown-menu {
            opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease;
        }
        .dropdown-menu.active {
            opacity: 1; visibility: visible; transform: translateY(0);
        }
        .rotate-arrow { transform: rotate(180deg); }
    </style>
</head>

<body class="flex h-screen bg-slate-50 dark:bg-[#02040a] text-slate-800 dark:text-slate-300">

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/80 z-40 hidden backdrop-blur-sm transition-opacity"></div>

    <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-[#050810] border-r border-slate-200 dark:border-white/5 flex flex-col transform -translate-x-full md:translate-x-0 md:relative transition-transform duration-300 shadow-2xl">
        <div class="h-20 flex items-center gap-3 px-8 border-b border-slate-200 dark:border-white/5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/30">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <span class="text-xl font-bold text-slate-900 dark:text-white tracking-tight"><?php echo isset($sitename) ? $sitename : 'QFS Invest'; ?></span>
        </div>
    
        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-2">
            
            <div class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-4 mb-2">Platform</div>
    
            <a href="dashboard.php" class="flex items-center gap-4 px-4 py-3.5 bg-indigo-50 dark:bg-gradient-to-r dark:from-indigo-600/20 dark:to-transparent border-l-4 border-indigo-500 text-indigo-700 dark:text-white rounded-r-xl transition-all">
                <i class="fa-solid fa-grid-2 text-indigo-500 dark:text-indigo-400"></i>
                <span class="font-medium">Dashboard</span>
            </a>
    
            <a href="assets.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all group">
                <i class="fa-solid fa-coins group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors"></i>
                <span>Assets</span>
            </a>
    
            <a href="investments.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all group">
                <i class="fa-solid fa-chart-line group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors"></i>
                <span>Investments</span>
            </a>
    
            <a href="auto-trading.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all group">
                <i class="fa-solid fa-robot group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors"></i>
                <span>Auto Trading</span>
            </a>
    
            <a href="swap.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all group">
                <i class="fa-solid fa-arrow-right-arrow-left group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors"></i>
                <span>Swap Assets</span>
            </a>
    
            <div class="h-px bg-slate-200 dark:bg-white/5 my-4 mx-4"></div>
    
            <div class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest px-4 mb-2">Banking</div>
    
            <a href="cards.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all group">
                <i class="fa-regular fa-credit-card group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors"></i>
                <span>Virtual Cards</span>
            </a>
            
            <a href="cards.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all group">
                <i class="fa-solid fa-university text-lg leading-none flex-shrink-0"></i>
                <span>Connect Your Bank</span>
            </a>
    
    
            <a href="kyc.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all group">
                <i class="fa-solid fa-shield-halved group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors"></i>
                <span>AML / KYC</span>
            </a>
    
            <a href="referrals.php" class="nav-item flex items-center gap-4 px-4 py-3.5 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition-all group">
                <i class="fa-solid fa-users group-hover:text-indigo-500 dark:group-hover:text-indigo-400 transition-colors"></i>
                <span>Referrals</span>
            </a>
    
        </nav>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-96 bg-indigo-500/10 dark:bg-indigo-900/20 blur-[120px] pointer-events-none"></div>

        <header class="h-20 glass-header flex items-center justify-between px-4 md:px-8 sticky top-0 z-30 transition-colors duration-300">
            <button onclick="toggleSidebar()" class="md:hidden text-slate-600 dark:text-white p-2 mr-2">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>

            <div class="hidden md:flex flex-1 items-center gap-4 max-w-lg">
                <?php if ($wallet_connected): ?>
                    <a href="connect-wallet.php" class="flex items-center gap-2 px-4 py-2 rounded-full bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-400 text-xs font-bold hover:bg-green-500/20 transition-all">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        Wallet Connected
                    </a>
                <?php else: ?>
                    <a href="connect-wallet.php" class="flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-500/20 transition-all hover:scale-[1.02]">
                        <i class="fa-solid fa-link"></i> Connect Wallet
                    </a>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-3 md:gap-5 ml-auto">
                
                <a href="connect-wallet.php" class="md:hidden w-10 h-10 rounded-full flex items-center justify-center border border-slate-200 dark:border-white/10 text-indigo-500 hover:bg-indigo-50 dark:hover:bg-white/5 transition-all <?php echo $wallet_connected ? 'text-green-500 border-green-500/20 bg-green-500/5' : ''; ?>">
                    <i class="fa-solid fa-wallet"></i>
                </a>

                <button onclick="toggleTheme()" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-yellow-400 hover:bg-slate-200 dark:hover:bg-white/10 transition-colors flex items-center justify-center border border-slate-200 dark:border-white/5">
                    <i class="fa-solid fa-sun text-lg hidden dark:block"></i> 
                    <i class="fa-solid fa-moon text-lg block dark:hidden"></i> 
                </button>

                <div class="relative">
                    <button onclick="toggleProfile()" class="flex items-center gap-3 border-l border-slate-200 dark:border-white/10 pl-4 md:pl-6 focus:outline-none group">
                        <div class="hidden md:block text-right">
                            <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight"><?php echo $fullname; ?></p>
                            <p class="text-[10px] <?php echo $status_color; ?> font-bold uppercase tracking-wider">
                                <?php echo $status_label; ?>
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-full p-0.5 bg-gradient-to-br from-indigo-500 to-pink-500">
                            <img src="<?php echo $avatar_url; ?>" alt="User" class="w-full h-full rounded-full object-cover border-2 border-white dark:border-[#02040a]">
                        </div>
                        <i id="profile-arrow" class="fa-solid fa-chevron-down text-slate-400 text-xs ml-1 transition-transform duration-300"></i>
                    </button>

                    <div id="profile-dropdown" class="dropdown-menu absolute right-0 top-full mt-4 w-60 bg-white dark:bg-[#121826] border border-slate-200 dark:border-white/10 rounded-2xl shadow-xl z-50">
                        <div class="p-4 border-b border-slate-100 dark:border-white/5 md:hidden">
                            <p class="text-slate-900 dark:text-white font-bold"><?php echo $fullname; ?></p>
                            <p class="text-xs text-slate-500">ID: <?php echo $account_id; ?></p>
                        </div>
                        <ul class="py-2">
                            <li><a href="profile.php" class="block px-5 py-3 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 flex items-center gap-3"><i class="fa-regular fa-user w-4"></i> My Profile</a></li>
                            <div class="h-px bg-slate-100 dark:bg-white/5 my-1 mx-4"></div>
                            <li><a href="logout.php" class="block px-5 py-3 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center gap-3"><i class="fa-solid fa-right-from-bracket w-4"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>