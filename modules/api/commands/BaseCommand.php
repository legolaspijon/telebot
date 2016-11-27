<?php

namespace app\modules\api\commands;

abstract class BaseCommand {

    /**
     * @param $args array of text & beforeCommand
     * */
    public $answer;

    /**
     * @param $update array of response
     * */
    public $update;

    public function __construct($update, $answer = null)
    {
        $this->answer = $answer;
        $this->update = $update;
    }

    abstract public function execute();

    protected function setBeforeCommand($command){
        $session = \Yii::$app->session;
        $session->set('beforeCommand', $command);
    }

    protected function unsetBeforeCommand(){
        $session = \Yii::$app->session;
        $session->remove('beforeCommand');
    }
}