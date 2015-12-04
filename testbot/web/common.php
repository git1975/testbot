<?php

require_once 'Keyboards.php';
require_once 'MessagesStart.php';
require_once 'MessagesBorrow.php';
require_once 'MessagesLend.php';

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

function processMessage($message) {
	setlocale(LC_ALL, 'ru_RU.UTF-8');
	
    $keyboards = new Keyboards;
    $msgStart = new MessagesStart;
    $msgBorrow = new MessagesBorrow;
    $msgLend = new MessagesLend;

    $message_id = $message['message_id'];
    $chat_id = $message['chat']['id'];

    if (isset($message['text'])) {
        // incoming text message
        $text = $message['text'];
        //$text = strtolower($text);
        error_log("chat_id: $chat_id");
        error_log("INCOMING MESSAGE: $text");


        if (strcasecmp($text, "startscreen") === 0) {
            apiRequestJson("sendMessage",
                [
                    'chat_id' => $chat_id,
                    'text' => 'startScreen testing. Keyboard: '.implode($keyboards->keyboardStart),
                    'reply_markup' => [
                        'keyboard' => $keyboards->keyboardStart,
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true
                    ]
                ]);
        } else if (strcasecmp($text, "lendscreen") === 0) {
            apiRequestJson("sendMessage",
                [
                    'chat_id' => $chat_id,
                    'text' => 'borrwo screen testing. Keyboard: '.implode($keyboards->keyboardLend),
                    'reply_markup' => [
                        'keyboard' => $keyboards->keyboardLend,
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true
                    ]
                ]);
        } else if (strcasecmp($text, "borrowscreen") === 0) {
            apiRequestJson("sendMessage",
                [
                    'chat_id' => $chat_id,
                    'text' => 'lendscreen testing. Keyboard: '.implode($keyboards->keyboardLend),
                    'reply_markup' => [
                        'keyboard' => $keyboards->keyboardBorrow,
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true
                    ]
                ]);
        } else if ($text == strtolower("я инвестор")) {
        	$file = 'user1.txt';
        	file_put_contents($file, $chat_id);
        	apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Ок, инвестор!'));
        } else if (strtolower($text) == strtolower("я заемщик")) {
        		$file = 'user2.txt';
        		file_put_contents($file, $chat_id);
        		apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Ок, заемщик!'));
        		
        		$file = 'user1.txt';
        		$current = file_get_contents($file);
        		apiRequest("sendMessage", array('chat_id' => $current, "text" => 'Заемщик появился!'));
        } else if (strcmp($text, strtolower("привет")) === 0) {
        	apiRequest("sendMessage", array('chat_id' => $current, "text" => 'Рад Вас видеть!'));
        } else if ($text === strtolower("пока")) {
        		apiRequest("sendMessage", array('chat_id' => $current, "text" => 'До свидания!'));
        } else {
            apiRequestWebhook("sendMessage",
                [
                    'chat_id' => $chat_id,
                    "reply_to_message_id" => $message_id,
                    "text" => 'Cool'
                ]);
        }
    }
}

function push() {
    // аналог пуш-уведомлений
    // использовать можно как нотификация пользователей,
    // периодические напоминания, либо показ первоначального экрана при запуске бота
    // т.е. тогда, когда нет изначального сообщения от пользователя

    /*
          apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Легко!', 'reply_markup' => array(
            'keyboard' => array(array('Привязать карту', 'Помощь', 'Взять в долг', 'Дать в долг')),
            'one_time_keyboard' => true,
            'resize_keyboard' => true)));
        */
    //apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Ок, я запомнил!'));

}
