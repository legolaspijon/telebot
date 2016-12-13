<?php

namespace app\modules\api\controllers;

use app\modules\api\commands\BaseCommand;
use app\modules\api\helpers\StateStorageHelper;
use app\modules\api\models\Users;
use yii\base\Exception;
use yii\web\Controller;

class TelegramController extends Controller {
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
        '/settings' => 'SettingsCommand',
        '/measurement' => 'SetMeasurementCommand',
        '/city' => 'SetCityCommand',
        '/language' => 'SetLangCommand',
        '/notification' => 'SetNotificationCommand',

    // Show weather for:
        '/today' => 'ShowWeatherTodayCommand',
        '/tomorrow' => 'ShowWeatherTomorrowCommand',
        '/5days' => 'ShowWeatherFiveCommand',

        '/hello' => 'HelloCommand',
        '/start' => 'StartCommand',
        '/help' => 'HelpCommand',
    ];

    /**
     * @var $user Users
     * */
    public $user;


    public function beforeAction($action) {


//        var_dump(\Yii::$app->session->get('isAnswer'));
//        exit;

        // by webHook
        $this->update = \Yii::$app->telegram->hook();

        // by getUpdates
//        $this->update = \Yii::$app->telegram->getUpdates()->result;
//        $this->update = array_pop($this->update);

        $this->user = StateStorageHelper::getUser();
        if(!$this->user){
            $user = Users::findOne(['chat_id' => $this->update->message->chat->id]);
            if(!$user) {
                $user = new Users([
                    'lang' => $this->defaultLang,
                    'measurement' => $this->defaultMeasurement,
                    'chat_id' => $this->update->message->chat->id,
                    'first_name' => $this->update->message->from->first_name,
                    'last_name' => $this->update->message->from->last_name,
                ]);
                if(!$user->save()) \Yii::trace('user not save', 'debug');
                \Yii::$app->language = $this->defaultLang;
            }
            StateStorageHelper::setUser($user);
            $this->user = $user;
        }
        \Yii::trace(print_r($this->user, true), 'debug');
        \Yii::$app->language = ($this->user) ? $this->user->lang : $this->defaultLang;

        return parent::beforeAction($action);
    }

    /**
     * Telegram send own updates here
     * */
    public function actionWebHook() {

        $answer = null;

        if (StateStorageHelper::isAnswer()) {
            $answer = $this->update->message->text;

            if(mb_strpos($answer, \Yii::t('app', 'back')) !== false) {
                StateStorageHelper::unsetIsAnswer();
                StateStorageHelper::removeLastCommand();
                $answer = null;
            }

            $command = StateStorageHelper::getCurrentState();
        } else {
            if(mb_strpos($this->update->message->text, \Yii::t('app', 'back')) !== false) {
                StateStorageHelper::removeLastCommand();
                $command = StateStorageHelper::getCurrentState();
            } else {
                $command = $this->getCommandAlias($this->update->message->text);
            }
        }

        if($command == '/start') {
            $this->setStart();
        }

        $this->createCommand($command, $answer);
    }


    private $_start = false;

    private function setStart() {
        $this->_start = true;
    }

    public function isStart() {
        return $this->_start;
    }

    /**
     * @var $text string
     * @var $answer string
     * @throws Exception
     * */
    protected function createCommand($c, $answer = null) {
        $command = $this->checkCommand($c, $answer);
        if (!$command) return;
        if ($command instanceof BaseCommand) {
            StateStorageHelper::setStates($c, $this->isStart());
            $command->execute();
            var_dump(\Yii::$app->session->get('state'));
        } else {
            throw new Exception('Command class must extends BaseCommand');
        }
    }

    /**
     * @param $command string
     * @param $answer string
     * @return BaseCommand|false
     * */
    public function checkCommand($command, $answer = null)
    {
        if($command) {
            $this->currentCommand = $command;
            $classNamespace = $this->commandClassNamespace . $this->commands[$command];
            if(class_exists($classNamespace)){
                \Yii::trace('classNamespace: ' . $classNamespace, 'debug');
                return new $classNamespace($this->update, $this->user, $answer);
            }
        }

        return false;
    }

    public function getDefaultCommand($inputCommand) {
        foreach ($this->commands as $command => $class) {
            if ($this->searchCommand($command, $inputCommand)) {
                return $command;
            }
        }
        return false;
    }

    public function getCommandAlias($inputCommand) {
        foreach (\Yii::$app->params['commandsLabels'][\Yii::$app->language] as $command => $locale) {
            if ($this->searchCommand($locale, $inputCommand)) {
                return $command;
            }
        }
        return $this->getDefaultCommand($inputCommand);
    }

    public function searchCommand($searchCommand, $searchWhere) {
        return preg_match("~(\B)?$searchCommand\b~iu", $searchWhere);
    }
}
