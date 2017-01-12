<?php

namespace app\modules\api\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\modules\api\models\StateStorage;

class StorageBehavior extends Behavior {

    public $user;

    public function events(){
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'createStorage'
        ];
    }

    public function createStorage(){
        $state = new StateStorage(['user_id' => $this->user->id]);
        $state->save();
    }

}