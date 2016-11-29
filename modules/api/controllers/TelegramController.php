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
        $this->currentCommand === 'back' ?: \Yii::$app->session->set('beforeCommand', $this->currentCommand);
        return parent::afterAction($action, $result);
    }


    /**
     * Telegram send own updates here
     * */
    public function actionWebHook()
    {
        //var_dump(file_put_contents(\Yii::getAlias('@webroot') . '/test.txt', print_r($this->update, true)));
        $answer = null;
        if($this->isAnswer()){
            $command = $this->getBeforeCommand();
            $answer = $this->update->message->text;
        } else {
            $command = $this->update->message->text;
        }

        try {
            $this->createCommand($command, $answer);
        }catch (Exception $e){
            echo $e;
        }
    }

    public function isAnswer(){
        return \Yii::$app->session->get('isAnswer', false);
    }

    /**
     * @var $text string
     * @var $answer string
     * @throws Exception
     * */
    protected function createCommand($text, $answer = null)
    {
        $command = $this->checkCommand($text, $answer);
        if (!$command) return;
        if ($command instanceof BaseCommand) {
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

    public function getBeforeCommand(){
        $session = \Yii::$app->session;
        return $session->has('beforeCommand') ? $session->get('beforeCommand') : false;
    }
}