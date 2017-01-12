<?php

namespace app\commands;

use yii\console\Controller;
use app\modules\api\models\Notifications;
use app\modules\api\helpers\CommandManager;

class NotificationController extends Controller {

    public function actionSendMessage() {
        $GMT = 2;
        $hour = date('H') + $GMT;
        $notifications = Notifications::findAll(['hour' => $hour]);

        foreach ($notifications as $notification) {
            $user = $notification->user;
            \Yii::$app->language = $user->lang;
            $manager = new CommandManager($user);
            $command = $notification->getCommand();
            $manager->createCommand('/'. $command);
        }

        return true;
    }
}