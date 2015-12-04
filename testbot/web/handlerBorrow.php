<?php
require_once 'MessagesBorrow.php';
require_once 'Keyboards.php';
require_once 'telegram_io.php';
require_once 'actionBorrowYesno.php';
require_once 'logic.php';

/**
 * Created by PhpStorm.
 * User: annaserbakova
 * Date: 04.12.15
 * Time: 20:23
 */
class HandlerBorrow {
    //TODO до кнопок да и нет должно выводиться сообщение с графиком платежей

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
			setAction ( $chat_id, "action_borrow" );
            sendKeyboard ( $chat_id, "Выберите действие", $keyboards->keyboardBorrow );
            return;
		}
		

        if ($action == "action_borrow") {
			if ($text == 'Запросить займ') {
				setAction ( $chat_id, "action_borrow_sum" );
				sendMsg ( $chat_id, $msgs->loanInqMsg[0]);
				sendKeyboard ( $chat_id, $msgs->loanInqMsg[1], $keyboards->keyboardBorrow );
			} else if ($text == "Данные по займам") {
                sendKeyboard ( $chat_id, $msgs->takenLoansMsg, $keyboards->keyboardBorrow );
            } else if ($text == "Узнать ставку") {
                sendKeyboard ( $chat_id, $msgs->ratingMsg[0], $keyboards->keyboardBorrow );
            } else if ($text == "График платежей") {
                sendKeyboard ( $chat_id, $msgs->payScheduleMsg, $keyboards->keyboardBorrow );
            } else if ($text == "Остаток долга") {
                sendKeyboard ( $chat_id, $msgs->debtRemainMsg, $keyboards->keyboardBorrow );
            }

        } else if ($action == "action_borrow_sum") {
			if (! is_numeric ( $text )) {
				sendMsg ( $chat_id, "Неверный формат суммы" );
			} else {
				setAction ( $chat_id, "action_borrow_per" );
				setFileContent ( $chat_id, "borrowsum", $text );
				sendMsg ( $chat_id, $msgs->loanInqMsg[3] );
			}

        } else if ($action == "action_borrow_per") {
			if (! is_numeric ( $text ) || strlen ( $text ) > 4) {
				sendMsg ( $chat_id, "Неверный формат процентов" );
			} else {
				setAction ( $chat_id, "action_borrow_yesno" );
				setFileContent ( $chat_id, "borrowper", $text );
				$sum = getFileContent ( $chat_id, "borrowsum" );
				sendMsg ( $chat_id, "Ты запросил $sum руб на $text мес." );
				sendKeyboard ( $chat_id, "Согласен?", $keyboards->keyboardYesNo );
			}

        } else if ($action == "action_borrow_yesno") {
			if ($text == "Да") {
				setAction ( $chat_id, "action_borrow" );
                $lender = getFileContent($chat_id, "lender");
                if($lender === ""){
                	sendKeyboard ( $chat_id, "Инвесторы не найдены", $keyboards->keyboardYesNo );
                	return;
                }
                $sum = getFileContent ( $chat_id, "borrowsum" );
                $per = getFileContent ( $chat_id, "borrowper" );
                
                $str = $chat_id . ";" . $sum . ";" . $per . ";";
                addFileContent ( "borrowers", $str );
                
                error_log("--->>>action_borrow_yesno lender: $lender $sum");
                sendMsg($lender, "Заемщик списал сумму $sum");
				sendKeyboard ( $chat_id, "Поздравляем! Вы успешно оформили займ.", $keyboards->keyboardBorrow );
			} else if ($text == "Нет") {
				setAction ( $chat_id, "action_borrow" );
				sendKeyboard ( $chat_id, "Вы отказались от займа", $keyboards->keyboardBorrow );
			} else {
				sendKeyboard ( $chat_id, "Ответьте Да или Нет", $keyboards->keyboardYesNo );
			}
        } else if ($action == "action_borrow_payment_schedule") {
            //TODO тут вроде нет никаких действий

        } else if ($action == "action_borrow_loan_data") {
            //TODO тут вроде нет никаких действий

        } else if ($action == "action_borrow_debt_remaining") {
            //TODO тут вроде нет никаких действий

        } else if ($action == "action_borrow_ask_rating") {
            //TODO тут вроде нет никаких действий
        }
	}
}