<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\StateStorageHelper;
use app\modules\api\models\Users;

class SetLangCommand extends BaseCommand{

    public function execute(){
        $btnLabel = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        $message = $this->update->message;
        if($this->answer) {
            $this->setLang($message);
        } else {
            StateStorageHelper::setIsAnswer();
            $btn = [[$btnLabel['/back']], array_values(\Yii::$app->params['languages'])];
            $keyboard = json_encode(['keyboard' => $btn, "resize_keyboard" => true]);
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t("app", "Choose language..."),
                'reply_markup' => $keyboard
            ]);
        }
    }

    protected function setLang($message){
        $lang = array_search($message->text, \Yii::$app->params['languages']);
        if(Users::setOption('lang', $lang, $message->chat->id)){
            \Yii::$app->language = $lang;
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t("app", '{language} language was set successfully...', ['language' => $message->text]),
            ]);

            StateStorageHelper::unsetIsAnswer();
            return true;
        }

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => \Yii::t("app", '{language} language was not set, something wrong...', ['language' => $message->text]),
        ]);

        return false;
    }
}