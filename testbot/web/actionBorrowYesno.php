<?php

require_once 'MessagesBorrow.php';
require_once 'Keyboards.php';

class ActionBorrowYesNo {
	function handle($message) {
		$chat_id = $message['chat']['id'];
		$text = $message['text'];
        $kb = new Keyboards();
		if($text == "Да"){

            sendKeyboard($chat_id, "Поздравляем! Вы успешно Вы оформили займ.",$kb->keyboardBorrow);

		} else if($text == "Нет"){
            sendKeyboard($chat_id, "Вы выбрали\"нет\"",$kb->keyboardBorrow);
		} else {
			sendKeyboard($chat_id, "Ответьте Да или Нет",
					$keyboards->keyboardYesNo);
		}
	}
}