<?php

class HandlerLend {
	function handle() {
		$chat_id = $message['chat']['id'];
		$text = $message['text'];
		
		if ($text === 'Дать в долг') {
			setAction ( $chat_id, "action_lend" );
			$msg = new MessagesLend ();
			sendMsg ( $chat_id, $msg->launchMsg [0] );
			sendKeyboard ( $chat_id, $msg->launchMsg [1], $keyboards->keyboardLend );
		}
	}
}