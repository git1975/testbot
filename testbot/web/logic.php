<?php

require_once 'Keyboards.php';
require_once 'MessagesStart.php';
require_once 'MessagesBorrow.php';
require_once 'MessagesLend.php';
require_once 'actionInfo.php';
require_once 'actionLinkCard.php';
require_once 'actionNoCard.php';
require_once 'telegram_io.php';
require_once 'actionBorrowYesno.php';
require_once 'handlerLend.php';
require_once 'handlerBorrow.php';
require_once 'RiskLogic.php';

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
        $action = getAction($chat_id);
        
        error_log("---->>>>>chat_id: $chat_id");
        error_log("---->>>>MSG: $text");
        error_log("---->>>>action: $action");
        
        $end = true;
        // Action handler
        if($action == "action_card_link"){
        	if(!is_numeric($text) || strlen($text) !== 16){
        		sendMsg($chat_id, "Неверный формат номера карты");
        	} else {
        		setAction($chat_id, "action_card_commit");
                $randomCode = rand(1000, 9999);
        		setFileContent($chat_id, "code", $randomCode);
        		setFileContent($chat_id, "card_pre", $text);
                //send($chat_id, "Подтвердите секретный код ".$randomCode);
                sendMsg($chat_id, $msgStart->linkCardMsg['smsSentMsg']);
                //sendMsg($chat_id, $randomCode);
        	}
        } else if($action == "action_card_commit"){
        	$content = getFileContent($chat_id, "code");
        	//if (strcasecmp($text, $content) === 0) {
        	if(is_numeric($text) && strlen($text) === 4){
        		setAction($chat_id, "start");
         		$content = getFileContent($chat_id, "card_pre");
       			setFileContent($chat_id, "card", $content);
       			if($content[0] === "4"){
        			sendStartScreen($chat_id, $msgStart->linkCardMsg['registrationSuccessMsg']);
       			} else if($content[0] === "5"){
        			sendStartScreen($chat_id, $msgStart->linkCardMsg['registrationSuccessMsg2']);
       			} else {
        			sendStartScreen($chat_id, $msgStart->linkCardMsg['registrationSuccessMsg3']);
       			}
        	} else {
        		sendMsg($chat_id, "Код неверный");
        	}
        } else if(strpos($action, "action_borrow")  !== false){
        	$handler = new HandlerBorrow();
        	$handler->handle($message);
        } else if(strpos($action, "action_lend") !== false){ 	
        	$handler = new HandlerLend();
        	$handler->handle($message);
        } else {
        	$end = false;
        }
        
        if($end){
        	return;
        }
        
        if ($text === 'Назад' || $text === 'start') {
        	setAction($chat_id, "");
        	sendStartScreen($chat_id, "Выберите действие");
        }
        
        // Root handler
        if ($text === 'Привязать карту') {        	
        	setAction($chat_id, "action_card_link");
        	sendMsg($chat_id, $msgStart->linkCardMsg['enterCardNumberMsg']);
            //sendKeyboardNumeric($chat_id, $msgStart->linkCardMsg['enterCardNumberMsg'], $keyboards->keyboardNumeric);
        } else if ($text === 'Нет карты банка') {
        	
        } else if ($text === 'Инфо') {
        	sendMsg($chat_id, $text);
        } else if ($text === 'Взять в долг') {
        	$riskLogic = new RiskLogic();
            setAction($chat_id, "action_borrow");
        	$msg = new MessagesBorrow();
            $riskGroup = $riskLogic->getUserGroupRisk();
            $percent = $riskLogic->getLoanPercent($riskGroup);
            $msgRisk = "Твой кредитный рейтинг $riskGroup, процентная ставка $percent% годовых";

            sendMsg($chat_id, $msg->launchMsg[0]);
        	sendMsg($chat_id, $msg->launchMsg[1]);
        	sendMsg($chat_id, $msg->launchMsg[2]);

        	sendKeyboard($chat_id, $msgRisk, $keyboards->keyboardBorrow);
        } else if ($text === 'Дать в долг') {
        	setAction ( $chat_id, "action_lend" );
        	$msg = new MessagesLend ();
        	sendMsg($chat_id, $msg->launchMsg [0]);
            sendMsg($chat_id, $msg->launchMsg [1]);
            sendKeyboard ( $chat_id, $msg->launchMsg [2], $keyboards->keyboardLend );
        } else if (strcasecmp($text, "start") === 0 || strcasecmp($action, "start") === 0) {
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
        } else {
            sendStartScreen($chat_id, "Выберите действие");
        }
    }
}

}



