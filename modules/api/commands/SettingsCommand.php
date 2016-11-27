<?php

namespace app\modules\api\commands;

class SettingsCommand extends BaseCommand
{
    public function execute()
    {
        $message = isset($this->update->callback_query) ? $this->update->callback_query->message : $this->update->message;

        $opz = [
            ['back'],
            ["city", "measurement"],
            ["lang", "notification"],
        ];

        $keyboard = ["keyboard" => $opz, 'one_time_keyboard' => true, 'resize_keyboard' => true];
        $keyboard = json_encode($keyboard);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'Выберите настройку',
            'reply_markup' => $keyboard
        ]);
    }
}