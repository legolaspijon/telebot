<?php

namespace app\modules\api\commands;

class SettingsCommand extends BaseCommand
{
    public function execute(){
        $message = $this->update->message;
        $text = "\n<b>". \Yii::t("app", "Current Settings:") ."</b>";
        $text .= "\n<b>". \Yii::t("app", "City") .":</b> " . $this->user->getCity();
        $text .= "\n<b>". \Yii::t("app", "Language") .":</b> " . \Yii::$app->params['languages'][$this->user->lang];
        $text .= "\n\n<b>". \Yii::t('app', "Select an option...") ."</b>";
        $text = html_entity_decode($text);


        \Yii::$app->telegram->sendMessage([
            'chat_id' => $message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $this->keyboard()
        ]);
    }

    public function keyboard() {
        $btnLabels = \Yii::$app->params['commandsLabels'][\Yii::$app->language];
        $menuEmoji = \Yii::$app->params['emoji']['menu'];

        $emojiEncoded = [];
        foreach ($menuEmoji as $label => $emoji) {
            $emojiEncoded[$label] = json_decode('"'. $emoji .'"');
        }

        $btns = [
            [$btnLabels["/start"]],
            [$emojiEncoded['location']." ".$btnLabels["/city"]],
            [$emojiEncoded['language']." ".$btnLabels["/language"]],
            [$btnLabels["/notifications"]]
        ];

        $keyboard = ["keyboard" => $btns, "resize_keyboard" => true];

        return json_encode($keyboard);
    }
}