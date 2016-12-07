<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\StateStorageHelper;
use app\modules\api\models\Users;

class SetLangCommand extends BaseCommand{

    public function execute(){
        var_dump('qwerty');
        $message = $this->update->message;
        if($this->answer) {
            if($this->setLang($message)) {
                StateStorageHelper::unsetIsAnswer();
                \Yii::$app->telegram->sendMessage([
                    'chat_id' => $message->chat->id,
                    'text' => \Yii::t("app", '{language} language was set successfully...', ['language' => $message->text]),
                    'reply_markup' => $this->keyboard()
                ]);
            } else {
                \Yii::$app->telegram->sendMessage([
                    'chat_id' => $message->chat->id,
                    'text' => \Yii::t("app", '{language} language was not set, something wrong...', ['language' => $message->text]),
                ]);
            }
        } else {
            StateStorageHelper::setIsAnswer();
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t("app", "Choose language..."),
                'reply_markup' => $this->keyboard()
            ]);
        }
    }

    protected function keyboard() {
        $emodji = \Yii::$app->params['emoji']['menu'];
        $btnLabel = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        $btn = [[$btnLabel['/back']], array_values(\Yii::$app->params['languages'])];

        return json_encode(['keyboard' => $btn, "resize_keyboard" => true]);
    }

    protected function setLang($message){
        $lang = array_search($message->text, \Yii::$app->params['languages']);
        if(Users::setOption('lang', $lang, $message->chat->id)){
            \Yii::$app->language = $lang;
            return true;
        }

        return false;
    }
}