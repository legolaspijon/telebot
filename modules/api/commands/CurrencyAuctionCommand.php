<?php

namespace app\modules\api\commands;

class CurrencyAuctionCommand extends BaseCommand {

    public function execute()
    {
        $btnLabels = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        $btns = [[$btnLabels['/start']], [$btnLabels['/usd'], $btnLabels['/eur'], $btnLabels['/rub']]];
        $keyboard = ['keyboard' => $btns, 'resize_keyboard' => true];

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->user->chat_id,
            'text' => 'Выберите валюту',
            'reply_markup' => json_encode($keyboard)
        ]);
    }
}