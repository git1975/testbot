<?php
require_once 'MessagesBorrow.php';
require_once 'Keyboards.php';

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
        if ($text === 'Узнать ставку') {
            setAction ( $chat_id, "action_ask_rating" );
            $msg = new MessagesBorrow();
            // в зависимости от рейтинга выбирать сообщение из массива
            sendKeyboard ( $chat_id, $msg->ratingMsg[0], $kb->key );
        }
    }

}