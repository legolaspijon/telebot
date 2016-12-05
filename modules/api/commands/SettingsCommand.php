<?php

namespace app\modules\api\commands;

use app\modules\api\models\Users;

class SettingsCommand extends BaseCommand
{
    public function execute()
    {
        $btnLabels = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        $message = $this->update->message;
        $text = \Yii::t("app", "Current Settings:");
        $text .= "\n*". \Yii::t("app", "City") .":* " . $this->user->city;
        $text .= "\n*". \Yii::t("app", "Language") .":* " . $this->user->lang;
        $text .= "\n*". \Yii::t("app", "Units of measurement") .":* " . $this->user->measurement;
        $text .= "\n*". \Yii::t("app", "Notification") .":* " . 'notification here';
        $text .= "\n\n*". \Yii::t('app', "Select an option...") ."*";
        $opz = [['back'], [$btnLabels["/city"], $btnLabels["/measurement"]], [$btnLabels["/language"], $btnLabels["/notification"]]];
        $keyboard = ["keyboard" => $opz, "resize_keyboard" => true];
        $keyboard = json_encode($keyboard);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyboard
        ]);
    }
}