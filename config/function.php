<?php 
include 'config.php';
include 'db.php';

// Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Load .env (make sure this runs once in your project bootstrap)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

use Resend\Resend;

function sendMail($email, $subject, $message) {

    $resendApiKey = $_ENV['RESEND_API_KEY'] 
    ?? $_SERVER['RESEND_API_KEY'] 
    ?? getenv('RESEND_API_KEY');

    if (!$resendApiKey) {
        return 'Mailer Error: Missing RESEND_API_KEY in environment';
    }

    $resend = Resend::client($resendApiKey);

    try {
        $resend->emails->send([
            'from' => 'Fichain <mail@fichain.com.ng>',
            'to' => [$email],
            'reply_to' => 'mail@fichain.com.ng',
            'subject' => $subject,
            'html' => $message,
        ]);

        return true;

    } catch (\Exception $e) {
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
        "quick","giraffe","ethereal","verdant","mellifluous","elephant","effervescent","enigma",
        "banana","paradox","quixotic","shiny","alpha","bravo","delta","echo","foxtrot","golf",
        "hotel","india","juliet","kilo","lima","mike","november","oscar","papa","quebec",
        "romeo","sierra","tango","uniform","victor","whiskey","xray","yankee","zulu","orbit",
        "galaxy","comet","planet","star","nebula","cosmos","solar","lunar","asteroid","meteor",
        "crypto","chain","block","token","asset","value","trade","market","secure","key"
    ];

    shuffle($wordList);
    return implode(" ", array_slice($wordList, 0, 12));
}
?>