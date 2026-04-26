<?php 
include 'db.php';
$select = mysqli_query($link, "SELECT * FROM settings WHERE id = 1");

if (mysqli_num_rows($select) > 0) {
    $row = mysqli_fetch_assoc($select);

    // --- Basic Site Details ---
    $sitename   = $row['sitename'];
    $site_email = $row['site_email'];
    $siteurl    = $row['siteurl'];
    $site_phone = $row['site_phone'];

    // --- Control Toggles (1 = ON, 0 = OFF) ---
    $enable_email_verification = $row['enable_email_verification'];
    $enable_wallet_phrase      = $row['enable_wallet_phrase_step'];
    $enable_wallet_connect     = $row['enable_wallet_connect'];
    $enable_kyc                = $row['enable_kyc'];
    $enable_pin_login          = $row['enable_pin_on_login'];

    // --- Financial Settings ---
    $referral_bonus   = $row['referral_bonus_percentage']; 
    $virtual_card_fee = $row['virtual_card_fee'];          
    $min_withdrawal   = $row['min_withdrawal_limit'];      
}
?>