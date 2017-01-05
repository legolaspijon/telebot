<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\Interbank;

class MbCommand extends BaseCommand {

    public function execute()
    {
        $mb = new Interbank();
        $mb_info = $mb->getMB();
        $text = "\n<b>Курсы на межбанке</b>";
        $text .= "\n<b>Курс к гривне</b>";
        $text .= "\n\n<b>Покупка</b>";
        $text .= $this->text($mb_info['buy']);
        $text .= "\n\n<b>Продажа</b>";
        $text .= $this->text($mb_info['cell']);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }

    private function text($currency){
        $text = "\nДоллар: {$currency['usd']}";
        $text .= "\nЕвро: {$currency['eur']}";
        $text .= "\nРубль: {$currency['rub']}";

        return $text;
    }
}