<?php

namespace app\modules\api\commands;

class ShowWeatherTomorrowCommand extends BaseCommand {

    public function execute()
    {
        $units = $this->user->measurement == 'C' ? 'metric' : 'imperial';
        $weather = \Yii::$app->weather->getForecast($this->user->city, [
            'lang' => $this->user->lang,
            'units' => $units
        ], 2)['list'][1];

        $text = "\n" . "<i>Tomorrow " . date('m/d l', $weather['dt']) ."</i>";
        $text .= sprintf("\n %d...%d &deg;%s - %s", $weather['temp']['min'], $weather['temp']['max'], $this->user->measurement, $weather['weather'][0]['description']);
//        $text .= sprintf("\n *Morning: * %d &deg;%s", $weather['temp']['morn'], $this->user->measurement);
//        $text .= sprintf("\n *Day: * %d &deg;%s", $weather['temp']['day'], $this->user->measurement);
//        $text .= sprintf("\n *Evening: * %d &deg;%s", $weather['temp']['eve'], $this->user->measurement);

        $text = html_entity_decode($text);
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }
}