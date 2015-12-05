<?php

/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 03.12.15
 * Time: 12:21
 */
class Keyboards {

    public $keyboardStart = [
        ['Привязать карту', 'Нет карты банка'],
                    ['Инфо']
    ];

    public $keyboardLendBorrow = [
        ['Взять в долг', 'Дать в долг']
    ];

    public $keyboardBorrow = [
        ['Запросить займ', 'Инфо'],
        ['Узнать ставку', 'График платежей'],
        ['Остаток долга', 'Данные по займам'],
                    ['Назад']
    ];

    public $keyboardLend = [
        ['Разместить сумму', 'Инфо'],
        ['Выданные займы', 'График получения выплат'],
        ['Подать на взыскание', 'Аналитика'],
        ['Назад']
    ];

    public $keyboardYesNo = [
        ['Да','Нет']
    ];
    
    public $keyboardBack = [
    		['Назад']
    ];

    public $keyboardNumeric = [
        [1, 2, 3],
        [4, 5, 6],
        [7, 8, 9],
            [0]
    ];

}