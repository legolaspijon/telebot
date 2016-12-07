<?php

namespace app\modules\api\commands;

class ShowWeatherTodayCommand extends BaseCommand {

    public function execute()
    {
        $units = $this->user->measurement == 'C' ? 'metric' : 'imperial';
        $todayWeather = \Yii::$app->weather->getForecast($this->user->city, [
            'units' => $units,
            'lang' => $this->user->lang
        ], 1)['list'][0];

        $currentWeather = \Yii::$app->weather->getWeather($this->user->city, [
            'units' => $units,
            'lang' => $this->user->lang
        ]);

        $text = sprintf("<i>Today, %s</i>", date('m/d l', $todayWeather['dt']));
        $text .= sprintf("\n%d...%d &deg;%s - %s", $todayWeather['temp']['min'], $todayWeather['temp']['max'], $this->user->measurement, $todayWeather['weather'][0]['description']);
        $text .= sprintf("\n<b>Now %d &deg;%s wind %d</b>", $currentWeather['main']['temp'], $this->user->measurement, $currentWeather['wind']['speed']);
        $text = html_entity_decode($text);


        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }
}