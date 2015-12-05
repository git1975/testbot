<?php

/**
 * Created by PhpStorm.
 * User: gmananton
 * Date: 03.12.15
 * Time: 20:50
 * Экраны и собщения для инвестора
 */
class MessagesLend {
	public $launchMsg = [
        "С помощью меня ты можешь быстро инвестировать от 5000 руб.",
        "Я предложу тебе выбрать уровень риска и предполагаемую доходность.",
        "Я разделю сумму твоих инвестиций на равные доли по 500 рублей и переведу разным заемщикам. Т.е. твои 5000 руб. разойдутся 10 заемщикам, 10 000 руб – 20 заемщикам и т.д.",
        "Ты можешь подавать заявку на взыскание выданных займов с просрочкой более двух месяцев. ООО Сентинел выкупает просроченный долг за 60% остатка по нему."

	];
	
    /* Подать на взыскание */
    public $penaltyInqMsg = [
        'По следующим займам имеется просрочка более 2 мес.
        16.01.2015 | Плесов А.В. (id 54789) | 500 руб. | Остат 389 руб. | Рейт. D | Став. 40% | Займ 100 000 руб.
        16.01.2015 | Кротова С.В. (id 12547) | 500 руб. | Остат 389 руб. | Рейт. D | Став. 40% | Займ 50 000 руб.',

        'Введите id займа, по которому требуется взыскание. Например, 12345',

        'Требование по займу id 54789 Плесов А.В. отправлено коллекторам. На вашу карту поступит 233.4 руб. (60% от остатка)'
    ];

    /* Аналитика */
    public $analyticsMsg = [
    'Ваша фактическая доходность: 55% годовых',

    'Выплаты за 2015 год составили:
    Тело займа: 8 465 руб.
    Проценты: 6 485 руб.
    Итого: 14 950 руб.',

    'Из них

    В Январе
    Тело займа: 1 465 руб.
    Проценты: 485 руб.
    Итого: 1 950 руб.

    В Феврале
    Тело займа: 1 620 руб.
    Проценты:  540 руб.
    Итого: 2 160 руб.
    '
    ];

    /* Разместить сумму */
    public $disposeSumMsg = [
        'Напиши сумму, которую ты хочешь Инвестировать. Я начну подбирать заемщиков для тебя и буду распределять твои деньги по 500 руб. каждому. Списания с твоей карты будут производиться автоматически.',

        'Напиши сумму инвестиций. Например, 5000',

        'Ты инвестируешь 10000 руб. Напиши Да, если согласен или Нет, если хочешь иправить сумму.',

        'Вы инвестировали 10000 руб. Следите за Аналитокой.',
    ];

    public $infoMsg = [
        "С помощью меня ты можешь быстро инвестировать от 5000 руб.",
        "Я предложу тебе выбрать уровень риска и предполагаемую доходность.",
        "Я разделю сумму твоих инвестиций на равные доли по 500 рублей и переведу разным заемщикам. Т.е. твои 5000 руб. разойдутся 10 заемщикам, 10 000 руб – 20 заемщикам и т.д.",
        "Ты можешь подавать заявку на взыскание выданных займов с просрочкой более двух месяцев. ООО Сентинел выкупает просроченный долг за 60% остатка по нему."
    ];

}