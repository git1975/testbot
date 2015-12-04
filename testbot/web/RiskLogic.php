<?php

/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 04.12.15
 * Time: 23:50
 * Класс с логикой рассчета группы риска заемщика
 */
class RiskLogic {

    //использовать это если будет коннект к сервисам:
    //public function getUserGroupRisk($userId) {

    public function getUserGroupRisk() {
        return "D";
    }

    public function getLoanPercent($riskGroup) {
        if ($riskGroup === "A") {
            return 15;
        } else if ($riskGroup === "B") {
            return 20;
        } else if ($riskGroup === "C") {
            return 40;
        } else {
            return 60;
        }
    }

}