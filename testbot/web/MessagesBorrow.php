<?php
include_once 'paymentLogic.php';
include_once 'RiskLogic.php';
/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 03.12.15
 * Time: 20:50
 * Экраны и сообщения для заемщика
 */
class MessagesBorrow {




    private function getRiskGroup() {
        $riskLogic = new RiskLogic();
        return $riskLogic->getUserGroupRisk(); //Не забыть поправить на вызов с userId для реального сервиса
    }

    private function getPercent() {
        $riskLogic = new RiskLogic();
        return $riskLogic->getLoanPercent($riskLogic->getUserGroupRisk()); //Не забыть поправить на вызов с userId для реального сервиса
    }

    private function calcMonthlyPayment($loanSum, $period, $percent) {
        $paymentLogic = new PaymentLogic();
        return $paymentLogic->getPaymentMonthlyInq($loanSum, $period, $percent);
    }

    private function getPaymentSchedule($monthCount) {
        $paymentLogic = new PaymentLogic();
        $resultArray[] = $paymentLogic->getPaymentSchedule($monthCount);
        error_log("RESULT ARRAY:"); //TODO - тут пусто!
        error_log($paymentLogic->getPaymentSchedule($monthCount));
        foreach($resultArray as $result) {
            error_log($result);
        }
        return $paymentLogic->getPaymentSchedule($monthCount); //это массив

    }




    /* При переходе на экран заемщика */
    public $launchMsg = [
        'Здесь вы можете запросить деньги в долг.',

        'После получения займа я рассчитаю твой график платежей. Каждый месяц в назначенный день сумма платежа будет списываться с твоей карты.',

        'В случае отсутствия достаточной суммы на карте долг начнет считаться просроченным. Инвесторы, выдавшие тебе займ смогут подать на взыскание в коллекторское агентство.'
    ];

    /* Инфо */
    public $infoMsg = [
        'Прежде чем занимать деньги узнайте свои рейтинг и ставку, а так же возможности их изменения.',

        'Если вы уже занимали деньги с моей помощью, то вы можете посмотреть график платежей.Кроме того, я буду оповещать вас о приближении очередного платежа.',
    ];

    /* Узнать свой рейтинг и ставку */
    public $ratingMsg = [
        'D' => 'Чтобы понизить ставку просто принеси в любое отделение Альфа-Банка справку с работы и справку 2-НДФЛ.',

        'C' => 'У тебя плохая кредитная история или ее нет совсем. Чтобы понизить ставку тебе нужно успешно погасить займ по текущей ставке.',

        'B' => 'Чтобы понизить ставку тебе нужно успешно погасить займ по текущей ставке.',

        'A' => 'Твой рейтинг A, отличный. Ставка по следующему займу будет 15% годовых.'
    ];

    /* Посмотреть график платежей */
    public $payScheduleMsg =
        "Твой график платежей. Деньги будут автоматически списываться с твоей карты.

Декабрь 2015
5 декабря 2015 – 1600 руб. - погашен

13 декабря 2015 – 2000 руб. - погашен

Январь 2016
5 января 2016 – 1600 руб.
13 января 2016 – 2000 руб.

Февраль 2016
5 февраля 2016 – 1600 руб.
13 февраля 2016 – 2000 руб.

Март 2016
5 марта 2016 – 1600 руб.
13 марта 2016 – 2000 руб.
    ";

    /* Узнать оставшуюся сумму долга */
    public $debtRemainMsg =
        "По займу 60 000 руб от 4 июня 2015 тебе осталось вернуть 55 000 руб.
    По займу 30 000 руб от 13 июня 2015 тебе осталось вернуть 20 000 руб.";

    /* Данные по полученным займам */
    public $takenLoansMsg = "У тебя есть 1 погашенный займ.
Займ 30 000 руб от 3 сентября 2014 года.

У тебя 2 действующих займа.

Займ 60 000 руб от 4 июня 2015 тебе
Ставка 30%
Получен от:
Семенов А.И. – 500 руб
Зайцева А.В. – 500 руб.
Николаев Г.С. – 500 руб.

Займ 30 000 руб от 13 июня 2015 Ставка 30%
Получен от:
Перекопский В.К. – 500 руб.
Смирнов М.В. – 500 руб.
Константинов К.Е. – 500 руб.
    ";

    /* Запросить займ */
    public $loanInqMsg = [
        "Напиши сумму, которую ты хочешь занять и срок. Я расчитаю тебе сумму ежемесячного платежа. Эта сумма будет автоматически списываться с твоего счета. При отсутствии на счета необходимой суммы займ будет считаться просроченным и кредитор сможет инициировать взыскание.",

        "Сначала напиши сумму. Например, 20000",

        "Ты запросил 30 000 руб.",

        "Теперь срок в месяцах. Например 18",

        "Ты запросил 30 000 руб на 18 мес.",

        "Твой график платежей будет следующим… Напиши Да, если .. Нет, если…"
    ];

    public function getSumAndScheduleMessage($sum, $monthCount){
        $datesArray = $this->getPaymentSchedule($monthCount);
        $resultArray = [];
        $resultMsg = "Ты запросил $sum руб. на $monthCount мес. Твой график платежей будет следующим:

5 января 2016 – 1600 руб.

5 февраля 2016 – 1600 руб.

5 марта 2016 – 1600 руб.
        ";

        $i=0;
        $percent = $this->getPercent();
        foreach ($datesArray as $date) {
            error_log("DATE: $date");

            $resultArray[$i] = $date."   ".$this->calcMonthlyPayment($sum,$monthCount,$percent);
            //$resultArray[$i] = $date;
            error_log("RESULTARRAY $i : $resultArray[$i]");
            $i++;
        }

        foreach ($resultArray as $line) {
            $resultMsg.$line."\n";
        }

        return $resultMsg;

    }


}