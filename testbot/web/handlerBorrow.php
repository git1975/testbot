<?php
require_once 'MessagesBorrow.php';
require_once 'Keyboards.php';
require_once 'telegram_io.php';

/**
 * Created by PhpStorm.
 * User: annaserbakova
 * Date: 04.12.15
 * Time: 20:23
 */
class HandlerBorrow
{
    function handle($message) {
        $chat_id = $message['chat']['id'];
        $text = $message['text'];
        $kb = new Keyboards();
        $action = getAction($chat_id);

        if ($text === 'Инфо') {
			return;
        } else if ($text === '') {
			return;
        }
        
        if($action == "action_borrow"){
        	if($text == 'Запросить займ'){
        		setAction($chat_id, "action_borrow_sum");
        		sendMsg($chat_id, "Напиши сумму, которую ты хочешь занять и срок. Я рассчитаю тебе сумму ежемесячного платежа. "
        				. "Эта сумма будет автоматически списываться с твоего счета. При отсутствии на счете необходимой суммы "
        				. "займ будет считаться просроченным и кредитор сможет инициировать взыскание");
        		sendKeyboard($chat_id, "Сначала напиши сумму, например 20000",
        				$keyboards->keyboardBorrow);
        	}
        } else if($action == "action_borrow_sum"){
        	if(!is_numeric($text)){
        		sendMsg($chat_id, "Неверный формат суммы");
        	} else {
        		setAction($chat_id, "action_borrow_per");
        		setFileContent($chat_id, "borrowsum", $text);
        		sendMsg($chat_id, "Напиши срок");
        	}
        } else if($action == "action_borrow_per"){
        	if(!is_numeric($text) || strlen($text) > 4){
        		sendMsg($chat_id, "Неверный формат процентов");
        	} else {
        		setAction($chat_id, "action_borrow_yesno");
        		setFileContent($chat_id, "borrowper", $text);
        		$sum = getFileContent($chat_id, "borrowsum");
        		sendMsg($chat_id, "Ты запросил $sum руб на $text мес.");
        		sendKeyboard($chat_id, "Согласен?",
        				$keyboards->keyboardYesNo);
        	}
        } else if($action == "action_borrow_yesno"){
        	$handler = new ActionBorrowYesNo();
        	$handler->handle($message);
        }
    }

}