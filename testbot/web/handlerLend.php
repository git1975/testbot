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
		} else if ($text == 'Назад') {
			setAction ( $chat_id, "action_lend" );
			sendKeyboard ( $chat_id, "Выберите действие", $keyboards->keyboardLend );
			return;
		} else if ($text == 'Дать в долг') {
			setAction ( $chat_id, "action_lend" );
			sendKeyboard ( $chat_id, "Выберите действие", $keyboards->keyboardLend );
			return;
		} else if ($text == 'Аналитика') {
			setAction ( $chat_id, "action_lend" );
			//addFileContent("borrowers", "qqqqq");
			sendKeyboard ( $chat_id, $content, $keyboards->keyboardBack );
			return;
		} else if ($text == 'Инфо') {
			setAction ( $chat_id, "action_lend_info" );
			
			$content = getFileContent2("borrowers");
			
			//sendKeyboard ( $chat_id, "Вы инвестировали:1) 16.01.2015 - 10 000 руб. Из них выдано для:-)16.01.2015 | Смирнов А.В. (id 12345) | 500 руб. | Остат 389 руб. | Рейт. B | Став. 20% | Займ 100 000 руб.-)16.01.2015 | Иванов С.В. (id 23453) | 500 руб. | Остат 356 руб. | Рейт. C | Став. 30% | Займ 50 000 руб.-)17.01.2015 | Симонов М.К. (id 74473) | 500 руб. | Остат 389 руб. | Рейт. C | Став. 30% | Займ 30 000 руб", $keyboards->keyboardBack );
			sendKeyboard ( $chat_id, $content, $keyboards->keyboardBack );
			return;
		}
			
		if($action == "action_lend_sum"){
			if(!is_numeric($text)){
				sendMsg($chat_id, "Неверный формат суммы");
			} else {
				setAction($chat_id, "action_lend_sumyesno");
				setFileContent($chat_id, "lendsum", $text);
				sendKeyboard($chat_id, "Ты инвестируешь $text руб. Напиши Да, если согласен или Нет, если хочешь изменить сумму",
						$keyboards->keyboardYesNo);
			}
		} else if($action == "action_lend_sumyesno"){
			if($text == "Да"){
				$sum = getFileContent($chat_id, "lendsum");
				setFileContent ( $chat_id, "lender", $chat_id );
				sendKeyboard($chat_id, "Вы инвестировали $sum руб. Следите за аналитикой", $keyboards->keyboardLend);
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