<?php 
require_once 'config.php';
require_once 'db.php';

// Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load .env (local only)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

use Resend\Resend;

/**
 * EMAIL FUNCTION WITH FULL DEBUG SUPPORT
 */
function sendMail($email, $subject, $message) {

    $debug = [];

    // Check all possible environment sources
    $debug['env'] = $_ENV['RESEND_API_KEY'] ?? null;
    $debug['server'] = $_SERVER['RESEND_API_KEY'] ?? null;
    $debug['getenv'] = getenv('RESEND_API_KEY');

    // Final key resolution
    $resendApiKey = $debug['env'] 
        ?? $debug['server'] 
        ?? $debug['getenv'];

    // If missing API key, return debug info
    if (!$resendApiKey) {
        return [
            'error' => 'Missing RESEND_API_KEY in environment',
            'debug' => $debug
        ];
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
        return [
            'error' => $e->getMessage(),
            'debug' => $debug
        ];
    }
}

/**
 * CLEAN INPUT
 */
function clean($data) {
    global $link;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($link, $data);
}

/**
 * REDIRECT
 */
function redirect($url) {
    echo "<script>window.location.href='$url';</script>";
    exit;
}

/**
 * GENERATE WALLET PHRASE
 */
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