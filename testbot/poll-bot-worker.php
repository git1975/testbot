<?php
error_log('poll-bot-worker.php started');
set_time_limit(0);
require_once 'PollBot.php';
define('BOT_TOKEN', '148713043:AAEb7CdO-XXnEzM7nlZVHn4wSixatlQ45DI');
$bot = new PollBot(BOT_TOKEN, 'dztestbot');
$bot->runLongpoll();