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
     * @var app\modules\api\components\CommandManager
     */
    public $manager;

    /**
     * Default language
     * @var $defaultCommand string
     * */
    public $defaultLang = 'ru';

    /**
     * last updates
     * @var $update array
     * */
    public $update;

    /**
     * command list
     * */
    public $commands = [
        '/hello' => 'HelloCommand',
        '/start' => 'StartCommand',
        '/help' => 'HelpCommand',
        // Settings
        '/settings' => 'SettingsCommand',
        '/city' => 'SetCityCommand',
        '/language' => 'SetLangCommand',
        // courses
        '/usd' => 'ShowUsdCommand',
        '/eur' => 'ShowEurCommand',
        '/rub' => 'ShowRubCommand',
        '/cards' => 'CardsCommand',
        '/bankcourses' => 'BanksCoursesCommand',
        '/mb' => 'MbCommand',
        '/currencyauction' => 'CurrencyAuctionCommand',
    ];

    /**
     * @var $user Users
     * */
    public $user;


    public function beforeAction($action) {

        try{
//            $this->update = \Yii::$app->telegram->hook();
            $update = \Yii::$app->telegram->getUpdates()->result;
            $this->update = array_pop($update);
            if(is_object($this->update)){
                $this->setUser();
            } else {
                throw new \Exception('update is empty');
            }
            \Yii::$app->language = ($this->user) ? $this->user->lang : $this->defaultLang;
        }catch(\Exception $e){
            \Yii::trace($e->getMessage(), 'debug');
        }

        //call_user_func([$this, $action->actionMethod]);\
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
            $this->createCommand($command, $answer);
        }catch(\Exception $e){
            \Yii::trace($e->getMessage(), 'debug');
        }

        exit;
    }

    /**
     * @var $c string
     * @var $answer string
     * @throws Exception
     * */
    public function createCommand($inputCommand, $answer = null, $redirect = false) {
        if($redirect) $this->setUser();

        $command = $this->checkCommand($inputCommand, $answer);
        if (!$command) return;
        if ($command instanceof BaseCommand) {
            StateStorage::setState($inputCommand, $this->user->id);
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
            $classNamespace = $this->commandClassNamespace . $this->commands[$command];
            if(class_exists($classNamespace)){
                return new $classNamespace($this->update, $this->user, $answer, $this);
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

    /**
     * @param $inputCommand
     * @return string|false
     */
    public function getCommandAlias($inputCommand) {
        foreach (\Yii::$app->params['commandsLabels'][\Yii::$app->language] as $command => $locale) {
            if ($this->searchCommand($locale, $inputCommand)) {
                return $command;
            }
        }
        return $this->getDefaultCommand($inputCommand);
    }

    /**
     * Search command in query
     *
     * @param $searchCommand
     * @param $searchWhere
     * @return bool
     */
    protected function searchCommand($searchCommand, $searchWhere) {
        return preg_match("~(\B)?$searchCommand\b~iu", $searchWhere);
    }


    /**
     * Set user if not exist
     */
    protected function setUser(){
        $user = Users::findOne(['chat_id' => $this->update->message->chat->id]);
        if(!$user) {
            $user = new Users([
                'lang' => $this->defaultLang,
                'measurement' => $this->defaultMeasurement,
                'chat_id' => $this->update->message->chat->id,
                'first_name' => $this->update->message->from->first_name,
                'last_name' => $this->update->message->from->last_name,
            ]);
            $user->save();

            $state = new StateStorage(['user_id' => $user->id]);
            $state->save();
        }
        $this->user = $user;
    }
}
