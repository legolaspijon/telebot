<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\CurrencyAuction;

class ShowEurCommand extends BaseCommand {

    use FinanceTrait;

    public function execute()
    {
        $currencyAuction = new CurrencyAuction();
        echo $this->user->city;
        $city = array_search($this->user->city, \Yii::$app->params['cities']);
        $currency = CurrencyAuction::CURRENCY_EUR;
        $currencies_info = $currencyAuction->getCurrencyAuction($currency, $city);

        \Yii::trace(print_r($currencies_info, true), 'debug');

        $text = $this->formattingResponse($currencies_info, $currency);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }
}