<?php 
require_once 'config.php';
require_once 'db.php';

// Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load .env (make sure this runs once in your project bootstrap)
// Only load .env locally
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

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
            'from' => 'Fichain <mail@mytradingaxis.live>',
            'to' => [$email],
            'reply_to' => 'mail@mytradingaxis.live',
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