<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\Minfin;

class CourseCommand extends BaseCommand {

    public function execute()
    {
//        $currencies = Minfin::getCurrencyAuction(Minfin::CURRENCY_USD, 'vinnitsa');
//        $text = "<b>Валюта:</b> " . strtoupper(Minfin::CURRENCY_USD);
//        $text .= "\n<i>Средняя покупка: {$currencies['buy']}</i>";
//        $text .= "\n<i>Средняя продажа: {$currencies['sell']}</i>";
//        $text .= "\n<i>Заявок на покупку: {$currencies['forBuy']}</i>";
//        $text .= "\n<i>Заявок на продажу: {$currencies['forSell']}</i>";
//        $text .= "\n<i>Покупка: {$currencies['buying']}</i>";
//        $text .= "\n<i>Продажа: {$currencies['selling']}</i>";

//        foreach ($currencies['deals_list']['buy'] as $deal){
//            $buy .= "<i>Время:</i> {$deal['time']}\n";
//            $buy .= "<i>Валюта:</i> {$deal['currency']}\n";
//            $buy .= "<i>Сумма:</i> {$deal['sum']}\n";
//            $buy .= "<i>Сообщение:</i> {$deal['msg']}\n";
//            $buy .= "___________________________\n";
//        }
//        foreach ($currencies['deals_list']['sell'] as $deal){
//            $sell .= "<i>Время:</i> {$deal['time']}\n";
//            $sell .= "<i>Валюта:</i> {$deal['currency']}\n";
//            $sell .= "<i>Сумма:</i> {$deal['sum']}\n";
//            $sell .= "<i>Сообщение:</i> {$deal['msg']}\n";
//            $sell .= "___________________________\n";
//        }


        var_dump(\Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => 'Валюта',
            'parse_mode' => 'HTML',
            'reply_markup' => $this->keyboard()
        ]));
    }


    public function keyboard() {
        $menuEmoji = \Yii::$app->params['emoji']['menu'];
        $btns = [
            ['USD', 'EUR', 'RUB'],
            [json_decode('"'.$menuEmoji['back'].'"') .' '. \Yii::t('app', 'back')]
        ];
        $keyboard = ["keyboard" => $btns, "resize_keyboard" => true];

        return json_encode($keyboard);
    }
}