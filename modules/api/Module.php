<?php

namespace app\modules\api;

class Module extends \yii\base\Module {
    
    public $controllerNamespace = 'app\modules\api\controllers';

    public function init()
    {
        \Yii::$app->setComponents([
            'weather' => [
                'class' => '\app\modules\api\components\OpenWeather',
                'appId' => 'ef1678c075c0ba1229200bf033ee6392',
                'format' => 'json'
            ]
        ]);
        parent::init();
    }
}