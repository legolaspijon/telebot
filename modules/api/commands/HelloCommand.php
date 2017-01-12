<?php
namespace app\modules\api\commands;

class HelloCommand extends BaseCommand {

    public function execute()
    {

        $btns = [
            [['text' => 'summary', 'callback_data' => 'qwerty']],
        ];
        $keyboard = ['inline_keyboard' => $btns];

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->user->chat_id,
            'text' => 'hello command executed',
            'reply_markup' => json_encode($keyboard),
        ]);
    }
}