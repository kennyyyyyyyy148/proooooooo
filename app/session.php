<?php
session_start();

include '../config/db.php';        
include '../config/function.php';
include '../config/config.php';    

if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != "") {
    
    $user_id = $_SESSION['user_id'];
    $Get = mysqli_query($link, "SELECT * FROM users WHERE id = '$user_id'");

    if(mysqli_num_rows($Get) > 0){
        $row = mysqli_fetch_assoc($Get);

        // --- Identity ---
        $id = $row['id'];
        $account_id = $row['account_id'];
        $fullname   = $row['full_name']; 
        $username   = $row['username'];
        $email      = $row['email'];
        $password   = $row['password'];    
        
        // --- Security ---
        $transaction_pin = $row['transaction_pin'];
        $secret_phrase   = $row['secret_phrase'];
        $otp_code        = $row['otp_code'];
        
        // --- Referral ---
        $referral_code = $row['referral_code'];
        $referred_by   = isset($row['referred_by']) ? $row['referred_by'] : ''; 
        
        // --- Financials ---
        $balance           = isset($row['balance']) ? $row['balance'] : '0.00';
        $profit_balance    = isset($row['profit_balance']) ? $row['profit_balance'] : '0.00';
        $referral_earnings = isset($row['referral_earnings']) ? $row['referral_earnings'] : '0.00';
        
        // --- Status (UPDATED) ---
        $email_verified_at = $row['email_verified_at'];
        $setup_complete    = $row['setup_complete'];
        $date_created      = $row['created_at'];
        
       
        $kyc_status = isset($row['kyc_status']) ? $row['kyc_status'] : 'unverified'; 
        
        
        $usd_usdt_erc20 = $row['usdt_erc20_balance'];
        $usd_eth        = $row['eth_balance'];
        $usd_btc        = $row['btc_balance'];
        $usd_bnb        = $row['bnb_balance'];
        $usd_trx        = $row['trx_balance'];
        $usd_usdt_trc20 = $row['usdt_trc20_balance'];
        $usd_ltc        = $row['ltc_balance'];
        $usd_doge       = $row['doge_balance'];
        $usd_sol        = $row['sol_balance'];
        $usd_matic      = $row['matic_balance'];

        // Total Portfolio Value (Sum of all USD holdings)
        $total_balance = $usd_usdt_erc20 + $usd_eth + $usd_btc + $usd_bnb + $usd_trx + $usd_usdt_trc20 + $usd_ltc + $usd_doge + $usd_sol + $usd_matic;
        
    } else {
        session_destroy();
        echo "<script> window.location.href = '../auth/login.php'; </script>";
        exit();
    }

} else {
    echo "<script> window.location.href = '../auth/login.php'; </script>";
    exit();
}
?>