<?php

//define('BOT_TOKEN', '148713043:AAEb7CdO-XXnEzM7nlZVHn4wSixatlQ45DI');
define('BOT_TOKEN', '172422666:AAEy8f1P2sSigKdE-RqSE7jxC7LYI4cACQ8');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

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
  $url = API_URL.$method.'?'.http_build_query($parameters);

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
  // process incoming message
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($message['text'])) {
    // incoming text message
    $text = $message['text'];

    if (strpos($text, "/start") === 0) {
      apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Привет', 'reply_markup' => array(
        'keyboard' => array(array('Привет', 'Пока')),
        'one_time_keyboard' => true,
        'resize_keyboard' => true)));
    } else if (strpos($text, "?") === 0) {
    	apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Привет', 'reply_markup' => array(
    			'keyboard' => array(array('Привет', 'Hi')),
    			'one_time_keyboard' => true,
    			'resize_keyboard' => true)));
    } else if (strpos($text, "Привет") === 0) {
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Рад Вас видеть!'));
    } else if ($text === "Пока") {
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Пока-пока!'));
    } else if (strpos(strtolower($text), "услуги") !== false) {
      apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Легко!', 'reply_markup' => array(
        'keyboard' => array(array('Кредит', 'Депозит')),
        'one_time_keyboard' => true,
        'resize_keyboard' => true)));
    } else if ($text === 'develop') {
    	$keyboard = [
    		['Привязать карту', 'Помощь'],
    		['Взять в долг', 'Дать в долг']
    	]; 
    	apiRequestJson("sendMessage", 
    	array(
    	'chat_id' => $chat_id,
    	"text" => 'Легко!',
    	'reply_markup' => array(
        'keyboard' => $keyboard,	
        'one_time_keyboard' => true,
        'resize_keyboard' => false)));
    } else if (strpos($text, "/stop") === 0) {
      // stop now
    } else {
      apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => 'Cool'));
    }
  } else {
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Я понимаю  только текст'));
  }
}

function push() {
	// аналог пуш-уведомлений
	// использовать можно как нотификация пользователей, 
	// периодические напоминания, либо показ первоначального экрана при запуске бота
	// т.е. тогда, когда нет изначального сообщения от пользователя
	

      apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Легко!', 'reply_markup' => array(
        'keyboard' => array(array('Привязать карту', 'Помощь', 'Взять в долг', 'Дать в долг')),	
        'one_time_keyboard' => true,
        'resize_keyboard' => true)));
	 
}

define('WEBHOOK_URL', 'https://alfaprofitbot.herokuapp.com/web/index.php');

//if (php_sapi_name() == 'cli') {
  // if run from console, set or delete webhook
  apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
  //exit;
//}

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  // receive wrong update, must not happen
  exit;
}

if (isset($update["message"])) {
  processMessage($update["message"]);
}

//push(); // при запуске бота 