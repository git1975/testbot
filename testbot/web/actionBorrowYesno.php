<?php

require_once 'MessagesBorrow.php';
require_once 'Keyboards.php';

class ActionBorrowYesNo {
	function handle($message) {
		$chat_id = $this->message['chat']['id'];
		$text = $this->message['text'];
        $kb = new Keyboards();
		if($this->text == "Да"){

            sendKeyboard($this->chat_id, "Поздравляем! Вы успешно Вы оформили займ.",$kb->keyboardBorrow);

		} else if($this->text == "Нет"){
            sendKeyboard($this->chat_id, "Вы выбрали\"нет\"",$kb->keyboardBorrow);
		} else {
			sendKeyboard($this->chat_id, "Ответьте Да или Нет",
					$this->keyboards->keyboardYesNo);
		}
	}
}