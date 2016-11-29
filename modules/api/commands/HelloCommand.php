<?php
namespace app\modules\api\commands;

use app\modules\api\commands\BaseCommand;

class HelloCommand extends BaseCommand {

    public function execute()
    {
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => 'hello command executed',
        ]);
    }
}