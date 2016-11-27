<?php

namespace app\modules\api\commands;

class StartCommand extends BaseCommand
{
    public function execute()
    {
        $message = $this->update->message;
        
        $btns = [
            ['today', 'tomorrow'],
            ['5 days', 'settings']
        ];
        $keyboard = ['keyboard' => $btns, 'one_time_keyboard' => true, 'resize_keyboard' => true];

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'What weather do you want to see???',
            'reply_markup' => json_encode($keyboard)
        ]);
    }
}