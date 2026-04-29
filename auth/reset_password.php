<?php
session_start();

require_once '../config/bootstrap.php';
require_once '../config/function.php';

$email = "";
$err = "";

/**
 * STEP 1 — VALIDATE TOKEN
 */
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: login.php");
    exit;
}

$token = mysqli_real_escape_string($link, $_GET['token']);

$query = mysqli_query($link, "SELECT * FROM password_reset WHERE token = '$token' LIMIT 1");

if (!$query || mysqli_num_rows($query) === 0) {
    header("Location: login.php");
    exit;
}

$row = mysqli_fetch_assoc($query);
$email = $row['email'];

/**
 * STEP 2 — HANDLE RESET
 */
if (isset($_POST['reset_password'])) {

    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (empty($password)) {
        $err = "Password is required";
    } elseif ($password !== $password2) {
        $err = "Passwords do not match";
    } else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Update password
        $update = mysqli_query($link, "
            UPDATE users 
            SET password = '$hashed' 
            WHERE email = '$email'
        ");

        if ($update) {

            // Delete reset token
            mysqli_query($link, "
                DELETE FROM password_reset 
                WHERE email = '$email'
            ");

            echo "<script>
                alert('Password reset successful');
                window.location.href = 'login.php';
            </script>";
            exit;

        } else {
            $err = "Something went wrong. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password | <?php echo $sitename ?? 'App'; ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] }
                }
            }
        }
    </script>

</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-[#02040a]">

<div class="w-full max-w-md p-8 bg-white dark:bg-[#0B0F19] rounded-2xl shadow-lg">

    <h2 class="text-xl font-bold text-center mb-6 text-gray-900 dark:text-white">
        Reset Password
    </h2>

    <?php if (!empty($err)) : ?>
        <div class="bg-red-100 text-red-600 p-3 rounded mb-4 text-sm">
            <?php echo $err; ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="email"
               value="<?php echo htmlspecialchars($email); ?>"
               readonly
               class="w-full mb-4 p-3 rounded bg-gray-100 dark:bg-gray-800 text-gray-500">

        <input type="password"
               name="password"
               placeholder="New Password"
               class="w-full mb-4 p-3 rounded bg-gray-100 dark:bg-gray-800 text-white"
               required>

        <input type="password"
               name="password2"
               placeholder="Confirm Password"
               class="w-full mb-6 p-3 rounded bg-gray-100 dark:bg-gray-800 text-white"
               required>

        <button type="submit"
                name="reset_password"
                class="w-full bg-indigo-600 text-white py-3 rounded font-bold hover:bg-indigo-700 transition">
            Update Password
        </button>

    </form>

</div>

</body>
</html>