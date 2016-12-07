<?php

namespace app\modules\api\commands;

class ShowWeatherFiveCommand extends BaseCommand {

    public function execute()
    {
        $unit = $this->user->measurement;
        $units = \Yii::$app->params['units'][$unit];
        $emoji = \Yii::$app->params['emoji']['weather'];

        $weather = \Yii::$app->weather->getForecast($this->user->city, [
            'lang' => $this->user->lang,
            'units' => $units
        ], 5);

        $text = '';

        foreach ($weather['list'] as $weather) {
            $emoji = json_decode('"' .$emoji[$weather['weather'][0]['icon']] .'"');
            $dayLocale = \Yii::t('app', date('l', $weather['dt']));
            $text .= "\n<i>". \Yii::t('app', "For {date} {day}", ['date' => date('m/d', $weather['dt']), 'day' => $dayLocale]) ."</i>";
            $text .= "\n" . $emoji . "{$weather['temp']['day']}...{$weather['temp']['night']} &deg;$unit - {$weather['weather'][0]['description']}\n";
        }

        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => html_entity_decode($text),
            'parse_mode' => 'HTML',
        ]);
    }
}