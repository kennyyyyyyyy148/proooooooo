<?php 
include 'config.php';
include 'db.php';

// Include the Composer autoloader
require '../vendor/autoload.php'; 

function sendMail($email, $subject, $message) {
    // 1. Paste your actual Resend API Key here
    $resendApiKey = 're_GkMAQB3u_7FwRQXQSaarXQ4zzPDy2LtPo'; 

    $resend = Resend::client($resendApiKey);

    try {
        $resend->emails->send([
            // 2. This uses your newly verified domain!
            'from' => 'Fichain <mail@fichain.com.ng>', 
            'to' => [$email], // The user registering
            'reply_to' => 'mail@fichain.com.ng',
            'subject' => $subject,
            'html' => $message,
        ]);
        
        return true;
        
    } catch (\Exception $e) {
        // If something goes wrong, this will print the exact error
        return 'Mailer Error: ' . $e->getMessage();
    }
}

function clean($data) {
    global $link;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($link, $data);
}

function redirect($url) {
    echo "<script>window.location.href='$url';</script>";
    exit;
}

// Generate 12-word phrase
function generateWalletPhrase() {
    $wordList = [
        "quick", "giraffe", "ethereal", "verdant", "mellifluous", "elephant", "effervescent", "enigma", 
        "banana", "paradox", "quixotic", "shiny", "alpha", "bravo", "delta", "echo", "foxtrot", "golf", 
        "hotel", "india", "juliet", "kilo", "lima", "mike", "november", "oscar", "papa", "quebec", 
        "romeo", "sierra", "tango", "uniform", "victor", "whiskey", "xray", "yankee", "zulu", "orbit", 
        "galaxy", "comet", "planet", "star", "nebula", "cosmos", "solar", "lunar", "asteroid", "meteor",
        "crypto", "chain", "block", "token", "asset", "value", "trade", "market", "secure", "key"
    ];
    shuffle($wordList);
    return implode(" ", array_slice($wordList, 0, 12));
}

?>