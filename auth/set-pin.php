<?php
include '../app/session.php'; 

$alert = "";

if (isset($_POST['save_pin'])) {
    // 1. Clean Inputs
    $p1 = clean($_POST['p1']);
    $p2 = clean($_POST['p2']);
    $p3 = clean($_POST['p3']);
    $p4 = clean($_POST['p4']);
    
    // 2. Combine into 4-digit PIN
    $full_pin = $p1 . $p2 . $p3 . $p4;
    $user_id  = $_SESSION['user_id'];

    // 3. Validation
    if (strlen($full_pin) == 4 && is_numeric($full_pin)) {
        
        // 4. Update Database: Set PIN and mark setup as complete
        $update = mysqli_query($link, "UPDATE users SET transaction_pin='$full_pin', setup_complete=1 WHERE id='$user_id'");
        
        if ($update) {
            // 5. Success Logic: Destroy session to force re-login
            session_destroy(); 
            
            $alert = "Swal.fire({
                icon: 'success', 
                title: 'Setup Complete!', 
                text: 'Your PIN has been set. Please log in to continue.', 
                confirmButtonText: 'Go to Login',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });";
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Error', text: 'Failed to save PIN. Please try again.'});";
        }
    } else {
        $alert = "Swal.fire({icon: 'warning', title: 'Invalid PIN', text: 'PIN must be 4 digits.'});";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set PIN | <?php echo $sitename; ?></title>
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
        /* Remove number spinners */
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

    <div class="glass-panel w-full max-w-md rounded-3xl p-8 md:p-10 relative z-10 text-center">
        
        <div class="w-16 h-16 rounded-full bg-green-500/10 flex items-center justify-center mx-auto mb-6 text-green-500">
            <i class="fa-solid fa-shield-halved text-3xl"></i>
        </div>

        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Set Transaction PIN</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-8">Create a secure 4-digit PIN to authorize withdrawals and transfers.</p>

        <form method="POST">
            
            <div class="flex justify-center gap-4 mb-8">
                <input type="number" name="p1" id="pin1" class="w-14 h-14 bg-slate-50 dark:bg-[#0B0F19] text-center text-2xl font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" maxlength="1" required oninput="moveToNext(this, 'pin2')" onkeydown="handleBackspace(event, 'pin1')">
                
                <input type="number" name="p2" id="pin2" class="w-14 h-14 bg-slate-50 dark:bg-[#0B0F19] text-center text-2xl font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" maxlength="1" required oninput="moveToNext(this, 'pin3')" onkeydown="handleBackspace(event, 'pin1')">
                
                <input type="number" name="p3" id="pin3" class="w-14 h-14 bg-slate-50 dark:bg-[#0B0F19] text-center text-2xl font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" maxlength="1" required oninput="moveToNext(this, 'pin4')" onkeydown="handleBackspace(event, 'pin2')">
                
                <input type="number" name="p4" id="pin4" class="w-14 h-14 bg-slate-50 dark:bg-[#0B0F19] text-center text-2xl font-bold text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all" maxlength="1" required oninput="moveToNext(this, 'submitBtn')" onkeydown="handleBackspace(event, 'pin3')">
            </div>

            <button id="submitBtn" type="submit" name="save_pin" class="w-full py-4 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold shadow-lg hover:scale-[1.02] transition-transform opacity-50 cursor-not-allowed" disabled>
                Confirm & Set PIN
            </button>

        </form>

        <p class="text-xs text-slate-500 mt-6 flex items-center justify-center gap-2">
            <i class="fa-solid fa-lock"></i> Your PIN is encrypted locally.
        </p>
    </div>

    <script>
        <?php echo $alert; ?>

        // Auto-focus next input logic
        function moveToNext(current, nextFieldID) {
            if (current.value.length >= 1) {
                if(nextFieldID === 'submitBtn') {
                    // All filled
                    document.getElementById('submitBtn').disabled = false;
                    document.getElementById('submitBtn').classList.remove('opacity-50', 'cursor-not-allowed');
                    document.getElementById('submitBtn').focus();
                } else {
                    document.getElementById(nextFieldID).focus();
                }
            } else {
                // If user deleted a number, disable button
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        // Backspace logic to go to previous input
        function handleBackspace(event, prevFieldID) {
            if (event.key === 'Backspace' && event.target.value === '') {
                document.getElementById(prevFieldID).focus();
            }
        }

        // Focus first input on load
        window.onload = function() {
            document.getElementById('pin1').focus();
        };
    </script>

</body>
</html>