<?php

namespace app\modules\api\commands;

use app\modules\api\models\StateStorage;
use app\modules\api\models\Users;

class SetCityCommand extends BaseCommand{

    public function execute(){
        $menuEmoji = \Yii::$app->params['emoji']['menu'];
        $message = $this->update->message;

        if($this->answer) {
            $this->setCity($this->update->message);
        } else {
            StateStorage::setIsAnswer($this->user->id);
            foreach (\Yii::$app->params['cities'] as $city){
                $btn[] = [$city];
            }
            array_unshift($btn, [json_decode('"'.$menuEmoji['back'].'"') .' '. \Yii::t('app', 'back')]);
            $keyboard = json_encode(['keyboard' => $btn, 'resize_keyboard' => true, 'one_time_keyboard' => true]);

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                //'text' => \Yii::t('app', 'Enter the city (in Russian)'),
                'text' => \Yii::t('app', 'Select the city'),
                'reply_markup' => $keyboard,
            ]);
        }
    }

    private function setCity($message){
        $city = mb_strtolower(trim($message->text));
        if(!$this->checkCity($city)) {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t('app', 'City {text} does not exist', ['text' => $city]),
            ]);

            return;
        }

        if(Users::setOption('city', $city, $message->chat->id)){
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t('app', 'City {text} was successfully set...', ['text' => $city]),
            ]);
            StateStorage::unsetIsAnswer($this->user->id);
            StateStorage::removeLastCommand($this->user->id);
            sleep(1);
            $this->bot->createCommand('/start', null, 1);
        } else {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' =>  \Yii::t('app', 'City {text} is not set, something wrong... try again', ['text' => $city]),

            ]);
        }
    }

    private function checkCity($city){
//        $countries = simplexml_load_file('https://pogoda.yandex.ru/static/cities.xml');
//        foreach ($countries->country as $cities) {
//            foreach ($cities->city as $xmlcity) {
//                if (mb_strtolower($xmlcity, "UTF-8") == mb_strtolower($city, "UTF-8")) {
//                    return true;
//                }
//            }
//        }
        //\Yii::trace(trim(strtolower($city)), 'debug');
        \Yii::trace('qwerty' . $city, 'debug');
        return in_array($city, \Yii::$app->params['cities']) ? true : false;
    }
}