<?php

namespace app\modules\api\commands;

use app\modules\api\models\Users;

class SetCityCommand extends BaseCommand{

    public function execute(){;
        $message = $this->update->message;

        if($this->answer) {
            $this->setCity($this->update->message);
        } else {
            $this->setIsAnswer();
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'Enter the city',
                'reply_markup' => false,
            ]);
        }
    }

    protected function setCity($message){

        if(!$this->checkCity($message->text)) {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'City ' . $message->text . ' does not exist',
            ]);
            return;
        }

        if(Users::setOption('city', strtolower($message->text), $message->chat->id)){
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'City ' . $message->text . ' was successfully set...',
            ]);
            $this->unsetIsAnswer();
        } else {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'City ' . $message->text . ' is not set, something wrong... try again',
            ]);
        }

    }

    public function checkCity($city){
        $countries = simplexml_load_file('https://pogoda.yandex.ru/static/cities.xml');
        foreach ($countries->country as $cities) {
            foreach ($cities->city as $xmlcity)
                if(mb_strtolower($xmlcity) == mb_strtolower($city)) {
                    return true;
                }
        }
        return false;
    }
}