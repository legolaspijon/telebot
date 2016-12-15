<?php

namespace app\modules\api\controllers;

use app\modules\api\commands\BaseCommand;
use app\modules\api\models\StateStorage;
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

        try{
            $this->update = \Yii::$app->telegram->hook();
            //$this->update = json_decode(file_get_contents('php://input'));
//            $this->update = \Yii::$app->telegram->getUpdates()->result;
//            $this->update = array_pop($this->update);

            if(is_object($this->update)){
                $user = Users::findOne(['chat_id' => $this->update->message->chat->id]);
                if(!$user) {
                    $user = new Users([
                        'lang' => $this->defaultLang,
                        'measurement' => $this->defaultMeasurement,
                        'chat_id' => $this->update->message->chat->id,
                        'first_name' => $this->update->message->from->first_name,
                        'last_name' => $this->update->message->from->last_name,
                    ]);
                    if($user->save()) \Yii::trace('user saved', 'debug');
                    $state = new StateStorage(['user_id' => $user->id]);
                    $state->save();
                    \Yii::$app->language = $this->defaultLang;
                }
                $this->user = $user;
            } else {
                throw new \Exception('update is empty');
            }

            \Yii::$app->language = ($this->user) ? $this->user->lang : $this->defaultLang;
        }catch(\Exception $e){
            \Yii::trace($e->getMessage().' '.$e->getLine(). '<<< file:'. $e->getFile(), 'debug');
        }


        call_user_func([$this, $action->actionMethod]);
        return parent::beforeAction($action);
    }

    /**
     * Telegram send own updates here
     * */
    public function actionWebHook() {

        try{
            $answer = null;
            if (StateStorage::isAnswer($this->user->id)) {
                $answer = $this->update->message->text;

                if(mb_strpos($answer, \Yii::t('app', 'back')) !== false) {
                    StateStorage::unsetIsAnswer($this->user->id);
                    StateStorage::removeLastCommand($this->user->id);
                    $answer = null;
                }

                $command = StateStorage::getCurrentState($this->user->id);
            } else {
                if(mb_strpos($this->update->message->text, \Yii::t('app', 'back')) !== false) {
                    StateStorage::removeLastCommand($this->user->id);
                    $command = StateStorage::getCurrentState($this->user->id);
                } else {
                    $command = $this->getCommandAlias($this->update->message->text);
                }
            }

            if($command == '/start') {
                $this->setStart();
            }

            $this->createCommand($command, $answer);
        }catch(\Exception $e){
            \Yii::trace($e->getMessage().' '.$e->getLine().' '.$e->getFile(), 'debug');
        }

        exit;
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
            StateStorage::setState($c, $this->user->id, $this->isStart());
            $command->execute();
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
