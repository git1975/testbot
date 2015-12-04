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
			sendMsg ( $chat_id, "Напиши сумму инвестиций. Например, 5000" );
			return;
		}
		if($action == "action_lend_sum"){
			if(!is_numeric($text)){
				sendMsg($chat_id, "Неверный формат суммы");
			} else {
				setAction($chat_id, "action_lend_sumyesno");
				setFileContent($chat_id, "lendsum", $text);
				sendKeyboard($chat_id, "Ты инвестируешь $text руб. Надиши Да, если согласен или Нет, если хочешь изменить сумму",
						$keyboards->keyboardYesNo);
			}
		} else if($action == "action_lend_sumyesno"){
			if($text == "Да"){
				$sum = getFileContent($chat_id, "lendsum");
				sendKeyboard($chat_id, "Вы инвестировали $sum руб. Следите за Аналитокой", $keyboards->keyboardBorrow);
			} else if($text == "Нет"){
				setAction($chat_id, "action_lend_sum");
				sendKeyboard ( $chat_id, "Напиши сумму инвестиций. Например, 5000" );
			} else {
				sendKeyboard($chat_id, "Ответьте Да или Нет",
						$kb->keyboardYesNo);
			}
		}
	}
}