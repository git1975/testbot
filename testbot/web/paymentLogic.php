<?php

/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 04.12.15
 * Time: 23:49
 * класс с логикой для рассчета графика платежей
 */
class PaymentLogic {

    /* Расчет графика платежей для единственного запрошенного займа */
    public function getPaymentMonthlyInq($loanSum, $periodMonths, $percent) {
        // PMT = PV * (i/12) / [ 1 - (1 / (1 + i/12) )^n ]
        // PMT - ежемес. выплата
        // PV - сумма займа
        // i - процентная ставка
        // n - кол-во месяцев
        $pmt = $loanSum * ($percent/100/12) / ( 1 - pow((1/(1+$percent/100/12)),$periodMonths) );
        error_log("Loan payment monthly = $pmt");
        return $pmt;
    }

    /* Расчет графика платежей по уже имеющимся займам  */
    public function getPaymentMonthlyAll() {

    }

    private function getNextPaymentDate($referenceDate){
        // referenceDate - стартовая дата. Текущая, либо каждая следующая на каждом шаге итерации

        $time = strtotime($referenceDate);
        $newDate = date("Y-m-d", strtotime("+1 month", $time));

        return $newDate;
    }

    public function getPaymentSchedule($period) {
        /* получает на вход кол-во месяцев, на выходе - массив дат с шагом в месяц */
        $datesArray = [];
        $referenceDate = date('Y-m-d');

        for ($i = 0; $i < $period; $i++ ) {
            $datesArray[$i] = $this->getNextPaymentDate($referenceDate);
            $referenceDate = $this->getNextPaymentDate($referenceDate);
            error_log("REFERENCE DATE: $referenceDate"); //текущая дата
        }
        return $datesArray;
    }
}