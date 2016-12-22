<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\Minfin;

class ShowUsdCommand extends BaseCommand {

    use FinanceTrait;

    public function execute()
    {
        $city = array_search($this->user->city, \Yii::$app->params['cities']);
        $currency = Minfin::CURRENCY_USD;
        $currencies_info = Minfin::getCurrencyAuction($currency, $city);
        $text = $this->formattingResponse($currencies_info, $currency);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }
}