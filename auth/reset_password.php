<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include '../config/db.php';        
include '../config/function.php';
include '../config/config.php'; 


$email = $password = $password2 = "";
$err = "";
$msg = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the password_reset table
    $check_token = mysqli_query($link, "SELECT * FROM password_reset WHERE token = '$token' ");
    if (mysqli_num_rows($check_token) == 0) {
			echo "<script>window.location.href='location:login.php';</script>";
    } else {
        // Get the email from the password_reset table
        $row = mysqli_fetch_assoc($check_token);
        $email = $row['email'];
    }
}else{
	echo "<script>window.location.href='location:login.php';</script>";

}

if (isset($_POST['reset_password'])) {
    if (empty($_POST['password'])) {
        $err = "Password is required";
    } elseif ($_POST['password'] != $_POST['password2']) {
        $err =  'Passwords do not match';
    } else {
        $password = clean($_POST['password']);
        $password2 = clean($_POST['password2']);
         $passwordhasg = password_hash(clean($_POST['password']), PASSWORD_DEFAULT);

        

        // Update the password in the users table
        $update_password = mysqli_query($link, "UPDATE users SET password = '$passwordhasg' WHERE email = '$email' ");

        if ($update_password) {
            // Delete the token from the password_reset table
            $delete_token = mysqli_query($link, "DELETE FROM password_reset WHERE email = '$email' ");

            if ($delete_token) {
                echo "<script>alert('Password Recovery Successfully'); window.location.href='login.php';</script>";

            
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password | <?php echo isset($sitename) ? $sitename : 'App'; ?></title>
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
        .fade-in { animation: fadeIn 0.4s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="flex h-screen bg-slate-50 dark:bg-[#02040a] text-slate-800 dark:text-slate-300 items-center justify-center p-4 relative overflow-hidden">

    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute top-1/2 -right-20 w-80 h-80 bg-purple-500/10 rounded-full blur-[80px] pointer-events-none"></div>

    <div class="glass-panel w-full max-w-sm rounded-3xl p-8 relative z-10 fade-in">
        
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 flex items-center justify-center text-white font-bold text-xl shadow-lg mx-auto mb-4">
                <i class="fa-solid fa-shield-check"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">New Password</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Set a strong password to protect your account.</p>
        </div>

        <form method="POST" id="resetForm">
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Email Address</label>
                    <div class="relative">
                        <input type="email" name="email" value="<?php echo $email ?>" required 
                            class="w-full bg-slate-100 dark:bg-[#0B0F19]/50 text-slate-500 border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none cursor-not-allowed" readonly>
                        <i class="fa-regular fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="••••••••" required 
                            class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Confirm Password</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="password2" placeholder="••••••••" required 
                            class="w-full bg-slate-50 dark:bg-[#0B0F19] text-slate-900 dark:text-white border border-slate-200 dark:border-white/10 rounded-xl py-3 px-4 pl-10 focus:outline-none focus:border-indigo-500 transition-all">
                        <i class="fa-solid fa-circle-check absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>
            </div>

            <button type="submit" name="reset_password" class="w-full py-4 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-black font-bold shadow-lg hover:opacity-90 transition-all hover:scale-[1.02]">
                Update Password
            </button>
        </form>

    </div>


</body>
</html>