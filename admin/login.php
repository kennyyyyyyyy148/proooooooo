<?php
session_start();
include '../config/db.php';

// If already logged in, redirect to Admin Dashboard
if(isset($_SESSION['admin_id'])){
    header("Location: index.php");
    exit();
}

$alert = "";

if(isset($_POST['admin_login'])){
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = $_POST['password'];

    // Check Admin Table
    $sql = "SELECT * FROM admin WHERE email = '$email'";
    $result = mysqli_query($link, $sql);
    
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        
        // Verify Password
        if(password_verify($password, $row['password'])){
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_role'] = $row['role'];
            
            $alert = "Swal.fire({
                icon: 'success',
                title: 'Welcome Back!',
                text: 'Logging into " . $sitename . " Admin Panel...',
                timer: 2000,
                showConfirmButton: false
            }).then(() => { window.location.href = 'index.php'; });";
        } else {
            $alert = "Swal.fire({icon: 'error', title: 'Access Denied', text: 'Invalid Password'});";
        }
    } else {
        $alert = "Swal.fire({icon: 'error', title: 'Login Failed', text: 'Admin account not found'});";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo $sitename; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-[#02040a] text-white h-screen flex items-center justify-center overflow-hidden relative">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-md p-4 relative z-10">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-indigo-500/20">
                <i class="fa-solid fa-shield-cat text-3xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold tracking-tight text-white"><?php echo $sitename; ?> Admin</h1>
            <p class="text-slate-400 mt-2 text-sm">Secure Management Portal</p>
        </div>

        <div class="glass p-8 rounded-3xl shadow-2xl">
            <form method="POST">
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" name="email" required placeholder="admin@example.com" 
                                   class="w-full bg-[#0B0F19] text-white border border-slate-800 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm placeholder-slate-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-500">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="password" required placeholder="••••••••" 
                                   class="w-full bg-[#0B0F19] text-white border border-slate-800 rounded-xl py-3.5 pl-10 pr-4 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all text-sm placeholder-slate-600">
                        </div>
                    </div>

                    <button type="submit" name="admin_login" 
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-500/25 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i> Login to Dashboard
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-600 mt-8">
            &copy; <?php echo date("Y"); ?> <?php echo $sitename; ?>. All rights reserved.
        </p>

    </div>

    <script>
        <?php echo $alert; ?>
    </script>

</body>
</html>