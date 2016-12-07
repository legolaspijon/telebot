<?php

namespace app\modules\api\commands;

class ShowWeatherFiveCommand extends BaseCommand {

    public function execute()
    {
        $units = $this->user->measurement == 'C' ? 'metric' : 'imperial';
        $weather = \Yii::$app->weather->getForecast($this->user->city, [
            'lang' => $this->user->lang,
            'units' => $units
        ], 5);

        $text = '';
        foreach ($weather['list'] as $weather) {
            $text .= "\n" . "<i>For " . date('m/d l', $weather['dt']) ."</i>";
            $text .= sprintf("\n %d...%d &deg;%s - %s", $weather['temp']['min'], $weather['temp']['max'], $this->user->measurement, $weather['weather'][0]['description']);
            $text .= "\n-----------\n";
        }

        $text = html_entity_decode($text);

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }
}