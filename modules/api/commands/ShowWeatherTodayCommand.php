<?php

namespace app\modules\api\commands;

use Cmfcmf\OpenWeatherMap;

class ShowWeatherTodayCommand extends BaseCommand {

    private $units = [
        'C' => 'metric',
        'F' => 'imperial'
    ];

    public function execute()
    {
        $openWeather = new OpenWeatherMap('ef1678c075c0ba1229200bf033ee6392');
        $weather = $openWeather->get($this->user->city, \Yii::$app->params['units'][$this->user->measurement], $this->user->lang, '', 4);

        var_dump($weather);


        $unit = $this->user->measurement;

        $text = "\n" . '<i>Today ' . $weather->lastUpdate->date . '<i>';
        $text .= "\n" . "<b>NOW</b> " . $weather->temperature->now;
        $text .= sprintf("\n %d %s ... %d %s %s", $weather->temperature->min, $unit , $weather->temperature->max, $unit, $weather->temperature->getDescription());
        $text .= sprintf("\n<b>Morning:</b> %d %s", $weather->temperature->morning, $unit);
        $text .= sprintf("\n<b>Day:</b> %d %s", $weather->temperature->day, $unit);
        $text .= sprintf("\n<b>Evening:</b> %d %s", $weather->temperature->evening, $unit);
        $text = html_entity_decode($text);
        //$text = str_replace();
echo $text;exit;
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => $text,
            //'parse_mode' => 'HTML',
        ]);
    }
}