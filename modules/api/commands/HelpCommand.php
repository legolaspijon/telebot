<?php


namespace app\modules\api\commands;

class HelpCommand extends BaseCommand {

    public function execute()
    {
        echo "sadada";
        $text = "Supported commands";
        $text .= "\n/settings - setting mode";
        $text .= "\n/help - setting mode";

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text
        ]);
    }
}