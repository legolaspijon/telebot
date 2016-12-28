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
        $text.= "\n/usd - Доллар на валютном аукционе";
        $text.= "\n/eur - Евро на валютном аукционе";
        $text.= "\n/rub - Рубль на валютном аукционе";
        $text.= "\n/mb - Курсы на межбанке";
        $text.= "\n/cards - Курс конвертации банковских карт";
        $text.= "\n/bankcourses - Курс валют в банках Украины на сегодня";
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