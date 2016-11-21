<?php

namespace app\modules\api;

class Module extends \yii\base\Module {
    
    public $controllerNamespace = 'app\modules\api\controllers';
    public $layout = 'main';
    public $token;

    public function init()
    {
        parent::init();
    }
}