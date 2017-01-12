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
        $text .= "Межбанк: " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_MB, ', ');
        $text .= "\nVisa/MasterCard: " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_CARDS, ', ');
        $text .= "\nКурсы в банках: " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_BANKS_COURSE, ', ');
        $text .= "\nUSD: " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_AUCTION_USD, ', ');
        $text .= "\nEUR: " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_AUCTION_EUR, ', ');
        $text .= "\nRUB: " . Notifications::getNotifyAsString($this->user->id, Notifications::TYPE_AUCTION_RUB, ', ');
        $text .= "\n\nВыберите для чего установить нотификации";


        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->user->chat_id,
            'text' => $text,
            'reply_markup' => json_encode($keyboard),
            'parse_mode' => 'HTML'
        ]);
    }

}