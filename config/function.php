<?php 
include 'config.php';
include 'db.php';

include 'mailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "mailer/PHPMailer.php";
require_once "mailer/SMTP.php";
require_once "mailer/Exception.php";

function sendMail($email, $subject, $message) {
    $mail = new PHPMailer();
    
    // SMTP Settings
    $mail->isSMTP();
    $mail->Host = "fichain.com.ng"; // Replace with your mail server
    $mail->SMTPAuth = true;
    $mail->Username = "mail@fichain.com.ng"; // Your cPanel email
    $mail->Password = '@@52352535##'; // Your cPanel email password
    $mail->Port = 465; // Typically 465 for SSL
    $mail->SMTPSecure = "ssl"; // Use 'tls' for 587

    // Email Settings
    $mail->isHTML(true);
    $mail->setFrom('mail@fichain.com.ng', 'Fichain'); // Sender details
    $mail->addAddress($email); // Recipient email
    $mail->AddReplyTo("mail@fichain.com.ng", "Fichain"); // Reply-to email
    $mail->Subject = $subject;
    $mail->MsgHTML($message);

    // Error handling
    if (!$mail->Send()) {
        return 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        return true;
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