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
     * Default Command
     * @var $defaultCommand string
     * */
    public $defaultCommand = '';

    /**
     * Default language
     * @var $defaultCommand string
     * */
    public $defaultLang = 'en';

    /**
     * Default measurement
     * @var $defaultMeasurement string
     * */
    public $defaultMeasurement = 'C';

    /**
     * current command
     * @var $currentCommand string
     * */
    public $currentCommand;

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
        'settings' => 'SettingsCommand',
        'measurement' => 'SetMeasurementCommand',
        'city' => 'SetCityCommand',
        'language' => 'SetLangCommand',
        'notification' => 'SetNotificationCommand',
        '/back' => 'BackCommand',

    // Show weather for:
        'today' => 'ShowWeatherTodayCommand',
        'tomorrow' => 'ShowWeatherTomorrowCommand',
        '5 days' => 'ShowWeatherFiveCommand',

    //
        '/hello' => 'HelloCommand',
        '/start' => 'StartCommand',
        '/help' => 'HelpCommand',
    ];

    /**
     * @var $user Users
     * */
    public $user;


    public function beforeAction($action)
    {
//        \Yii::$app->session->remove('state');
//        \Yii::$app->session->remove('isAnswer');exit;
        $update = \Yii::$app->telegram->getUpdates()->result;
        $this->update = array_pop($update);
        $this->user = Users::findOne(['chat_id' => $this->update->message->chat->id]);
        if($this->user === null){
            $user = new Users();
            $user->lang = $this->defaultLang;
            $user->measurement = $this->defaultMeasurement;
            $user->chat_id = $this->update->message->chat->id;
            $user->first_name = $this->update->message->from->first_name;
            $user->last_name = $this->update->message->from->first_name;
            $user->save();
            \Yii::$app->language = $this->defaultLang;
        } else {
            \Yii::$app->language = $this->user->lang;
        }

        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }


    /**
     * Telegram send own updates here
     * */
    public function actionWebHook()
    {
        $answer = null;
        if($this->isAnswer()){
            $command = $this->getCurrentState();
            $answer = $this->update->message->text;
        } else {
            $command = $this->update->message->text;
        }

        $this->createCommand($command, $answer);
    }

    public function isAnswer(){
        return \Yii::$app->session->get('isAnswer', false);
    }

    /**
     * @var $text string
     * @var $answer string
     * @throws Exception
     * */
    protected function createCommand($c, $answer = null)
    {
        $command = $this->checkCommand($c, $answer);
        if($answer == 'back') {
            \Yii::$app->session->remove('isAnswer');
        }

        if (!$command) return;
        if ($command instanceof BaseCommand) {
            $this->setStates();
            var_dump(\Yii::$app->session->get('state'));
            $command->execute();
        } else {
            throw new Exception('Command class must extends BaseCommand');
        }
    }

    /**
     * @param $commands array
     * @param $answer string
     * @return BaseCommand|false
     * */
    public function checkCommand($commands, $answer = null)
    {
        if ($commands == null) {
            return false;
        }
        if ($commands == 'back' || $answer == 'back') {
            $commands = $this->getStateBefore();
            $answer = null;
        }

        foreach ($this->commands as $command => $class) {
            $commands = strtolower($commands);
            if (preg_match("~(\B)?$command\b~", $commands)) {
                $this->currentCommand = $command;
                $classNamespace = $this->commandClassNamespace . $this->commands[$command];

                return new $classNamespace($this->update, $this->user, $answer);
            }
        }
        return false;
    }

    protected function getCurrentState(){
        $session = \Yii::$app->session;
        $state = $session->get('state', [null]);
        return end($state);
    }

    protected function getStateBefore(){
        $session = \Yii::$app->session;
        $state = $session->get('state', [null]);

        return array_shift($state);
    }

    protected function setStates(){
        $state = null;
        $session = \Yii::$app->session;
        if ($session->has('state')) {
            $state = $session->get('state');
            if (count($state) > 1 && $state != $this->currentCommand) {
                array_shift($state);
                array_push($state, $this->currentCommand);
            } else {
                array_push($state, $this->currentCommand);
            }

            $session->set('state', $state);
            return;
        }
        $session->set('state', [$this->currentCommand]);
    }
}