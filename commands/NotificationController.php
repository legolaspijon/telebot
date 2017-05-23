<?php

namespace app\commands;

use yii\console\Controller;
use app\modules\api\models\Notifications;
use app\modules\api\helpers\CommandManager;

class NotificationController extends Controller {

    public function actionSendMessage() {
        $GMT = 2;
        $hour = date('H') + $GMT;

        $notifications = Notifications::find()->where(['hour' => Notifications::PERIOD_EVERY_HOUR]);
        if(($hour % 3) == 0){
            $notifications->orWhere(['hour' => Notifications::PERIOD_EVERY_THREE_HOUR]);
        }
        if($hour == 11 || $hour == 16){
            $notifications->orWhere(['hour' => Notifications::PERIOD_TO_TIMES_PER_DAY]);
        }

        foreach ($notifications->all() as $notification) {
            $user = $notification->user;
            \Yii::$app->language = $user->lang;
            $manager = new CommandManager($user);
            $command = $notification->getCommand();
            $manager->createCommand('/'. $command);
        }

        return true;
    }

    public function actionTime(){
        echo date('H:i:s', strtotime('now'));
    }
}