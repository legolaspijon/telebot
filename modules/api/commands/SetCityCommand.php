<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\StateStorageHelper;
use app\modules\api\models\Users;

class SetCityCommand extends BaseCommand{

    public function execute(){
        $btnLabel = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        $message = $this->update->message;
        if($this->answer) {
            $this->setCity($this->update->message);
        } else {
            StateStorageHelper::setIsAnswer();
            $btn = [[$btnLabel['/back']]];
            $keyboard = json_encode(['keyboard' => $btn, 'resize_keyboard' => true]);

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t('app', 'Enter the city'),
                'reply_markup' => $keyboard,
            ]);
        }
    }

    private function setCity($message){

        if(!$this->checkCity($message->text)) {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t('app', 'City {text} does not exist', ['text' => $message->text]),
            ]);
            return;
        }

        if(Users::setOption('city', $message->text, $message->chat->id)){
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t('app', 'City {text} was successfully set...', ['text' => $message->text]),
            ]);
            StateStorageHelper::unsetIsAnswer();
        } else {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' =>  \Yii::t('app', 'City {text} is not set, something wrong... try again', ['text' => $message->text]),

            ]);
        }
    }

    private function checkCity($city){
        $countries = simplexml_load_file('https://pogoda.yandex.ru/static/cities.xml');
        foreach ($countries->country as $cities) {
            foreach ($cities->city as $xmlcity) {
                if (mb_strtolower($xmlcity, "UTF-8") == mb_strtolower($city, "UTF-8")) {
                    return true;
                }
            }
        }

        return false;
    }
}