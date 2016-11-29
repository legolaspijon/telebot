<?php

namespace app\modules\api\commands;

class StartCommand extends BaseCommand
{
    public function execute()
    {
        $message = $this->update->message;
        $text =         \Yii::t("app", "What weather do you want to see???");
        $text .= "\n".  \Yii::t("app", "For looking supported command list use /help");
        $btns = [['today', 'tomorrow'],['5 days', 'settings']];
        $keyboard = ['keyboard' => $btns, 'resize_keyboard' => true];

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => $text,
            'reply_markup' => json_encode($keyboard)
        ]);
    }
}