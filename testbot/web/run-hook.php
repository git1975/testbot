<?php
require_once 'common.php';

define('WEBHOOK_URL', 'https://alfaprofitbot.herokuapp.com/web/index.php');

//if (php_sapi_name() == 'cli') {
  // if run from console, set or delete webhook
  error_log('run-hook.php started');
  apiRequest('setWebhook', array('url' => WEBHOOK_URL));
//  exit;
//}
echo "setWebhook OK";

return json_encode(array('result' => "setWebhook OK"));
