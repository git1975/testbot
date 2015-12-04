<?php
require_once 'Keyboards.php';
require_once 'telegram_io.php';

class HandlerLend {
	function handle($message) {
		$chat_id = $message ['chat'] ['id'];
		$text = $message ['text'];
		$keyboards = new Keyboards ();
		$action = getAction($chat_id);
		
		if ($text == 'Разместить сумму') {
			setAction ( $chat_id, "action_lend_sum" );
			sendKeyboard ( $chat_id, "Напиши сумму инвестиций. Например, 5000", $keyboards->keyboardLend );
			return;
		}
		if($action == "action_lend_sum"){
			if(!is_numeric($text)){
				sendMsg($chat_id, "Неверный формат суммы");
			} else {
				setAction($chat_id, "action_lend_sum_ok");
				setFileContent($chat_id, "lendsum", $text);
				sendMsg($chat_id, "Сумма принята");
			}
		}
	}
}