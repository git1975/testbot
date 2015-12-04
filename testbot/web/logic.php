<?php

require_once 'Keyboards.php';
require_once 'MessagesStart.php';
require_once 'MessagesBorrow.php';
require_once 'MessagesLend.php';
require_once 'actionInfo.php';
require_once 'actionLinkCard.php';
require_once 'actionNoCard.php';
require_once 'telegram_io.php';

class Logic {
function processMessage($message) {
    $keyboards = new Keyboards;
    $msgStart = new MessagesStart;
    $msgBorrow = new MessagesBorrow;
    $msgLend = new MessagesLend;

    $message_id = $message['message_id'];
    $chat_id = $message['chat']['id'];

    if (isset($message['text'])) {
        // incoming text message
        $text = $message['text'];
        //$text = strtolower($text);
        error_log("chat_id: $chat_id");
        error_log("INCOMING MESSAGE: $text");
        
        $file = "action_$chat_id.txt";
        $action = file_get_contents($file);
        
        $end = true;
        
        if($action == "action_card_link"){
        	if(strlen($text) !== 16){
        		send($chat_id, "Неверный формат номера карты");
        	} else {
        		setAction($chat_id, "action_card_commit");
                $randomCode = rand(1000, 9999);
        		setFileContent($chat_id, "code", $randomCode);
        		setFileContent($chat_id, "card_pre", $text);
                //send($chat_id, "Подтвердите секретный код ".$randomCode);
                send($chat_id, $msgStart->linkCardMsg['smsSentMsg']);
                send($chat_id, $randomCode);
        	}
        } else if($action == "action_card_commit"){
        	$content = getFileContent($chat_id, "code");
        	if (strcasecmp($text, $content) === 0) {
        		setAction($chat_id, "start");
         		$content = getFileContent($chat_id, "card_pre");
       			setFileContent($chat_id, "card", $content);
        		sendStartScreen($chat_id, $msgStart->linkCardMsg['registrationSuccessMsg']);
        	} else {
        		send($chat_id, "Код неверный");
        	}
        } else if($action == "action_borrow"){
        	if($text == 'Запросить займ'){
        		setAction($chat_id, "action_borrow_sum");
        		send($chat_id, "Напиши сумму, которую ты хочешь занять и срок. Я рассчитаю тебе сумму ежемесячного платежа. "
        				. "Эта сумма будет автоматически списываться с твоего счета. При отсутствии на счете необходимой суммы "
        				. "займ будет считаться просроченным и кредитор сможет инициировать взыскание");
        		sendKeyboard($chat_id, "Сначала напиши сумму, например 20000",
        				$keyboards->keyboardBorrow);
        	}
        } else if($action == "action_borrow_sum"){
        	if(is_numeric($text)){
        		send($chat_id, "Неверный формат суммы");
        	} else {
        		setAction($chat_id, "action_borrow_per");
        		setFileContent($chat_id, "borrowsum", $text);
        		send($chat_id, "Напиши срок");
        	}
        } else if($action == "action_borrow_per"){
        	if(is_numeric($text) || strlen($text) > 2){
        		send($chat_id, "Неверный формат процентов");
        	} else {
        		setAction($chat_id, "action_borrow_yesno");
        		setFileContent($chat_id, "borrowper", $text);
        		$sum = getFileContent($chat_id, "borrowsum");
        		send($chat_id, "Ты запросил $sum руб на $text мес.");
        		sendKeyboard($chat_id, "Согласен?",
        				$keyboards->keyboardYesNo);
        	}
        } else if($action == "action_borrow_yesno"){
        	if($text == "Да"){
        		
        	} else if($text == "Нет"){
        		
        	} else {
        		sendKeyboard($chat_id, "Ответьте Да или Нет",
        				$keyboards->keyboardYesNo);
        	}
        } else {
        	$end = false;
        }
        
        if($end){
        	return;
        }

        if (strcasecmp($text, "start") === 0 || strcasecmp($action, "start") === 0) {
        	sendStartScreen($chat_id, "");
        } else if (strcasecmp($text, "lend") === 0) {
            apiRequestJson("sendMessage",
                [
                    'chat_id' => $chat_id,
                    'text' => 'Lend screen',
                    'reply_markup' => [
                        'keyboard' => $keyboards->keyboardLend,
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true
                    ]
                ]);
        } else if (strcasecmp($text, "borrow") === 0) {
            apiRequestJson("sendMessage",
                [
                    'chat_id' => $chat_id,
                    'text' => 'Borrow Screen',
                    'reply_markup' => [
                        'keyboard' => $keyboards->keyboardBorrow,
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true
                    ]
                ]);
        } else if (strcasecmp($text, "borrowlend") === 0) {
            apiRequestJson("sendMessage",
                [
                    'chat_id' => $chat_id,
                    'text' => 'Borrow-Lend Screen',
                    'reply_markup' => [
                        'keyboard' => $keyboards->keyboardLendBorrow,
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true
                    ]
                ]);
        }
        else if ($text == "я инвестор") {
        	$file = 'user1.txt';
        	file_put_contents($file, $chat_id);
        	apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Ок, инвестор!'));
        } else if ($text == "я заемщик") {
        		$file = 'user2.txt';
        		file_put_contents($file, $chat_id);
        		apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Ок, заемщик!'));
        		
        		$file = 'user1.txt';
        		$current = file_get_contents($file);
        		apiRequest("sendMessage", array('chat_id' => $current, "text" => 'Заемщик появился!'));
        } else if ($text == "привет") {
        	apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Рад Вас видеть!'));
        } else if ($text === "пока") {
        		apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'До свидания!'));
        		//++++++++==============
        } else if ($text === 'Привязать карту') {        	
        	setAction($chat_id, "action_card_link");
        	send($chat_id, $msgStart->linkCardMsg['enterCardNumberMsg']);
        } else if ($text === 'Нет карты банка') {
        	
        } else if ($text === 'Инфо') {
        	send($chat_id, $text);
        } else if ($text === 'Взять в долг') {
        	setAction($chat_id, "action_borrow");        	
        	$msg = new MessagesBorrow();
        	send($chat_id, $msg->launchMsg[0]);
        	send($chat_id, $msg->launchMsg[1]);
        	sendKeyboard($chat_id, $msg->launchMsg[2], $keyboards->keyboardBorrow);
        } else {
            apiRequestWebhook("sendMessage",
                [
                    'chat_id' => $chat_id,
                    "reply_to_message_id" => $message_id,
                    "text" => 'Cool'
                ]);
        }
    }
}

}


