<?php

namespace app\modules\api\commands;

class StartCommand extends BaseCommand
{
    public function execute()
    {
        $opz = [
            [
                ["text" => "Выбор города", "callback_data" => "/setcity"],
                ["text" => "Выбор ед. измерения", "callback_data" => "/setmeasurement"],
            ],
            [
                ["text" => "Выбор Языка", "callback_data" => "/setlang"],
                ["text" => "Уведомления", "callback_data" => "/settimenotify"],
            ]
        ];

        $message = isset($this->update->callback_query) ?
            $this->update->callback_query->message : $this->update->message;

        $keyboard = array("inline_keyboard" => $opz, 'one_time_keyboard' => true);
        $keyboard = json_encode($keyboard);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => '!',
            'reply_markup' => $keyboard
        ]);
    }
}