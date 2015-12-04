<?php
require_once 'common.php';

apiRequest('setWebhook', array('url' => ''));

echo "setWebhook Stoped";

return json_encode(array('result' => "setWebhook Stoped"));