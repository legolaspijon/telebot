<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\StateStorageHelper;
use app\modules\api\models\Users;

class SetLangCommand extends BaseCommand{

    public function execute(){

        $message = $this->update->message;

        if($this->answer) {
            $this->setLang($message);
        } else {
            StateStorageHelper::setIsAnswer();
            $btn = [['back'], array_values(\Yii::$app->params['languages'])];
            $keyboard = json_encode(['keyboard' => $btn, "resize_keyboard" => true]);
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t("app", "Choose language..."),
                'reply_markup' => $keyboard
            ]);
        }
    }

    protected function setLang($message){
        if(Users::setOption('lang', \Yii::$app->params[$message->text], $message->chat->id)){
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'Language ' . $message->text . ' was set successfully...',
            ]);

            StateStorageHelper::unsetIsAnswer();
            return true;
        }

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'Lang ' . $message->text . ' was not set, something wrong...',
        ]);

        return false;
    }
}