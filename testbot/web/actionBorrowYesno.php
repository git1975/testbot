<?php

require_once 'baseHandler.php';

class ActionBorrowYesNo extends BaseHandler{

    function ActionBorrowYesNo($message){
		$this->message = $message;
		$this->init();
	}
	
	function handle() {


		if($this->text == "Да"){
            sendMsg($this->chat_id, 'You pressed Yes');
		
		} else if($this->text == "Нет"){
            sendMsg($this->chat_id, 'You pressed No');
		} else {
			sendKeyboard($this->chat_id, "Ответьте Да или Нет",
					$this->keyboards->keyboardYesNo);
		}
	}
}