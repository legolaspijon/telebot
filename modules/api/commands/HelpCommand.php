<?php


namespace app\modules\api\commands;

class HelpCommand extends BaseCommand {

    public function execute()
    {
        $text = \Yii::t('app', "Supported commands");
        $text.= "\n/start - ". \Yii::t('app', "main menu");
        $text.= "\n/help - ". \Yii::t('app', "view supported commands");
        $text.= "\n/settings - ". \Yii::t('app', "you can set language, city, units here");
        $text.= "\n/city - ". \Yii::t('app', "you can set city here, typing on the keyboard");
        $text.= "\n/language - ". \Yii::t('app', "you can choose language here");
        $text.= "\n/usd - ". \Yii::t('app', "usd info");
        $text.= "\n/eur - ". \Yii::t('app', "eur info");
        $text.= "\n/rub - ". \Yii::t('app', "rub info");
//        $text.= "\n/measurement - ". \Yii::t('app', "set units here");
//        $text.= "\n/today - ". \Yii::t('app', "today weather");
//        $text.= "\n/tomorrow - ". \Yii::t('app', "tomorrow weather");
//        $text.= "\n/5days - ". \Yii::t('app', "weather 5 days");

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text
        ]);
    }
}