<?php

namespace app\modules\api\commands;

class BackCommand extends BaseCommand {

    public function execute()
    {
        $beforeCommandInstance = \Yii::$app->controller->checkCommand(['/start']);
        if($beforeCommandInstance) $beforeCommandInstance->execute();
    }
}