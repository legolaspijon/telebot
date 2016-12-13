<?php

namespace app\modules\api\commands;

class StartCommand extends BaseCommand
{
    public function execute()
    {
        $btnLabels = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        $message = $this->update->message;
        $text =         \Yii::t("app", "What weather do you want to see???");
        $text .= "\n".  \Yii::t("app", "For looking supported command list use /help");

        $btns = [[$btnLabels['/today'], $btnLabels['/tomorrow']],[$btnLabels['/5days'], $btnLabels['/settings']]];
        $keyboard = ['keyboard' => $btns, 'resize_keyboard' => true];
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => $text,
            'reply_markup' => json_encode($keyboard)
        ]);
    }
}