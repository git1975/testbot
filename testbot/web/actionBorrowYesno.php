<?php

require_once 'baseHandler.php';

class ActionBorrowYesno extends BaseHandler{

    function ActionBorrowYesno($message){
		$this->message = $message;
		init();
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