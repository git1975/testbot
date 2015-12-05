<?php

require_once 'Keyboards.php';
require_once 'MessagesStart.php';
require_once 'MessagesBorrow.php';
require_once 'MessagesLend.php';
require_once 'actionInfo.php';
require_once 'actionLinkCard.php';
require_once 'actionNoCard.php';

//define('BOT_TOKEN', '148713043:AAEb7CdO-XXnEzM7nlZVHn4wSixatlQ45DI');
define('BOT_TOKEN', '172422666:AAEy8f1P2sSigKdE-RqSE7jxC7LYI4cACQ8');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

function apiRequestWebhook($method, $parameters) {
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    $parameters["method"] = $method;

    header("Content-Type: application/json");
    echo json_encode($parameters);
    return true;
}

function exec_curl_request($handle) {
    $response = curl_exec($handle);

    if ($response === false) {
        $errno = curl_errno($handle);
        $error = curl_error($handle);
        error_log("Curl returned error $errno: $error\n");
        curl_close($handle);
        return false;
    }

    $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
    curl_close($handle);

    if ($http_code >= 500) {
        // do not wat to DDOS server if something goes wrong
        sleep(10);
        return false;
    } else if ($http_code != 200) {
        $response = json_decode($response, true);
        error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
        if ($http_code == 401) {
            throw new Exception('Invalid access token provided');
        }
        return false;
    } else {
        $response = json_decode($response, true);
        if (isset($response['description'])) {
            error_log("Request was successfull: {$response['description']}\n");
        }
        $response = $response['result'];
    }

    return $response;
}

function apiRequest($method, $parameters) {
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    foreach ($parameters as $key => &$val) {
        // encoding to JSON array parameters, for example reply_markup
        if (!is_numeric($val) && !is_string($val)) {
            $val = json_encode($val);
        }
    }
    $url = API_URL . $method . '?' . http_build_query($parameters);

    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);

    return exec_curl_request($handle);
}

function apiRequestJson($method, $parameters) {
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = array();
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    $parameters["method"] = $method;

    $handle = curl_init(API_URL);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
    curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

    return exec_curl_request($handle);
}

function setAction($chat_id, $action) {
	$file = "action_$chat_id.txt";
	file_put_contents($file, $action);
}

function getAction($chat_id) {
	$file = "action_$chat_id.txt";
	$action = file_get_contents($file);
	return $action;
}

function setFileContent($chat_id, $name, $content) {
    $file = $chat_id."_".$name.".txt";
	file_put_contents($file, $content);
}

function setFileContent2($name, $content) {
	$file = "$name.txt";
	file_put_contents($file, $content);
}

function addFileContent($name, $content) {
	$file = "$name.txt";
	error_log("--->>>addFileContent : $file $content");
	file_put_contents($file, "$content\r\n", FILE_APPEND);//
}

function getFileContent($chat_id, $name) {
    $file = $chat_id."_".$name.".txt";
	$content = file_get_contents($file);
	return $content;
}

function getFileContent2($name) {
	$file = "$name.txt";
	$content = file_get_contents($file);
	
	error_log("--->>>getFileContent2 : $file $content");
	
	return $content;
}

function isCardLinked($chat_id){
	$content = getFileContent($chat_id, "card");
	error_log("--->>>card: $content");
	if(strlen($content) == 16){
		return true;
	} else {
		return false;
	}
}

function sendMsg($chat_id, $content){
	apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $content));
}

function sendStartScreen($chat_id, $content){
	if($content == ""){
		$content = 'Start screen';
	}
	$keyboards = new Keyboards;
	$keyboard = $keyboards->keyboardStart;
	if(isCardLinked($chat_id)){
		$keyboard = $keyboards->keyboardLendBorrow;
	}
	apiRequestJson("sendMessage",
                [
                    'chat_id' => $chat_id,
                    'text' => $content,
                    'reply_markup' => [
                        'keyboard' => $keyboard,
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true
                    ]
                ]);
}

function sendKeyboard($chat_id, $content, $keyboard){
	apiRequestJson("sendMessage",
			[
					'chat_id' => $chat_id,
					'text' => $content,
					'reply_markup' => [
							'keyboard' => $keyboard,
							'one_time_keyboard' => true,
							'resize_keyboard' => true
					]
			]);
}

function sendKeyboardNumeric($chat_id, $content, $keyboard){
    apiRequestJson("sendMessage",
        [
            'chat_id' => $chat_id,
            'text' => $content,
            'reply_markup' => [
                'keyboard' => $keyboard,
                'one_time_keyboard' => false,
                'resize_keyboard' => true
            ]
        ]);
}





