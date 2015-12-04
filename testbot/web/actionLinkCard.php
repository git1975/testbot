<?php
require_once 'MessagesStart.php';
/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 04.12.15
 * Time: 15:39
 * Переход с главного экрана по нажатию "Привязать карту"
 */

function getLinkCardMsg1() {
    $msgs = new MessagesStart();
    return $msgs->linkCardMsg[0];
}

function getLinkCardMsg2() {
    $msgs = new MessagesStart();
    return $msgs->linkCardMsg[1];
}

function getLinkCardMsg3() {
    $msgs = new MessagesStart();
    return $msgs->linkCardMsg[2];
}