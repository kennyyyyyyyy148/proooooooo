<?php

// 1. Composer autoload (ONLY HERE)
require_once __DIR__ . '/../vendor/autoload.php';

// 2. DB
require_once __DIR__ . '/db.php';

// 3. Config
require_once __DIR__ . '/config.php';

// 4. Dotenv (ONLY ONCE)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}