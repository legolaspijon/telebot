<?php

namespace app\modules\api\commands;

class ShowWeatherFiveCommand extends BaseCommand {

    public function execute()
    {
        $unit = $this->user->measurement;
        $units = \Yii::$app->params['units'][$unit];
        $emoji = \Yii::$app->params['emoji']['weather'];

        $weatherForecast = \Yii::$app->weather->getForecast($this->user->city, [
            'lang' => $this->user->lang,
            'units' => $units
        ], 5);
        $text = "\n<b>". \Yii::t('app', "City: {city}", ['city' => $this->user->city]) . "</b>";
        foreach ($weatherForecast as $weather) {
	    \Yii::trace(print_r($weather, true), 'debug');
            $dayLocale = \Yii::t('app', date('l', $weather['dt']));
            $text .= "\n<i>". \Yii::t('app', "For {date} {day}", ['date' => date('m/d', $weather['dt']), 'day' => $dayLocale]) ."</i>";
            $text .= "\n" . json_decode('"' .$emoji[$weather['weather'][0]['icon']] .'"') . "{$weather['temp']['day']}...{$weather['temp']['night']}&deg;$unit - {$weather['weather'][0]['description']}";
	    $text .= "\n" . \Yii::t('app', "Wind speed {speed} m/s", ['speed' => $weather['speed']]);
	    $text .= "\n" .json_decode('"' .$emoji['03d'] .'"'). \Yii::t('app', "Clouds {clouds}%", ['clouds' => $weather['clouds']])."\n";
        }

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => html_entity_decode($text),
            'parse_mode' => 'HTML',
        ]);
    }
}