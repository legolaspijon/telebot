<?php

namespace app\modules\api\commands;

use app\modules\api\models\Users;

class SetCityCommand extends BaseCommand{

    public function execute(){
        var_dump(1);
        $message = $this->update->message;

        if($this->answer) {
            $this->setCity($this->update->message);
            $this->unsetBeforeCommand();
        } else {
            $this->setBeforeCommand($this->update->message->text);

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'Choose city',
                'reply_markup' => false,
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


    protected function setCity($message){
        echo $message->text . ": ";
        if(!$this->checkCity($message->text)) {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'City ' . $message->text . ' does not exist',
            ]);
            return;
        }

        $user = Users::findOne(['chat_id' => $message->chat->id]);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'City ' . $message->text . ' was successfully set...',
        ]);
    }
}