<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\BanksCurrencies;

class BanksCoursesCommand extends BaseCommand {

    public function execute()
    {
        $banks = new BanksCurrencies();
        $banks_info = $banks->getCurrenciesInBanks();
        $text = "\n<b>Курс валют в банках Украины на сегодня</b>";
        $text .= "\n<b>Курс к гривне (покупка / продажа)</b>";
        $text .= "\n\n<b>Доллар</b>";
        $text .= $this->text($banks_info['usd']);
        $text .= "\n\n<b>Евро</b>";
        $text .= $this->text($banks_info['eur']);
        $text .= "\n\n<b>Рубль</b>";
        $text .= $this->text($banks_info['rub']);

        var_dump(\Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]));
    }

    private function text($currency){
        $text = "\nСредний курс: {$currency['evrg']}";
        $text .= "\nНБУ: ";
	$text .= !empty($currency['nbu']) ? $currency['nbu'] : '-';
        $text .= "\nВалютный аукцион: {$currency['currency_auction']}";
        return $text;
    }
}