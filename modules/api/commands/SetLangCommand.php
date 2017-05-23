<?php

namespace app\modules\api\commands;

use app\modules\api\models\StateStorage;
use app\modules\api\models\Users;

class SetLangCommand extends BaseCommand{

    public function execute(){
	    $emoji = \Yii::$app->params['emoji']['menu'];
        if($this->answer) {
            if($this->setLang($this->answer)) {
                StateStorage::unsetIsAnswer($this->user->id);
                StateStorage::removeLastCommand($this->user->id);
                $btn = [[json_decode('"'.$emoji['back'].'"') .' '. \Yii::t('app', 'back')]];
                \Yii::$app->telegram->sendMessage([
                    'chat_id' => $this->user->chat_id,
                    'text' => \Yii::t("app", '{language} language was set successfully...', ['language' => $this->answer]),
                    'reply_markup' => $this->keyboard($btn)
                ]);
                $this->bot->createCommand('/start', null, 1);
            } else {
                \Yii::$app->telegram->sendMessage([
                    'chat_id' => $this->user->chat_id,
                    'text' => \Yii::t("app", '{language} language was not set, something wrong...', ['language' => $this->answer]),
                ]);
            }
        } else {
            StateStorage::setIsAnswer($this->user->id);
	        $btn = [[json_decode('"'. $emoji['back'] .'"') .' '. \Yii::t('app', 'back')], array_values(\Yii::$app->params['languages'])];
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $this->user->chat_id,
                'text' => \Yii::t("app", "Choose language..."),
                'reply_markup' => $this->keyboard($btn)
            ]);
        }
    }

    protected function keyboard($btn) {
        return json_encode(['keyboard' => $btn, "resize_keyboard" => true, 'one_time_keyboard' => true]);
    }

    protected function setLang($message){
        $lang = array_search($message, \Yii::$app->params['languages']);
        if(Users::setOption('lang', $lang, $this->user->chat_id)){
            \Yii::$app->language = $lang;
            return true;
        }

        return false;
    }
}