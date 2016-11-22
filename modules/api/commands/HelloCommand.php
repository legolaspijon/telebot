<?php

namespace app\modules\api\commands;

class HelloCommand extends BaseCommand{

    public function execute(){
        $opz = [
            [
                ["text"=>"hello btn1", "callback_data"=>"/hello"],
                ["text"=>"hello btn2", "callback_data"=>"data666"],
                ["text"=>"hello btn3", "callback_data"=>"data666"],
            ],
        ];

        $message = isset($this->update->callback_query) ?
            $this->update->callback_query->message : $this->update->message;

        $keyboard = array("inline_keyboard" => $opz, 'one_time_keyboard' => true);
        $keyboard = json_encode($keyboard);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'hello',
            'reply_markup' => $keyboard
        ]);
    }
}