<?php

namespace app\modules\api\commands;

use app\modules\api\models\StateStorage;
use app\modules\api\models\Users;

class SetLangCommand extends BaseCommand{

    public function execute(){
        $message = $this->update->message;
        if($this->answer) {
            if($this->setLang($message)) {
                StateStorage::unsetIsAnswer($this->user->id);
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
            StateStorage::setIsAnswer($this->user->id);
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t("app", "Choose language..."),
                'reply_markup' => $this->keyboard()
            ]);
        }
    }

    protected function keyboard() {
        $emodji = \Yii::$app->params['emoji']['menu'];
        $btn = [[json_decode('"'.$emodji['back'].'"') .' '. \Yii::t('app', 'back')], array_values(\Yii::$app->params['languages'])];

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