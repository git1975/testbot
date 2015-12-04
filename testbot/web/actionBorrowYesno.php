<?php

require_once 'baseHandler.php';

class ActionBorrowYesno extends BaseHandler{
	function ActionBorrowYesno($message){
		self::$message = $message;
		init();
	}
	
	function handle() {
		if($text == "Да"){
		
		} else if($text == "Нет"){
		
		} else {
			sendKeyboard($chat_id, "Ответьте Да или Нет",
					$keyboards->keyboardYesNo);
		}
	}
}