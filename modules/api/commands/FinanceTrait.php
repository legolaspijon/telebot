<?php

namespace app\modules\api\commands;

trait FinanceTrait {
    /**
     * @param $currencies_info array
     * @param $currency string
     * @return string
     */
    public function formattingResponse($currencies_info, $currency){

        $fc = mb_strtoupper(mb_substr($this->user->city, 0, 1));
        $city = $fc.mb_substr($this->user->city, 1);

        $text = "<b>".\Yii::t('app', 'Currency').":</b> ".strtoupper($currency)."\n";
        $text .= "<b>".\Yii::t('app', 'City')."</b>: $city\n";
        $text .= "\n<i>".\Yii::t('app', 'Average buy').": {$currencies_info['buy']}</i>";
        $text .= "\n<i>".\Yii::t('app', 'Average sell').": {$currencies_info['sell']}</i>";
        $text .= "\n<i>".\Yii::t('app', 'Requests for sell').": {$currencies_info['forSell']}</i>";
        $text .= "\n<i>".\Yii::t('app', 'Requests for buy').": {$currencies_info['forBuy']}</i>";
        $text .= "\n<i>".\Yii::t('app', 'Sell').": {$currencies_info['selling']}</i>";
        $text .= "\n<i>".\Yii::t('app', 'Buy').": {$currencies_info['buying']}</i>";

        return $text;
    }
}