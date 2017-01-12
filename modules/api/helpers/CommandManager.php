<?php

namespace app\modules\api\helpers;

use app\modules\api\commands\BaseCommand;
use app\modules\api\models\StateStorage;
use app\modules\api\models\Users;

class CommandManager {

    /**
     * @var $user \app\modules\api\models\Users
     */
    public $user;
    
    public function __construct(Users $user = null){
        $this->user = $user;
    }

    public $commandClassNamespace = '\app\modules\api\commands\\';

    private $_commands = [
        '/hello' => 'HelloCommand',
        '/start' => 'StartCommand',
        '/help' => 'HelpCommand',
        '/settings' => 'SettingsCommand',
        '/city' => 'SetCityCommand',
        '/language' => 'SetLangCommand',
        '/usd' => 'ShowUsdCommand',
        '/eur' => 'ShowEurCommand',
        '/rub' => 'ShowRubCommand',
        '/cards' => 'CardsCommand',
        '/bankcourses' => 'BanksCoursesCommand',
        '/mb' => 'MbCommand',
        '/currencyauction' => 'CurrencyAuctionCommand',
        '/notifications' => 'NotificationsCommand',
        '/setusdnotification' => 'SetUSDNotificationCommand',
        '/seteurnotification' => 'SetEURNotificationCommand',
        '/setrubnotification' => 'SetRUBNotificationCommand',
        '/setmbnotification' => 'SetMBNotificationCommand',
        '/setcardsnotification' => 'SetCardsNotificationCommand',
        '/setbankscoursenotification' => 'SetBanksCourseNotificationCommand',
    ];

    public $command;

    public $answer;

    public function prepare($inputText){

        if (StateStorage::isAnswer($this->user->id)) {
            $this->answer = $inputText;
            if(mb_strpos($this->answer, \Yii::t('app', 'back')) !== false) {
                StateStorage::unsetIsAnswer($this->user->id);
                StateStorage::removeLastCommand($this->user->id);
                $this->answer = null;
            }
            $command = StateStorage::getCurrentState($this->user->id);
        } else {
            $command = $inputText;
            if(mb_strpos($inputText, \Yii::t('app', 'back')) !== false) {
                StateStorage::removeLastCommand($this->user->id);
                $command = StateStorage::getCurrentState($this->user->id);
            }
        }
        
        $this->command = $command;
    }

    /**
     * @param $command string
     * @param null $answer
     * @throws \Exception
     */
    public function createCommand($inputText){
        $this->prepare($inputText);
        $command = $this->getCommandAlias($this->command);
        $commandObj = $this->instance($command, $this->answer);
        if(!$commandObj) return;
        if($commandObj instanceof BaseCommand){
            StateStorage::setState($command, $this->user->id);
            $commandObj->execute();
        } else {
            throw new \Exception('Command class must extends BaseCommand');
        }
    }

    public function instance($command, $answer = null){
        if($command) {
            $classNamespace = $this->commandClassNamespace . $this->_commands[$command];
            if(class_exists($classNamespace)){
                return new $classNamespace($this->user, $answer, $this);
            } else {
                throw new \Exception('Class not exists');
            }
        }

        return false;
    }

    public function getDefaultCommand($inputCommand){
        foreach ($this->_commands as $command => $class) {
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
    public function getCommandAlias($inputCommand){
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
    protected function searchCommand($searchCommand, $searchWhere){
        return preg_match("~(\B)?$searchCommand\b~iu", $searchWhere);
    }
    

}