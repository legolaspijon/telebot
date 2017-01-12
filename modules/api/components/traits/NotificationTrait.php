<?php

namespace app\modules\api\components\traits;

use app\modules\api\models\Notifications;
use app\modules\api\models\StateStorage;

trait NotificationTrait {

    public $time = [
        ['1:00'], ['2:00'], ['3:00'],
        ['4:00'], ['5:00'], ['6:00'],
        ['7:00'], ['8:00'], ['9:00'],
        ['10:00'], ['11:00'], ['12:00'],
        ['13:00'], ['14:00'], ['15:00'],
        ['16:00'], ['17:00'], ['18:00'],
        ['19:00'], ['20:00'], ['21:00'],
        ['22:00'], ['23:00'], ['0:00'],
    ];

    public function addNotify($time, $type){
        $time = date('G', strtotime($time));
        $notify = new Notifications([
            'user_id' => $this->user->id,
            'type' => $type,
            'hour' => $time
        ]);

        if ($notify->save()) {
            StateStorage::unsetIsAnswer($this->user->id);
            StateStorage::removeLastCommand($this->user->id);
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $this->user->chat_id,
                'text' => 'Нотификация успешно установлена',
            ]);
            $this->bot->createCommand('/notifications');
        } else {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $this->user->chat_id,
                'text' => 'Нотификация не установлена максимальное количество нотификаций - 3',
            ]);
        }
    }

    public function removeAll($user_id, $type){
        StateStorage::unsetIsAnswer($this->user->id);
        StateStorage::removeLastCommand($this->user->id);
        Notifications::deleteAll(['user_id' => $user_id, 'type' => $type]);
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->user->chat_id,
            'text' => 'Все уведомления для этой категории удалены',
        ]);
        $this->bot->createCommand('/notifications');
    }

    public function getBtns(){
        array_unshift($this->time, [\Yii::t('app', 'Remove all')]);
        array_unshift($this->time, [\Yii::t('app', 'back')]);
        return $this->time;
    }


}