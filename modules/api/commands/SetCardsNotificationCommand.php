<?php

namespace app\modules\api\commands;

use app\modules\api\models\Notifications;
use app\modules\api\models\StateStorage;
use app\modules\api\components\traits\NotificationTrait;

class SetCardsNotificationCommand extends BaseCommand {

    use NotificationTrait;

    public function execute()
    {
        if ($this->answer) {
            ($this->answer == \Yii::t('app', 'Remove all'))
                ? $this->removeAll($this->user->id, Notifications::TYPE_CARDS)
                : $this->addNotify($this->answer, Notifications::TYPE_CARDS);
        } else {
            StateStorage::setIsAnswer($this->user->id);
            $keyboard = ['keyboard' => $this->getBtns()];

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $this->user->chat_id,
                'text' => 'Выберите время',
                'reply_markup' => json_encode($keyboard),
            ]);
        }
    }
}