<?php
require_once 'MessagesBorrow.php';
/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 04.12.15
 * Time: 16:45
 */

function getBorrowRootMsg(){
    $msgs = new MessagesBorrow();
    return $msgs->launchMsg;
}