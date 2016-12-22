<?php

namespace app\modules\api\commands;

trait FinanceTrait {
    /**
     * @param $currencies_info array
     * @param $currency string
     * @return string
     */
    public function formattingResponse($currencies_info, $currency){
        $text = "<b>Валюта:</b> " . strtoupper($currency);
        $text .= "\n<i>Средняя покупка: {$currencies_info['buy']}</i>";
        $text .= "\n<i>Средняя продажа: {$currencies_info['sell']}</i>";
        $text .= "\n<i>Заявок на покупку: {$currencies_info['forBuy']}</i>";
        $text .= "\n<i>Заявок на продажу: {$currencies_info['forSell']}</i>";
        $text .= "\n<i>Покупают: {$currencies_info['buying']}</i>";
        $text .= "\n<i>Продают: {$currencies_info['selling']}</i>";

        return $text;
    }
}