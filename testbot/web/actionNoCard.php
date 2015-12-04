<?php
require_once 'MessagesStart.php';
/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 04.12.15
 * Time: 15:39
 * Переход с главного экрана по нажатию "Нет карты банка"
 */
function getNoCardMsg() {

    $msgs = new MessagesStart();
    return $msgs->noCardMsg;
}
