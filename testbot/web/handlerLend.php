<?php
require_once 'baseHandler.php';
class HandlerLend extends BaseHandler {
	function HandlerLend($message) {
		$this->message = $message;
		init ();
	}
	function handle() {
		if ($text === 'Дать в долг') {
			setAction ( $chat_id, "action_lend" );
			$msg = new MessagesLend ();
			sendMsg ( $chat_id, $msg->launchMsg [0] );
			sendKeyboard ( $chat_id, $msg->launchMsg [1], $keyboards->keyboardLend );
		}
	}
}