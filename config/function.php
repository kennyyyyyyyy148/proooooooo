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
    
    // --- MAILTRAP SMTP SETTINGS ---
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io'; // Mailtrap Host
    $mail->SMTPAuth = true;
    $mail->Port = 2525; // Mailtrap Port
    
    // TODO: Replace these with the ones from your Mailtrap "My Sandbox" page
    $mail->Username = 'cc2ef6fe8efbed'; 
    $mail->Password = 'af9562faec2164'; 
    // ------------------------------

    // Email Settings
    $mail->isHTML(true);
    $mail->setFrom('mail@mytradingaxis.live', 'MyTradingAxis Testing'); // Sender details
    $mail->addAddress($email); // Recipient email
    $mail->AddReplyTo("mail@mytradingaxis.live", "MyTradingAxis"); // Reply-to email
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