<?php

namespace app\modules\api\components\traits;

use app\modules\api\models\Notifications;
use app\modules\api\models\StateStorage;

trait NotificationTrait {

    public function addNotify($time, $type){

        $notify = Notifications::find()->where(['user_id' => $this->user->id, 'type' => $type])->one();
        $period = array_search($time, Notifications::getPeriods());

        if($period === false) {
            $text = 'Выберите вариант';
        } else {
            if(!$notify) {
                $notify = new Notifications([
                    'user_id' => $this->user->id,
                    'type' => $type,
                    'hour' => $period,
                ]);
                $notify->save();
                $text = 'Нотификация успешно установлена';
            } else {

                $notify->hour = $period;
                $notify->update();
                $text = 'Нотификация изменена';
            }
            StateStorage::unsetIsAnswer($this->user->id);
            StateStorage::removeLastCommand($this->user->id);
            $this->bot->createCommand('/notifications');
        }

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->user->chat_id,
            'text' => $text,
        ]);

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
        $btns = array_map(function($value){ return [$value]; }, Notifications::getPeriods());
        array_unshift($btns, [\Yii::t('app', 'Remove all')]);
        array_unshift($btns, [\Yii::t('app', 'back')]);
        return $btns;
    }

}