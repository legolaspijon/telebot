<?php

namespace app\modules\api\commands;

use app\modules\api\models\StateStorage;
use app\modules\api\models\Users;

class SetCityCommand extends BaseCommand{

    public function execute(){
        $menuEmoji = \Yii::$app->params['emoji']['menu'];

        if($this->answer) {
            $this->setCity($this->answer);
        } else {
            StateStorage::setIsAnswer($this->user->id);
            foreach (\Yii::$app->params['cities'] as $city){
                $btn[] = [$city];
            }
            array_unshift($btn, [json_decode('"'.$menuEmoji['back'].'"') .' '. \Yii::t('app', 'back')]);
            $keyboard = json_encode(['keyboard' => $btn, 'resize_keyboard' => true, 'one_time_keyboard' => true]);

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $this->user->chat_id,
                'text' => \Yii::t('app', 'Select the city'),
                'reply_markup' => $keyboard,
            ]);
        }
    }

    private function setCity($message){
        $city = mb_strtolower(trim($message));
        $fc = mb_strtoupper(mb_substr($city, 0, 1));
        $city = $fc . mb_substr($city, 1);

        if(!in_array($city, \Yii::$app->params['cities'])) {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $this->user->chat_id,
                'text' => \Yii::t('app', 'City {text} does not exist', ['text' => $city]),
            ]);

            return;
        }
        
        if(($this->bot->user = Users::setOption('city', $city, $this->user->chat_id)) !== false){

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $this->user->chat_id,
                'text' => \Yii::t('app', 'City {text} was successfully set...', ['text' => $city]),
            ]);

            StateStorage::unsetIsAnswer($this->user->id);
            StateStorage::removeLastCommand($this->user->id);
            $this->bot->createCommand('/start', null);
        } else {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' =>  \Yii::t('app', 'City {text} is not set, something wrong... try again', ['text' => $city]),
            ]);
        }
    }
}