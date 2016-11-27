<?php

namespace app\modules\api\controllers;

use app\modules\api\commands\BaseCommand;
use yii\base\Exception;
use yii\web\Controller;

class TelegramController extends Controller
{
    public $commandClassNamespace = '\app\modules\api\commands\\';
    public $defaultCommand = 'StartCommand';
    public $update;
    
    /**
     * command list
     * */
    public $commands = [
        // Settings
        '/settings' => 'SettingsCommand',
        '/city' => 'SetCityCommand',
        '/lang' => 'SetLangCommand',
        '/measurement' => 'SetMeasurementCommand',
        '/notification' => 'SetNotificationCommand',

        // Show weather for:
        '/today' => 'ShowWeatherCommand',
        '/tomorrow' => 'ShowWeatherCommand',
        '/5 day' => 'ShowWeatherCommand',
        
        //
        '/hello' => 'HelloCommand',
    ];

    public function beforeAction($action)
    {
            // if use WebHook
            // $update = \Yii::$app->telegram->getUpdates()->result;
        $update = \Yii::$app->telegram->getUpdates()->result;
        $this->update = array_pop($update);
        return parent::beforeAction($action);
    }

    public function actionWebHook()
    {
        var_dump($this->update->message->text);
        var_dump(file_put_contents(\Yii::getAlias('@webroot') . '/test.txt', print_r($this->update, true)));
        
        $answer = null;
        if(isset($this->update->callback_query)){
            $command = $this->update->callback_query->data;
        } elseif($command = $this->getAnswer()){
            $answer = $this->update->message->text;
        } else {
            $command = $this->update->message->text;
        }

        $this->createCommand($command, $answer);
    }

    protected function getAnswer(){
        $session = \Yii::$app->session;
        return $session->has('beforeCommand') ? $session->get('beforeCommand') : false;
    }

    protected function createCommand($text, $answer = null)
    {
        $segments = explode(' ', $text);
        $command = $this->checkCommand($segments, $answer);

        if (!$command) return;
        if ($command instanceof BaseCommand) {
            $command->execute();
        } else {
            throw new Exception('Command class must extends BaseCommand');
        }
    }

    /**
     * @param $commands array
     * @return BaseCommand|false
     * */
    protected function checkCommand($commands, $answer = null)
    {
        foreach ($commands as $command) {
            $command = strtolower($command);
            $command = ((substr($command, 0, 1) == '/') ? '' : '/') . $command;
            if (array_key_exists($command, $this->commands)) {
                $ClassNamespace = $this->commandClassNamespace . $this->commands[$command];
                return new $ClassNamespace($this->update, $answer);
            }
        }

        $defaultCommand = $this->commandClassNamespace . $this->defaultCommand;
        return new $defaultCommand($this->update);
    }

}