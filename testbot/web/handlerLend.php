<?php

class HandlerLend {
	function HandlerLend() {
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