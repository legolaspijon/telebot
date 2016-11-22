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

    public $commands = [
        '/start' => 'StartCommand',
        '/hello' => 'HelloCommand',
        '/getweather' => 'GetWeatherCommand',
        '/plz' => 'PlzCommand'
    ];

    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    public function actionWebHook()
    {
        $update = \Yii::$app->telegram->getUpdates()->result;
        $this->update = array_pop($update);
        var_dump($this->update);exit;
        if(isset($this->update->callback_query)){
            $text = $this->update->callback_query->data;
        } else {
            $text = $this->update->message->text;
        }
        $this->createCommand($text);
    }


    protected function createCommand($text)
    {
        $segments = explode(' ', $text);
        $command = $this->checkCommand($segments);
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
    protected function checkCommand($commands)
    {
        foreach ($commands as $command) {
            if (array_key_exists($command, $this->commands)) {
                $ClassNamespace = $this->commandClassNamespace . $this->commands[$command];
                return new $ClassNamespace($this->update);
            }
        }

        $defaultCommand = $this->commandClassNamespace . $this->defaultCommand;
        return new $defaultCommand($this->update);
    }

}