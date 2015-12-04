<?php
require_once 'MessagesStart.php';
/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 04.12.15
 * Time: 15:38
 * Переход с главного экрана по нажатию "Инфо"
 */

function getInfoMsg() {

    $msgs = new MessagesStart();
    return $msgs->infoMsg;
}