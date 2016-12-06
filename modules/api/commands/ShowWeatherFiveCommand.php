<?php

namespace app\modules\api\commands;

class ShowWeatherFiveCommand extends BaseCommand {

    public function execute()
    {
        $unit = $this->user->measurement;
        $weathers = \Yii::$app->weather->getWeatherForFiveDays($this->user->city, $this->user->lang);
        $text = '';
        foreach ($weathers['list'] as $weather) {
            $text .= "\n_" . "Date " . date('m/d l', $weather['dt']) ."_";
            $text .= sprintf("\n %d %s ... %d %s", $weather['temp']['min'] , $unit , $weather['temp']['max'], $unit, $weather['weather'][0]['description']);
            $text .= sprintf("\n *Morning: * %d %s", $weather['temp']['morn'], $unit);
            $text .= sprintf("\n *Day: * %d %s", $weather['temp']['day'], $unit);
            $text .= sprintf("\n *Evening: * %d %s", $weather['temp']['eve'], $unit);
            $text .= "\n-----------\n";
        }

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'Markdown',
        ]);
    }
}