<?php
require_once 'MessagesBorrow.php';
require_once 'Keyboards.php';
require_once 'telegram_io.php';

/**
 * Created by PhpStorm.
 * User: annaserbakova
 * Date: 04.12.15
 * Time: 20:23
 */
class HandlerBorrow
{
    function HandlerBorrow() {
    }


    function handle($message) {
        $chat_id = $message['chat']['id'];
        $text = $message['text'];
        $kb = new Keyboards();
        $action = getAction($chat_id);

        if ($text === 'Инфо') {

        } else if ($text === '') {

        }
    }

}