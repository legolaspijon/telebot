<?php

namespace app\modules\api\controllers;

use app\modules\api\commands\BaseCommand;
use app\modules\api\helpers\StateStorageHelper;
use app\modules\api\helpers\YahooWeatherHelper;
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

        '/hello' => 'HelloCommand',
        '/start' => 'StartCommand',
        '/help' => 'HelpCommand',
    ];

    /**
     * @var $user Users
     * */
    public $user;


    public function beforeAction($action) {
//        var_dump(\Yii::$app->session->remove('state'));
//        var_dump(\Yii::$app->session->remove('isAnswer'));
//        var_dump(\Yii::$app->session->remove('user'));
//        exit;
        $update = \Yii::$app->telegram->getUpdates()->result;
        $this->update = array_pop($update);
        if(($this->user = unserialize(StateStorageHelper::getUser())) === false){
            $this->user = Users::findOne(['chat_id' => $this->update->message->chat->id]);
            StateStorageHelper::setUser($this->user);
        }
        if($this->user === null){
            (new Users([
                'lang' => $this->defaultLang,
                'measurement' => $this->measurement,
                'chat_id' => $this->chat_id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ]))->save();

            \Yii::$app->language = $this->defaultLang;
        } else {
            \Yii::$app->language = $this->user->lang;
        }

        return parent::beforeAction($action);
    }

    /**
     * Telegram send own updates here
     * */
    public function actionWebHook() {

        $answer = null;
        if (StateStorageHelper::isAnswer()) {
            $command = StateStorageHelper::getCurrentState();
            $answer = $this->update->message->text;
            if($answer == 'back') {
                StateStorageHelper::unsetIsAnswer();
            }
        } else {
            $command = $this->update->message->text;
        }

        $this->createCommand($command, $answer);
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
            StateStorageHelper::setStates($this->currentCommand);
            var_dump(\Yii::$app->session->get('state'));
            $command->execute();
        } else {
            throw new Exception('Command class must extends BaseCommand');
        }
    }

    /**
     * @param $inputCommand array
     * @param $answer string
     * @return BaseCommand|false
     * */
    public function checkCommand($inputCommand, $answer = null)
    {
        if (\Yii::$app->language == $this->defaultLang) {
            $command = $this->getDefaultCommand($inputCommand);
        } else {
            $localeCommand = $this->getLocaleCommand($inputCommand);
            $command = !$localeCommand ? $this->getDefaultCommand($inputCommand) : $localeCommand;
        }

        if ($inputCommand == 'back' || $answer == 'back') {
            $command = StateStorageHelper::getStateBefore();
            $answer = null;
        }

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


    public function getLocaleCommand($inputCommand) {
        foreach (\Yii::$app->params['commandsLabels'][\Yii::$app->language] as $command => $locale) {
            if ($this->searchCommand($locale, $inputCommand)) {
                return $command;
            }
        }
        return false;
    }

    public function searchCommand($searchCommand, $searchWhere) {
        return preg_match("~(\B)?$searchCommand\b~iu", $searchWhere);
    }
}