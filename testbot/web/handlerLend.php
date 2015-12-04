<?php
require_once 'Keyboards.php';
class HandlerLend {
	function handle($message) {
		$chat_id = $message ['chat'] ['id'];
		$text = $message ['text'];
		$keyboards = new Keyboards ();
		
		if ($text == 'Разместить сумму') {
			setAction ( $chat_id, "action_lend_sum" );
			sendKeyboard ( $chat_id, "Напиши сумму инвестиций. Например, 5000", $keyboards->keyboardLend );
		}
	}
}