<?php

error_log('index.php started');

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    // receive wrong update, must not happen
    exit;
}

if ( isset($update["message"]) ) {
    processMessage($update["message"]);
}

//push(); // при запуске бота 