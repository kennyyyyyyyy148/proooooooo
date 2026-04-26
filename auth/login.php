<?php
session_start();
include '../config/db.php';
include '../config/function.php';
include '../config/config.php';

$alert = "";

// --- 1. HANDLE CREDENTIAL LOGIN ---
if (isset($_POST['login'])) {
    $input = clean($_POST['login_id']); 
    $password = clean($_POST['password']);

    $query = mysqli_query($link, "SELECT * FROM users WHERE username='$input' OR account_id='$input'");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['USER_LOGIN'] = $user['email']; 
            
            // --- SEND LOGIN EMAIL ALERT ---
            $user_email = $user['email'];
            $user_name  = $user['full_name'];
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $time       = date("Y-m-d H:i:s");
            
         $subject = "Security Alert: Login Detected - " . (isset($sitename) ? $sitename : 'App');

$body = '
<div style="font-family: Helvetica, Arial, sans-serif; background-color: #f8f9fa; padding: 40px 0;">
    <div style="max-width: 550px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden;">
        
        <div style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 40px 0; text-align: center;">
            <div style="background-color: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: inline-block; line-height: 60px; font-size: 30px;">
                ЁЯЫбя╕П
            </div>
            <h1 style="color: #ffffff; margin: 15px 0 0; font-size: 22px; font-weight: bold;">New Login Alert</h1>
        </div>

        <div style="padding: 40px;">
            <p style="color: #374151; font-size: 16px; margin-bottom: 25px;">Hello <strong>' . $user_name . '</strong>,</p>
            <p style="color: #6b7280; font-size: 15px; line-height: 1.6; margin-bottom: 25px;">
                We detected a successful login to your account. If this was you, you can safely ignore this email.
            </p>

            <div style="background-color: #f3f4f6; border-radius: 12px; padding: 20px;">
                <table width="100%">
                    <tr>
                        <td style="color: #6b7280; font-size: 13px; padding-bottom: 5px;">IP Address</td>
                        <td style="color: #6b7280; font-size: 13px; padding-bottom: 5px; text-align: right;">Time</td>
                    </tr>
                    <tr>
                        <td style="color: #111827; font-weight: bold; font-size: 15px;">' . $ip_address . '</td>
                        <td style="color: #111827; font-weight: bold; font-size: 15px; text-align: right;">' . $time . '</td>
                    </tr>
                </table>
            </div>

            <p style="text-align: center; margin-top: 30px;">
                <a href="#" style="background-color: #fee2e2; color: #dc2626; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: bold; display: inline-block;">I did not do this</a>
            </p>
        </div>
    </div>
    
    <div style="text-align: center; padding-top: 20px;">
        <p style="color: #9ca3af; font-size: 12px;">Sent automatically by ' . (isset($sitename) ? $sitename : 'App') . '</p>
    </div>
</div>';

sendMail($user_email, $subject, $body);
            
            // --- REDIRECT LOGIC ---
            // Check settings (default to 1 if not set)
            $pin_check = isset($enable_pin_login) ? $enable_pin_login : 1;
            
            if ($pin_check == 1) {
                echo "<script>window.location.href='enter-pin.php';</script>";
            } else {
                echo "<script>window.location.href='../app/dashboard.php';</script>";
            }
            exit();
            
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Failed', text: 'Incorrect Password'});";
        }
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Failed', text: 'Account not found'});";
    }
}

// --- 2. HANDLE WALLET PHRASE LOGIN ---
if (isset($_POST['wallet_login'])) {
    $phrase = clean($_POST['secret_phrase']);
    $query = mysqli_query($link, "SELECT * FROM users WHERE secret_phrase='$phrase'");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['USER_LOGIN'] = $user['email'];

        // --- SEND WALLET LOGIN ALERT ---
        $user_email = $user['email'];
        $user_name  = $user['full_name'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $time       = date("Y-m-d H:i:s");

        $subject = "Security Alert: Wallet Login Detected";
        $body = '
        <div style="font-family: Helvetica, Arial, sans-serif; background-color: #f3f4f6; padding: 30px;">
            <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden;">
                <div style="background-color: #02040a; padding: 25px; text-align: center;">
                    <h2 style="color: #ffffff; margin: 0; font-size: 20px; text-transform: uppercase;">Wallet Access</h2>
                </div>
                <div style="padding: 30px;">
                    <p style="color: #333; font-size: 16px;">Hello <strong>' . $user_name . '</strong>,</p>
                    <p style="color: #555;">Your account was accessed using your <strong>Secret Recovery Phrase</strong>.</p>
                    
                    <div style="background-color: #f9fafb; padding: 15px; border-left: 4px solid #10B981; margin: 20px 0;">
                        <p style="margin: 5px 0; font-size: 14px; color: #555;"><strong>IP Address:</strong> ' . $ip_address . '</p>
                        <p style="margin: 5px 0; font-size: 14px; color: #555;"><strong>Time:</strong> ' . $time . '</p>
                    </div>
                </div>
            </div>
        </div>';

        sendMail($user_email, $subject, $body);

        // Redirect
        $pin_check = isset($enable_pin_login) ? $enable_pin_login : 1;
        if ($pin_check == 1) {
            echo "<script>window.location.href='enter-pin.php';</script>";
        } else {
            echo "<script>window.location.href='../app/dashboard.php';</script>";
        }
        exit();
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Failed', text: 'Invalid Recovery Phrase'});";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo isset($sitename) ? $sitename : 'App'; ?></title>
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
        .hidden-form { display: none; }
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="flex h-screen bg-slate-50 dark:bg-[#02040a] text-slate-800 dark:text-slate-300 items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-500/10 rounded-full blur-[80px] pointer-events-none"></div>

    <div class="glass-panel w-full max-w-sm rounded-3xl p-8 relative z-10">
        
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white font-bold text-xl shadow-lg mx-auto mb-4">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Welcome Back</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Securely access your portfolio.</p>
        </div>

        <div class="flex p-1 bg-slate-100 dark:bg-[#0B0F19] rounded-xl mb-6 border border-slate-200 dark:border-white/10">
            <button onclick="switchTab('credentials')" id="tab-creds" class="flex-1 py-2 text-xs font-bold rounded-lg bg-white dark:bg-[#1E293B] text-indigo-600 shadow-sm transition-all">
                Credentials
            </button>
            <button onclick="switchTab('wallet')" id="tab-wallet" class="flex-1 py-2 text-xs font-bold rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-all">
                Wallet Phrase
            </button>
        </div>

        <form id="form-creds" method="POST" class="fade-in">
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Account ID or Username</label>
                    <div class="relative">
                        <input type="text" name="login_id" placeholder="e.g. johndoe or 8829102" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-regular fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase">Password</label>
                        <a href="forget_password.php" class="text-[11px] font-semibold text-indigo-500 hover:text-indigo-600 transition-colors">Forgot Password?</a>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" placeholder="••••••••" required class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
            </div>
            <button type="submit" name="login" class="w-full py-4 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-black font-bold shadow-lg hover:opacity-90 transition-all hover:scale-[1.02]">
                Continue
            </button>
        </form>

        <form id="form-wallet" method="POST" class="hidden-form fade-in">
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Secret Recovery Phrase</label>
                <textarea name="secret_phrase" placeholder="Enter your 12 word mnemonic phrase..." required rows="4" class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 focus:outline-none focus:border-indigo-500 transition-all text-sm resize-none"></textarea>
                <p class="text-[10px] text-slate-400 mt-2 flex items-center gap-1">
                    <i class="fa-solid fa-shield-halved text-green-500"></i> Processed locally. Never stored.
                </p>
            </div>
            
            <button type="submit" name="wallet_login" class="w-full py-4 rounded-xl bg-indigo-600 text-white font-bold shadow-lg hover:bg-indigo-500 transition-all hover:scale-[1.02]">
                Restore Access
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
            Don't have an account? <a href="register.php" class="text-indigo-500 font-bold hover:underline">Register</a>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const formCreds = document.getElementById('form-creds');
            const formWallet = document.getElementById('form-wallet');
            const tabCreds = document.getElementById('tab-creds');
            const tabWallet = document.getElementById('tab-wallet');

            if (tab === 'credentials') {
                formCreds.style.display = 'block';
                formWallet.style.display = 'none';
                
                tabCreds.classList.add('bg-white', 'dark:bg-[#1E293B]', 'text-indigo-600', 'shadow-sm');
                tabCreds.classList.remove('text-slate-500', 'dark:text-slate-400');
                
                tabWallet.classList.remove('bg-white', 'dark:bg-[#1E293B]', 'text-indigo-600', 'shadow-sm');
                tabWallet.classList.add('text-slate-500', 'dark:text-slate-400');
            } else {
                formCreds.style.display = 'none';
                formWallet.style.display = 'block';
                
                tabWallet.classList.add('bg-white', 'dark:bg-[#1E293B]', 'text-indigo-600', 'shadow-sm');
                tabWallet.classList.remove('text-slate-500', 'dark:text-slate-400');
                
                tabCreds.classList.remove('bg-white', 'dark:bg-[#1E293B]', 'text-indigo-600', 'shadow-sm');
                tabCreds.classList.add('text-slate-500', 'dark:text-slate-400');
            }
        }
        <?php echo $alert; ?>
    </script>
</body>
</html>