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
        //$tomorrow = date('y:m:d', time() + 86400);
        //strtotime($referenceDate)
        $time = strtotime($referenceDate);
        $newDate = date("Y-m-d", strtotime("+1 month", $time));

        error_log("TIME: $time");
        error_log("NEWDATE: $newDate");
        //$newDate = $referenceDate->modify("+1 month");
        error_log("Date in a month: " + $newDate->format("Y-m-d"));

        return $newDate->format("Y-m-d");
    }

    public function getPaymentSchedule($period) {
        /* получает на вход кол-во месяцев, на выходе - массив дат с шагом в месяц */
        $datesArray = [];
        $referenceDate = date('Y-m-d');
        error_log("REFERENCE DATE: $referenceDate"); //текущая дата
        for ($i = 0; $i < $period; $i++ ) {
            $datesArray[i] = $this->getNextPaymentDate($referenceDate);
            $referenceDate = $this->getNextPaymentDate($referenceDate);
        }
        return $datesArray;
    }
}