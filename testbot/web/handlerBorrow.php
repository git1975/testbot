<?php
require_once 'MessagesBorrow.php';
require_once 'Keyboards.php';
require_once 'telegram_io.php';
require_once 'actionBorrowYesno.php';
require_once 'logic.php';
require_once 'RiskLogic.php';

/**
 * Created by PhpStorm.
 * User: annaserbakova
 * Date: 04.12.15
 * Time: 20:23
 */
class HandlerBorrow {

    function sendAllMessages($chat_id, $msgArray) {
        foreach ($msgArray as $message) {
            sendMsg($chat_id, $message);
        }
    }

    function handle($message) {
		$chat_id = $message ['chat'] ['id'];
		$text = $message ['text'];
		$keyboards = new Keyboards ();
		$action = getAction ( $chat_id );
        $msgs = new MessagesBorrow();


		if ($text === 'Инфо') {
            //setAction ( $chat_id, "action_borrow_info" );
            sendMsg($chat_id, $msgs->infoMsg[0]);
            sendKeyboard($chat_id, $msgs->infoMsg[1], $keyboards->keyboardBorrow);
            return;
        } else if ($text === 'Взять в долг') {
        	setAction ( $chat_id, "action_borrow" );
        	sendKeyboard ( $chat_id, "Выберите действие", $keyboards->keyboardBorrow );
		} else if ($text === 'Назад') {
			if($action == "action_borrow"){
				setAction ( $chat_id, "-" );
				sendStartScreen($chat_id, "Start Screen");
			} else {
				setAction ( $chat_id, "action_borrow" );
				sendKeyboard ( $chat_id, "Выберите действие", $keyboards->keyboardLend );
			}
            return;
		}
		

        if ($action == "action_borrow") {
			if ($text == 'Запросить займ') {
			    $lender = getFileContent2("lender");
                if(trim($lender) === ""){
                	sendKeyboard ( $chat_id, "Инвесторы не найдены", $keyboards->keyboardBorrow );
                	return;
                }
				setAction ( $chat_id, "action_borrow_sum" );
				sendMsg ( $chat_id, $msgs->loanInqMsg[0]);
				sendKeyboard ( $chat_id, $msgs->loanInqMsg[1], $keyboards->keyboardBorrow );
			} else if ($text == "Данные по займам") {
                sendKeyboard ( $chat_id, $msgs->takenLoansMsg, $keyboards->keyboardBorrow );
            } else if ($text == "Узнать ставку") {
				$riskLogic = new RiskLogic();
				$riskGroup = $riskLogic->getUserGroupRisk();
				$loanPercent = $riskLogic->getLoanPercent($riskGroup);
				$msgRisk = "Твой кредитный рейтинг $riskGroup, процентная ставка $loanPercent% годовых";
				/*if ($riskGroup == 'A') {
					$result = 'Твой рейтинг A. Ставка по кредиту ';
				} else if ($riskGroup == 'B') {
					$result = ;
				} else if ($riskGroup == 'C') {
					$result = ;
				} else if ($riskGroup == 'D') {
					$result = ;
				} */

				sendKeyboard ( $chat_id, $msgRisk, $keyboards->keyboardBorrow );
            } else if ($text == "График платежей") {
                sendKeyboard ( $chat_id, $msgs->payScheduleMsg, $keyboards->keyboardBorrow );
            } else if ($text == "Остаток долга") {
                sendKeyboard ( $chat_id, $msgs->debtRemainMsg, $keyboards->keyboardBorrow );
            }

        } else if ($action == "action_borrow_sum") {
			if (! is_numeric ( $text )) {
				sendMsg ( $chat_id, "Неверный формат суммы" );
			} else if(is_numeric ( $text ) && intval($text) < 500){
				sendMsg ( $chat_id, "Сумма не может быть менее 500 руб." );
			} else {
				setAction ( $chat_id, "action_borrow_per" );
				setFileContent ( $chat_id, "borrowsum", $text );
				sendMsg ( $chat_id, $msgs->loanInqMsg[3] ); //теперь напиши срок
			}

        } else if ($action == "action_borrow_per") {
			if (! is_numeric ( $text ) || strlen ( $text ) > 4) {
				sendMsg ( $chat_id, "Неверный формат процентов" );
			} else {
				setAction ( $chat_id, "action_borrow_yesno" );
				setFileContent ( $chat_id, "borrowper", $text );
				$sum = getFileContent ( $chat_id, "borrowsum" );


                sendMsg($chat_id,$msgs->getSumAndScheduleMessage($sum, $text));

				sendKeyboard ( $chat_id, "Согласен?", $keyboards->keyboardYesNo );
			}

        } else if ($action == "action_borrow_yesno") {
			if ($text == "Да") {
				setAction ( $chat_id, "action_borrow" );
                $lender = getFileContent2("lender");
                if(trim($lender) === ""){
                	sendKeyboard ( $chat_id, "Инвесторы не найдены", $keyboards->keyboardYesNo );
                	return;
                }
                $sum = getFileContent ( $chat_id, "borrowsum" );
                $per = getFileContent ( $chat_id, "borrowper" );
                
                $str = $chat_id . ";" . $sum . ";" . $per . ";06.12.2015;";
                addFileContent ( "borrowers", $str );
                
                error_log("--->>>action_borrow_yesno lender: $lender $sum");
                sendMsg($lender, "Заемщик списал сумму 500 руб.");
				sendKeyboard ( $chat_id, "Поздравляем! Вы успешно оформили займ.", $keyboards->keyboardBorrow );
			} else if ($text == "Нет") {
				setAction ( $chat_id, "action_borrow" );
				sendKeyboard ( $chat_id, "Вы отказались от займа", $keyboards->keyboardBorrow );
			} else {
				sendKeyboard ( $chat_id, "Ответьте Да или Нет", $keyboards->keyboardYesNo );
			}
        }
	}
}