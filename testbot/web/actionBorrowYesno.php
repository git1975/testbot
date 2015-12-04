<?php
class ActionBorrowYesno{
	function handle($message) {
		if($text == "Да"){
		
		} else if($text == "Нет"){
		
		} else {
			sendKeyboard($chat_id, "Ответьте Да или Нет",
					$keyboards->keyboardYesNo);
		}
	}
}