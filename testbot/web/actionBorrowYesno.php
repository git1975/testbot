<?php

require_once 'MessagesBorrow.php';
require_once 'Keyboards.php';
require_once 'telegram_io.php';

class ActionBorrowYesNo {

	function handle($message) {
        $text = $message ['text'];
        $chat_id = $message['chat']['id'];
        $action = getAction($chat_id);
        $kb = new Keyboards();

		if($text == "Да"){
            setAction($chat_id,"action_borrow");
            sendKeyboard($chat_id, "Поздравляем! Вы успешно Вы оформили займ.",$kb->keyboardBorrow);
		} else if($text == "Нет"){
            setAction($chat_id,"action_borrow");
            sendKeyboard($chat_id, "Вы отказались от займа",$kb->keyboardBorrow);
		} else {
			sendKeyboard($chat_id, "Ответьте Да или Нет",
					$kb->keyboardYesNo);
		}
	}
}