<?php

namespace app\modules\api\commands;

class SetCityCommand extends BaseCommand{

    public function execute(){
        $message = isset($this->update->callback_query) ? $this->update->callback_query->message : $this->update->message;

        if($this->answer) {
            $this->setCity($this->update->message);
            $this->unsetBeforeCommand();
        } else {
            $this->setBeforeCommand($this->update->message->text);

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'Введите город',
            ]);
        }
    }

    protected function setCity($message){
        var_dump('Looking for ' . $message->text . ' city...');
        
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'City ' . $message->text . ' was successfully set...',
        ]);
    }
}