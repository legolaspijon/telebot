<?php

namespace app\modules\api\helpers\MinfinParser;

abstract class MinfinParser {

    const BASE_URI = 'http://minfin.com.ua/currency/';

    const CURRENCY_EUR = 'eur';
    const CURRENCY_USD = 'usd';
    const CURRENCY_RUB = 'rub';

    public static $chr = [
        self::CURRENCY_USD => 'USD',
        self::CURRENCY_EUR => 'EUR',
        self::CURRENCY_RUB => 'RUB',
    ];

    /**
     * Parse minfin HTML
     * @param $params string
     * @return string|false
     */
    protected function curl($params){
        $ch = curl_init(self::BASE_URI . $params);
        curl_setopt($ch, CURLOPT_USERAGENT, 'minfinbot/2.0 (http://minfinbot.web)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }

    /**
     * @param $str
     * @return string
     */
    protected function inner_trim_spaces($str){
        return trim(preg_replace('/\s+/', ' ', $str));
    }
}