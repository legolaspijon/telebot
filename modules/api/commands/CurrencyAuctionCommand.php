<?php

namespace app\modules\api\commands;

class CurrencyAuctionCommand extends BaseCommand {

    public function execute()
    {
        $btnLabels = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        $btns = [[$btnLabels['/start']], [$btnLabels['/usd'], $btnLabels['/eur'], $btnLabels['/rub']]];
        $keyboard = ['keyboard' => $btns, 'resize_keyboard' => true];

        \Yii::trace(print_r(\Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => 'Выберите валюту',
            'reply_markup' => json_encode($keyboard)
        ]), true), 'debug');
    }
}