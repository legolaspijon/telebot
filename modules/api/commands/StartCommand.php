<?php

namespace app\modules\api\commands;

class StartCommand extends BaseCommand
{
    public function execute()
    {
        $btnLabels = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        $message = $this->update->message;

        if (is_null($this->user->city)) {
            $this->bot->createCommand('/city');
        } else {
            $text = \Yii::t("app", "For view supported command list use /help. Also you can start typing '<b>/</b>' for search command.");
            $text .= "\n\n" . \Yii::t("app", "What weather do you want to see???");

            $btns = [[$btnLabels['/today'], $btnLabels['/tomorrow']], [$btnLabels['/5days'], $btnLabels['/settings']]];
            $keyboard = ['keyboard' => $btns, 'resize_keyboard' => true];

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode($keyboard),
            ]);
        }
    }
}