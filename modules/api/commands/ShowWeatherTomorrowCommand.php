<?php

namespace app\modules\api\commands;

class ShowWeatherTomorrowCommand extends BaseCommand {

    public function execute()
    {
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => 'this is weather tomorrow'
        ]);
    }
}