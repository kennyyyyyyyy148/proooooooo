<?php
session_start();
include '../config/db.php';
include '../config/config.php';
include '../config/function.php';

$alert = "";

// Security: If no user session, go back to login
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

if (isset($_POST['verify_pin'])) {
    $p1 = clean($_POST['p1']);
    $p2 = clean($_POST['p2']);
    $p3 = clean($_POST['p3']);
    $p4 = clean($_POST['p4']);
    $input_pin = $p1 . $p2 . $p3 . $p4;
    
    $user_id = $_SESSION['user_id'];
    
    // Fetch PIN from DB
    $query = mysqli_query($link, "SELECT transaction_pin FROM users WHERE id='$user_id'");
    
    if (mysqli_num_rows($query) > 0) {
        $user_data = mysqli_fetch_assoc($query);
        $stored_pin = $user_data['transaction_pin'];

        if ($input_pin === $stored_pin) {
            // Correct PIN - Go to Dashboard
            echo "<script>window.location.href='../app/dashboard.php';</script>";
            exit();
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Wrong PIN', text: 'Please try again.'});";
        }
    } else {
        // User not found in DB? Redirect to login
        echo "<script>window.location.href='login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter PIN | <?php echo isset($sitename) ? $sitename : 'App'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    }
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            @apply bg-white/70 dark:bg-[#121826]/70;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            @apply border border-slate-200 dark:border-white/5;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }
        /* Hide spinners */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
    </style>
</head>
<body class="flex h-screen bg-slate-50 dark:bg-[#02040a] text-slate-800 dark:text-slate-300 items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-500/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="glass-panel w-full max-w-sm rounded-3xl p-8 text-center relative z-10">
        
        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center mx-auto mb-6">
            <i class="fa-regular fa-user text-2xl text-slate-400"></i>
        </div>

        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Security Check</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">Enter your 4-digit PIN to continue.</p>

        <form method="POST">
            
            <div class="flex justify-center gap-4 mb-8">
                <input type="number" name="p1" id="p1" class="w-12 h-12 bg-slate-50 dark:bg-[#0B0F19] text-center text-xl font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" maxlength="1" required oninput="move(this, 'p2')">
                <input type="number" name="p2" id="p2" class="w-12 h-12 bg-slate-50 dark:bg-[#0B0F19] text-center text-xl font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" maxlength="1" required oninput="move(this, 'p3')">
                <input type="number" name="p3" id="p3" class="w-12 h-12 bg-slate-50 dark:bg-[#0B0F19] text-center text-xl font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" maxlength="1" required oninput="move(this, 'p4')">
                <input type="number" name="p4" id="p4" class="w-12 h-12 bg-slate-50 dark:bg-[#0B0F19] text-center text-xl font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" maxlength="1" required oninput="move(this, 'submitBtn')">
            </div>

            <button type="submit" name="verify_pin" id="submitBtn" class="w-full py-3.5 rounded-xl bg-indigo-600 text-white font-bold shadow-lg hover:scale-[1.02] transition-transform opacity-50 cursor-not-allowed" disabled>
                Unlock Dashboard
            </button>

        </form>

        <div class="mt-8 pt-6 border-t border-slate-200 dark:border-white/5">
            <a href="../app/logout.php" class="flex items-center justify-center gap-2 text-sm text-red-500 hover:text-red-400 font-bold transition-colors">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>

    </div>

    <script>
        function move(curr, nextID) {
            if(curr.value.length >= 1) {
                if(nextID === 'submitBtn') {
                    document.getElementById('submitBtn').disabled = false;
                    document.getElementById('submitBtn').classList.remove('opacity-50', 'cursor-not-allowed');
                    document.getElementById('submitBtn').focus();
                } else {
                    document.getElementById(nextID).focus();
                }
            }
        }
        
        window.onload = function() {
            document.getElementById('p1').focus();
        };

        <?php echo $alert; ?>
    </script>

</body>
</html>