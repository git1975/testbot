<?php

require_once 'logic.php';

function processMessage($message) {
	$logic = new Logic();
	$logic->processMessage($message);
}


