<?php

namespace app\modules\api\commands;

use app\modules\api\models\Users;

class SetLangCommand extends BaseCommand{

    public function execute(){
        $message = isset($this->update->callback_query) ? $this->update->callback_query->message : $this->update->message;

        if($this->answer) {
            $this->setLang($this->update->message);
            $this->unsetBeforeCommand();
        } else {
            $this->setBeforeCommand($this->update->message->text);

            $btn = [['back'], ['ru', 'en', 'de']];
            $keyboard = json_encode(['keyboard' => $btn, "resize_keyboard" => true]);

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'Choose language...',
                'reply_markup' => $keyboard
            ]);
        }
    }

    protected function setLang($message){
        $user = Users::findOne(['chat_id' => $message->chat->id]);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'City ' . $message->text . 'setting lang',
        ]);
    }
}