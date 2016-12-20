<?php

namespace app\modules\api\commands;

use app\modules\api\models\StateStorage;
use app\modules\api\models\Users;

class SetMeasurementCommand extends BaseCommand
{

    public function execute()
    {
        $message = $this->update->message;
        $menuEmoji = \Yii::$app->params['emoji']['menu'];
        if ($this->answer) {
            $this->setMeasurement($message);
        } else {
            StateStorage::setIsAnswer($this->user->id);
            $btn = [[json_decode('"'. $menuEmoji['back'] .'"') .' '. \Yii::t('app', 'back')], ['C', 'F']];
            $keyboard = json_encode(['keyboard' => $btn, "resize_keyboard" => true, 'one_time_keyboard' => true]);
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t("app", 'Select units'),
                'reply_markup' => $keyboard
            ]);
        }
    }

    protected function setMeasurement($message)
    {
        if (Users::setOption('measurement', $message->text, $message->chat->id)) {
            $menuEmoji = \Yii::$app->params['emoji']['menu'];
            $btn = [[json_decode('"'. $menuEmoji['back'] .'"') .' '. \Yii::t('app', 'back')]];
            $keyboard = json_encode(['keyboard' => $btn, "resize_keyboard" => true]);

            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t("app", "Units '{text}' was set successfully...", ['text' => $message->text]),
                'reply_markup' => $keyboard
            ]);

            StateStorage::unsetIsAnswer($this->user->id);
            StateStorage::removeLastCommand($this->user->id);
            sleep(1);
            $this->bot->createCommand('/settings', null, 1);

            return true;
        }

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => \Yii::t("app", 'Units {text} was not set, something wrong...', ['text' => $message->text]),
        ]);

        return false;
    }
}