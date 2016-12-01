<?php

namespace app\modules\api\controllers;

use app\modules\api\commands\BaseCommand;
use app\modules\api\helpers\StateStorageHelper;
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
    public function actionWebHook()
    {
        $answer = null;
        if(StateStorageHelper::isAnswer()){
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
    protected function createCommand($c, $answer = null)
    {
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
            $commands = StateStorageHelper::getStateBefore();
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
}