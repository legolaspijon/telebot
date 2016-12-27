<?php

namespace app\modules\api\commands;

trait FinanceTrait {
    /**
     * @param $currencies_info array
     * @param $currency string
     * @return string
     */
    public function formattingResponse($currencies_info, $currency){
        $text = "<b>".\Yii::t('app', 'Currency').":</b> ".strtoupper($currency)."\n";
        $text .= "<b>".\Yii::t('app', 'City')."</b>: {$this->user->city}\n";
        $text .= "\n<i>".\Yii::t('app', 'Average buy').": {$currencies_info['buy']}</i>";
        $text .= "\n<i>".\Yii::t('app', 'Average sell').": {$currencies_info['sell']}</i>";
        $text .= "\n<i>".\Yii::t('app', 'Requests for sell').": {$currencies_info['forBuy']} requests</i>";
        $text .= "\n<i>".\Yii::t('app', 'Requests for buy').": {$currencies_info['forSell']} requests</i>";
        $text .= "\n<i>".\Yii::t('app', 'Sell').": {$currencies_info['buying']}</i>";
        $text .= "\n<i>".\Yii::t('app', 'Buy').": {$currencies_info['selling']}</i>";

        return $text;
    }
}