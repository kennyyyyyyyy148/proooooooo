<?php
include '../app/session.php'; 

$alert = "";

// Security: Check if user_id is set (session.php handles this usually, but double check)
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

if (isset($_POST['verify'])) {
    $otp_input = clean($_POST['otp_code']);
    $user_id   = $_SESSION['user_id'];

    // 1. Check OTP
    $check = mysqli_query($link, "SELECT * FROM users WHERE id='$user_id' AND otp_code='$otp_input'");

    if (mysqli_num_rows($check) > 0) {
        // 2. Success
        mysqli_query($link, "UPDATE users SET email_verified_at=NOW(), otp_code=NULL WHERE id='$user_id'");
        
        // 3. Check Settings for Next Step
        // Use variables from session.php/config.php logic
        $check_wallet = isset($enable_wallet_phrase_step) ? $enable_wallet_phrase_step : (isset($enable_wallet_phrase) ? $enable_wallet_phrase : 1);
        
        $next_page = ($check_wallet == 1) ? 'wallet-phrase.php' : 'set-pin.php';

        $alert = "Swal.fire({
            icon: 'success', 
            title: 'Verified!', 
            text: 'Email verified successfully.', 
            timer: 1500, 
            showConfirmButton: false
        }).then(() => {
            window.location.href = '$next_page';
        });";
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Invalid Code', text: 'The code you entered is incorrect.'});";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | <?php echo isset($sitename) ? $sitename : 'App'; ?></title>
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
    </style>
</head>
<body class="flex h-screen bg-slate-50 dark:bg-[#02040a] text-slate-800 dark:text-slate-300 items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-500/10 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="glass-panel w-full max-w-md rounded-3xl p-8 relative z-10 text-center">
        
        <div class="w-16 h-16 rounded-full bg-indigo-500/10 flex items-center justify-center mx-auto mb-6 text-indigo-500">
            <i class="fa-regular fa-envelope-open text-3xl"></i>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Verify Email Address</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">We sent a 6-digit code to your email. Enter it below to secure your account.</p>

        <form method="POST">
            <div class="mb-6">
                <input type="text" name="otp_code" placeholder="ENTER CODE" required class="w-full bg-slate-50 dark:bg-[#02040a] text-center text-2xl tracking-[0.5em] font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-4 focus:outline-none focus:border-indigo-500 transition-all uppercase" maxlength="6">
            </div>

            <button type="submit" name="verify" class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold shadow-lg hover:scale-[1.02] transition-transform">
                Verify Account
            </button>
        </form>

        <p class="text-xs text-slate-500 mt-6">
            Didn't receive code? <a href="#" class="text-indigo-500 font-bold hover:underline">Resend</a>
        </p>
    </div>

    <script>
        <?php echo $alert; ?>
    </script>

</body>
</html>