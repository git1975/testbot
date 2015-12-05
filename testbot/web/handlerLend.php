<?php
require_once 'Keyboards.php';
require_once 'telegram_io.php';
require_once 'MessagesLend.php';

class HandlerLend {
	function handle($message) {
		$chat_id = $message ['chat'] ['id'];
		$text = $message ['text'];
		$keyboards = new Keyboards ();
		$action = getAction($chat_id);
		$msgs = new MessagesLend();

		if ($text === 'Разместить сумму') {
			setAction ( $chat_id, "action_lend_sum" );
			sendMsg ( $chat_id, "Напиши сумму инвестиций. Например, 5000" );
			return;
		} else if ($text === 'Назад') {
			//if($action == "action_lend"){
			if($action == "action_lend" || $action == "action_lend_sumyesno"){ //чтобы после размещения суммы назад переходили по одному нажатию
				setAction ( $chat_id, "-" );
				sendStartScreen($chat_id, "Выберите действие");
			} else {
				setAction ( $chat_id, "action_lend" );
				sendKeyboard ( $chat_id, "Выберите действие", $keyboards->keyboardLend );
			}
			return;
		} else if ($text === 'Дать в долг') {
			setAction ( $chat_id, "action_lend_info" );
			sendMsg($chat_id, $msgs->infoMsg[0]);
			sendMsg($chat_id, $msgs->infoMsg[1]);
			sendMsg($chat_id, $msgs->infoMsg[2]);
            sendMsg($chat_id, $msgs->infoMsg[3]);
			sendKeyboard ( $chat_id, "Выберите действие", $keyboards->keyboardLend );
			return;
		} else if ($text === 'Аналитика') {
			setAction ( $chat_id, "action_lend_info" );
			//addFileContent("borrowers", "qqqqq");
			sendKeyboard ( $chat_id, $text, $keyboards->keyboardBack );
			return;
		} else if ($text === 'Инфо') {
			setAction ( $chat_id, "action_lend_info" );

			sendMsg($chat_id, $msgs->infoMsg[0]);
			sendMsg($chat_id, $msgs->infoMsg[1]);
            sendMsg($chat_id, $msgs->infoMsg[2]);
			sendKeyboard ( $chat_id, $msgs->infoMsg[3], $keyboards->keyboardBack );
			return;
		} else if ($text === 'Выданные займы') {
			setAction ( $chat_id, "action_lend_info" );
						
			$s = "";
			$file = fopen("borrowers.txt", "r");
			while($file && !feof($file)){
				$line = fgets($file);
				$pieces = explode(";", $line);
				if(strlen($pieces[1]) > 0){
					//$s = $s.$pieces[3]." на сумму ".$pieces[1]." руб. на срок ".$pieces[2]." мес.\r\n";
					$s = $s.$pieces[3]." на сумму 500 руб. на срок ".$pieces[2]." мес.\r\n";
				}
			}
			fclose($file);
			
			$s = $s."...\r\n";
			$s = $s."01.12.2015 на сумму 1000 руб. на срок 3 мес.\r\n";
			
			sendKeyboard ( $chat_id, $s, $keyboards->keyboardBack );
			return;
		} else if ($text == 'График получения выплат') {
			$file = fopen("borrowers.txt", "r");
			$sum = 0;
			while($file && !feof($file)){
				$line = fgets($file);
				$pieces = explode(";", $line);
				if($pieces[1] !== ""){
					//$sum = $sum + intval($pieces[1]);
					if(strlen($pieces[1]) > 0){
						$sum = $sum + 66;
					}
				}
			}
			fclose($file);
			
			sendKeyboard ( $chat_id, "В следующем месяце вы плучите $sum руб.", $keyboards->keyboardBack );
			return;
		} else if ($text == 'Подать на взыскание') {
			$file = fopen("borrowers.txt", "r");
			while($file && !feof($file)){
				$line = fgets($file);
				$pieces = explode(";", $line);
				$id = $pieces[0];
				sendMsg ( $id, "Господа! Инвестор требует вернуть долг!");
			}
			fclose($file);
			
			sendKeyboard ( $chat_id, "Сообщения должникам отправлены", $keyboards->keyboardBack );
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
				setFileContent2 ( "lender", $chat_id );
				sendKeyboard($chat_id, "Вы инвестировали $sum руб. Следите за аналитикой", $keyboards->keyboardLend);
			} else if($text == "Нет"){
				setAction($chat_id, "action_lend_sum");
				sendMsg ( $chat_id, "Напиши сумму инвестиций. Например, 5000" );
			} else {
				sendKeyboard($chat_id, "Ответьте Да или Нет",
						$keyboards->keyboardYesNo);
			}
		}
	}
}