<?php

/**
 * EMAIL FUNCTION (SAFE + DEBUG READY)
 */
function sendMail($email, $subject, $message) {

    $key = getenv('RESEND_API_KEY');

    if (!$key) {
        return [
            'error' => 'Missing API key',
            'debug' => [
                'env' => $_ENV['RESEND_API_KEY'] ?? null,
                'server' => $_SERVER['RESEND_API_KEY'] ?? null,
                'getenv' => getenv('RESEND_API_KEY')
            ]
        ];
    }

    try {

        // 💡 FIX: Direct absolute namespace call stops the "class in use" error
        $resend = \Resend::client($key);

        $resend->emails->send([
            'from' => 'Fichain <mail@mytradingaxis.live>',
            'to' => [$email],
            'subject' => $subject,
            'html' => $message,
        ]);

        return true;

    } catch (\Exception $e) {
        return [
            'error' => $e->getMessage(),
            'debug' => [
                'env' => $_ENV['RESEND_API_KEY'] ?? null,
                'server' => $_SERVER['RESEND_API_KEY'] ?? null,
                'getenv' => getenv('RESEND_API_KEY')
            ]
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
 * WALLET PHRASE
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