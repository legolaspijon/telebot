<?php

namespace app\modules\api\helpers;
use \phpQuery;


class Minfin {

    const BASE_URI = 'http://minfin.com.ua/currency/';

    const CURRENCY_EUR = 'eur';
    const CURRENCY_USD = 'usd';
    const CURRENCY_RUB = 'rub';

    const ACTION_BUY = 'buy';
    const ACTION_SELL = 'sell';

    public static $chr = [
        self::CURRENCY_USD => 'USD',
        self::CURRENCY_EUR => 'EUR',
        self::CURRENCY_RUB => 'RUB',
    ];

    /**
     * Get average sums & deals list together
     * @param $currency string
     * @param $city string
     * @return array of results
     * */
    public static function getCurrencyAuction($currency = self::CURRENCY_USD, $city = 'all'){
        $result = self::getAverageSum($currency, $city);
        $result['deals_list'] = self::getDealsList($currency, $city);

        return $result;
    }

    /**
     * Parse average sums
     * @param $currency
     * @param string $city
     * @return array
     */
    public static function getAverageSum($currency, $city = 'all'){
//        $key = md5('everage_'.$currency.$city);
//        if(false === ($html = \Yii::$app->cache->get($key))){
        $params = 'auction/' . $currency;
        $html = self::curl($params . '/' . self::ACTION_BUY . '/' . $city);
//            \Yii::$app->cache->add($key, $html, 300);
//        }

        $document = phpQuery::newDocument($html);
        $sell_buy = $document->find('.au-status > .au-status--group:first-child');
        $infoBlocks = $document->find('.au-status > .au-status--group:last-child');

        // average sums
        $buy = $sell_buy->find('.au-status--group:eq(0) > .au-mid-buysell');
        $sell = $sell_buy->find('.au-status--group:eq(1) > .au-mid-buysell');

        // proposal
        $firstBlock = $infoBlocks->find('.au-status--group:eq(0)');
        $secondBlock = $infoBlocks->find('.au-status--group:eq(1)');
        $forBuy = $firstBlock->find('.au-pbar:first');
        $forSell = $firstBlock->find('.au-pbar:last');
        $buying = $secondBlock->find('.au-pbar:first');
        $selling = $secondBlock->find('.au-pbar:last');
        $rurs = str_replace('P', '₽', $buying->find('.rurs:first')->text());
        $forSell->find('*')->remove();
        $forBuy->find('*')->remove();
        $buying->find('*')->remove();
        $selling->find('*')->remove();
        $buy->find('*')->remove();
        $sell->find('*')->remove();

        return [
            'buy' => trim($buy->text()),
            'sell' => trim($sell->text()),
            'forSell' => self::getNums($forSell->text()),
            'forBuy' => self::getNums($forBuy->text()),
            'buying' => self::getNums($buying->text()) . $rurs,
            'selling' => self::getNums($selling->text()) . $rurs,
        ];
    }

    /**
     * Parse dials list sell & buy
     * @param string $currency
     * @param string $city
     * @return array
     */
    public static function getDealsList($currency, $city = 'all'){
//        $key = md5('dealslist_'.$currency.$city);
//        if(false === ($html = \Yii::$app->cache->get($key))){
            $params = 'auction/' . $currency;
            $html['sell'] = self::curl($params . '/' . self::ACTION_SELL . '/' . $city);
            $html['buy'] = self::curl($params . '/' . self::ACTION_BUY . '/' . $city);
//            \Yii::$app->cache->add($key, $html, 300); // 15 minutes
//        }
        $deals = array_merge(self::parseDeals($html['sell'], self::ACTION_SELL), self::parseDeals($html['buy'], self::ACTION_BUY));

        return $deals;
    }

    /**
     * Parse dial by action sell|buy
     * @param $html
     * @param $action
     * @return mixed
     */
    public static function parseDeals($html, $action){
        $document = phpQuery::newDocument($html);
        $deals = $document->find('.au-deals-list .au-deal');
        $result[$action] = []; $i = 0;
        foreach ($deals as $deal) {
            $dealRow = pq($deal)->find('.au-deal-row');
            $result[$action][$i]['time'] = pq($dealRow)->find('.au-deal-time')->text();
            $result[$action][$i]['currency'] = pq($dealRow)->find('.au-deal-currency')->text();
            $result[$action][$i]['sum'] = pq($dealRow)->find('.au-deal-sum')->text();
            $result[$action][$i]['msg'] = trim(pq($dealRow)->find('.au-deal-msg')->text());
            $i++;
        }

        $result[$action] = array_filter($result[$action], function($elem){
            return !empty($elem['currency']);
        });


        return $result;
    }

    public static function getNums($str) {
        preg_match('/(\b[0-9][0-9 ]+[$|€]?)/u', $str, $num);
        return isset($num[1]) ? $num[1] : null;
    }

    /**
     * Parse minfin HTML
     * @param $params string
     * @return string|false
     */
    public static function curl($params){
        $ch = curl_init(self::BASE_URI . $params);
        curl_setopt($ch, CURLOPT_USERAGENT, 'minfinbot/1.0 (http://minfinbot.web)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);

        return $html;
    }
}