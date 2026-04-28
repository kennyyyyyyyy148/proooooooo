<?php
// ping.php - lightweight keep-alive endpoint

require __DIR__ . '/config/db.php';

// very lightweight query to wake DB
mysqli_query($conn, "SELECT 1");

// respond fast
http_response_code(200);
echo "OK";