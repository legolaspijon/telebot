<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\StateStorageHelper;

class BackCommand extends BaseCommand {

    public function execute()
    {
        echo "sadsadas";exit;
        $controller = \Yii::$app->controller;
        $beforeCommandInstance = $controller->checkCommand([StateStorageHelper::getStateBefore()]);
        if($beforeCommandInstance) $beforeCommandInstance->execute();
    }

}