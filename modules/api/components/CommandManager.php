<?php

namespace app\modules\api\commands;

use yii\base\Component;

class CommandManager extends Component {

    public $instances;

    private $_commands = [
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
     * @param $command string
     * @throws \Exception
     */
    public function createCommand($command){

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

}