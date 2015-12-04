<?php
require_once 'Keyboards.php';
require_once 'MessagesStart.php';
require_once 'MessagesBorrow.php';
require_once 'MessagesLend.php';
require_once 'actionInfo.php';
require_once 'actionLinkCard.php';
require_once 'actionNoCard.php';
require_once 'telegram_io.php';

class BaseHandler{
	protected $message;
	protected $text;
	protected $keyboard;
	protected $msgStart;
	protected $msgBorrow;
	protected $msgLend;
	protected $message_id;
	protected $chat_id;

	function init() {
		$keyboards = new Keyboards;
		$msgStart = new MessagesStart;
		$msgBorrow = new MessagesBorrow;
		$msgLend = new MessagesLend;
		$message_id = $this->message['message_id'];
		$chat_id = $this->message['chat']['id'];
		$text = $this->message['text'];
	}
}