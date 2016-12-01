<?php

namespace app\modules\api\commands;

use app\modules\api\helpers\StateStorageHelper;
use app\modules\api\models\Users;

class SetMeasurementCommand extends BaseCommand
{

    public function execute()
    {
        $message = $this->update->message;
        if ($this->answer) {
            $this->setMeasurement($message);
        } else {
            StateStorageHelper::setIsAnswer();
            $btn = [['back'], ['C', 'F']];
            $keyboard = json_encode(['keyboard' => $btn, "resize_keyboard" => true]);
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => 'Choose measurement...',
                'reply_markup' => $keyboard
            ]);
        }
    }

    protected function setMeasurement($message)
    {
        if (Users::setOption('measurement', $message->text, $message->chat->id)) {
            \Yii::$app->telegram->sendMessage([
                'chat_id' => $message->chat->id,
                'text' => \Yii::t("app", "Measurement '{text}' was set successfully...", ['text' => $message->text]),
            ]);

            StateStorageHelper::unsetIsAnswer();
            return true;
        }

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => \Yii::t("app", 'Measurement {text} was not set, something wrong...', ['text' => $message->text]),
        ]);

        return false;
    }
}