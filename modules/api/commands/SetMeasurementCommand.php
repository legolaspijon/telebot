<?php

namespace app\modules\api\commands;

use app\modules\api\models\Users;

class SetMeasurementCommand extends BaseCommand
{

    public function execute()
    {
        $message = $this->update->message;
        if ($this->answer) {
            $this->setMeasurement($message);
        } else {
            $this->setIsAnswer();
            $btn = [['C', 'F']];
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

            $this->unsetIsAnswer();
            return true;
        }

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => 'Measurement ' . $message->text . ' was not set, something wrong...',
        ]);

        return false;
    }
}