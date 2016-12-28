<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\CurrencyAuction;
use app\modules\api\helpers\Minfin;

class ShowRubCommand extends BaseCommand {

    use FinanceTrait;

    public function execute()
    {
        $currencyAuction = new CurrencyAuction();
        $city = array_search($this->user->city, \Yii::$app->params['cities']);
        $currency = CurrencyAuction::CURRENCY_RUB;
        $currencies_info = $currencyAuction->getCurrencyAuction($currency, $city);
        $text = $this->formattingResponse($currencies_info, $currency);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }
}