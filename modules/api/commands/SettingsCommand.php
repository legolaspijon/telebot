<?php

namespace app\modules\api\commands;

class SettingsCommand extends BaseCommand
{
    public function execute()
    {
        $message = isset($this->update->callback_query) ? $this->update->callback_query->message : $this->update->message;

        $opz = [
            ["Back"],
            ["City", "Measurement"],
            ["Lang", "Notification"],
        ];

        $keyboard = ["keyboard" => $opz, "resize_keyboard" => true];
        $keyboard = json_encode($keyboard);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'Select an option',
            'reply_markup' => $keyboard
        ]);
    }
}