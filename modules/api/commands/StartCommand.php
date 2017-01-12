<?php

namespace app\modules\api\commands;

class StartCommand extends BaseCommand
{
    public function execute()
    {
        $btnLabels = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        
        if (is_null($this->user->city)) {
            $this->bot->createCommand('/city');
        } else {
            $text = "http://minfin.com.ua/currency/auction/usd/buy/all/\n"
                    ."Валютный аукцион доллара/евро/рубля.\n"
                    ."Курс доллара/евро/рубля на валютном аукционе Украины.\n"
                    ."Валютный аукцион в Украине: курс покупки доллара/евро/рубля на валютном аукционе.\n";

            $btns = [
                [$btnLabels['/currencyauction'], $btnLabels['/mb']],
                [$btnLabels['/bankcourses'], $btnLabels['/cards']],
                [$btnLabels['/settings']]
            ];
            $keyboard = ['keyboard' => $btns, 'resize_keyboard' => true];

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $this->user->chat_id,
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($keyboard),
            ]);
        }
    }
}