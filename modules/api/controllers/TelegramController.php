<?php

namespace app\modules\api\controllers;

use app\modules\api\commands\BaseCommand;
use app\modules\api\models\Users;
use yii\base\Exception;
use yii\web\Controller;

class TelegramController extends Controller
{

    /**
     * namespace where commands places
     * @var $commandClassNamespace string
     * */
    public $commandClassNamespace = '\app\modules\api\commands\\';

    /**
     * command which use by default
     * @var $defaultCommand string
     * */
    public $defaultCommand = '';

    /**
     * last updates
     * @var $update array
     * */
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
        '/back' => 'BackCommand',

        // Show weather for:
        '/today' => 'ShowWeatherCommand',
        '/tomorrow' => 'ShowWeatherCommand',
        '/5 day' => 'ShowWeatherCommand',
        
        //
        '/hello' => 'HelloCommand',
        '/start' => 'StartCommand'
    ];

    public function beforeAction($action)
    {
        $update = \Yii::$app->telegram->getUpdates()->result;
        $this->update = array_pop($update);
        $user = new Users();
        $searchUser = $user->findOne(['chat_id' => $this->update->message->chat->id]);

        if($searchUser === null){
            $user->lang = 'en';
            $user->chat_id = $this->update->message->chat->id;
            $user->username = $this->update->message->from->first_name;
            $user->save();
        } else {
            \Yii::$app->language = $searchUser->lang;
        }

        return parent::beforeAction($action);
    }


    /**
     * Telegram send own updates here
     * */
    public function actionWebHook()
    {

        var_dump(file_put_contents(\Yii::getAlias('@webroot') . '/test.txt', print_r($this->update, true)));
        $answer = null;
        if(isset($this->update->callback_query)){
            $command = $this->update->callback_query->data;
        } elseif($command = $this->getBeforeCommand()){
            $answer = $this->update->message->text;
        } else {
            $command = $this->update->message->text;
        }

        $this->createCommand($command, $answer);
    }


    /**
     * @var $text string
     * @var $answer string
     * */
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
    public function checkCommand($commands, $answer = null)
    {
        foreach ($commands as $command) {
            $command = strtolower($command);
            $command = ((substr($command, 0, 1) == '/') ? '' : '/') . $command;

            if (array_key_exists($command, $this->commands)) {
                $ClassNamespace = $this->commandClassNamespace . $this->commands[$command];
                var_dump($ClassNamespace);
                return new $ClassNamespace($this->update, $answer);
            }
        }

        return false;
    }

    protected function getBeforeCommand(){
        $session = \Yii::$app->session;
        return $session->has('beforeCommand') ? $session->get('beforeCommand') : false;
    }
}