<?php
require_once 'index.php';

define('WEBHOOK_URL', 'https://dztestbot.herokuapp.com/web/index.php');

//if (php_sapi_name() == 'cli') {
  // if run from console, set or delete webhook
  apiRequest('setWebhook', WEBHOOK_URL);
//  exit;
//}
echo "setWebhook OK";
