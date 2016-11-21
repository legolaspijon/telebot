<?php

namespace app\modules\api\controllers;
use yii\web\Controller;

class TelegramController extends Controller {

    public $commandClassNamespace = '\app\modules\api\commands\\';

    public $update;

    public $commands = [
        '/start' => 'StartCommand',
        '/hello' => 'HelloCommand',
        'привет' => 'HiCommand',
        '/getweather' => 'GetWeatherCommand'
    ];

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    public function actionWebHook()
    {
        $update = (array)\Yii::$app->telegram->getUpdates();
        $this->update = array_pop($update['result']);
        $this->parseRequest();
    }

    protected function parseRequest(){
        $segments = explode(' ', $this->update->message->text);

        foreach ($segments as $command) {
            if(array_key_exists($command, $this->commands)){
                $ClassNamespace = $this->commandClassNamespace . $this->commands[$command];
                $command = new $ClassNamespace($this->update);
                $command->execute();
            }
        }
    }

    protected function sendMessage($chat_id, $text){
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => $text,
        ]);
    }
}