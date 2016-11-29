<?php

namespace app\modules\api\commands;

class BackCommand extends BaseCommand {

    public function execute()
    {
        $controller = \Yii::$app->controller;
        $beforeCommandInstance = $controller->checkCommand([$controller->getBeforeCommand()]);
        if($beforeCommandInstance) $beforeCommandInstance->execute();
    }

}