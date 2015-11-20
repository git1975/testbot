<?php
require_once 'index.php';

define('WEBHOOK_URL', 'https://dztestbot.herokuapp.com/web/index.php');

apiRequest('setWebhook', '');

echo "setWebhook Stoped";
