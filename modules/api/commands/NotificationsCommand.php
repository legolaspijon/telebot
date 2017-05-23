<?php

namespace app\modules\api\commands;

use app\modules\api\models\Notifications;

class NotificationsCommand extends BaseCommand {

    public function execute()
    {
        $label = \Yii::$app->params['commandsLabels'][\Yii::$app->language];

        $btns = [
            [$label['/start']],
            [$label['/setmbnotification']],
            [$label['/setusdnotification']],
            [$label['/seteurnotification']],
            [$label['/setrubnotification']],
            [$label['/setcardsnotification']],
            [$label['/setbankscoursenotification']]
        ];
        $keyboard = ['keyboard' => $btns];
        $text = "<b>Установленные уведомления</b>\n";
        $text .= "\n<b>Межбанк:</b> " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_MB, ', ');
        $text .= "\n<b>Visa/MasterCard:</b> " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_CARDS, ', ');
        $text .= "\n<b>Курсы в банках:</b> " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_BANKS_COURSE, ', ');
        $text .= "\n<b>USD:</b> " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_AUCTION_USD, ', ');
        $text .= "\n<b>EUR:</b> " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_AUCTION_EUR, ', ');
        $text .= "\n<b>RUB:</b> " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_AUCTION_RUB, ', ');
        $text .= "\n\nВыберите для чего установить нотификации";


        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->user->chat_id,
            'text' => $text,
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'HTML'
        ]);
    }

}