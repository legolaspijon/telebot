<?php

namespace app\modules\api\commands;

class ShowWeatherTomorrowCommand extends BaseCommand {

    public function execute()
    {
        $unit = $this->user->measurement;                   // user units label
        $units = \Yii::$app->params['units'][$unit];        // request units imperial|metric
        $emoji = \Yii::$app->params['emoji']['weather'];    // emoji to weather text

        /**
         * Get weather for tomorrow
         * */
        $weather = \Yii::$app->weather->getTomorrowWeather($this->user->city, ['units' => $units, 'lang' => $this->user->lang]);

        /**
         * Formatting data
         * */
        $emoji = json_decode('"' .$emoji[$weather['weather'][0]['icon']] .'"');
        $dayLocale = \Yii::t('app', date('l', $weather['dt']));
        $text = "\n<b>". \Yii::t('app', "City: {city}", ['city' => $this->user->city]) ."</b>";
        $text .= "\n<i>". \Yii::t('app', "Tomorrow {date} {day}", ['date' => date('m/d', $weather['dt']), 'day' => $dayLocale]) ."</i>";
        $text .= "\n". $emoji . "{$weather['temp']['day']}...{$weather['temp']['night']}&deg;$unit - {$weather['weather'][0]['description']}";

/*        $text .= sprintf("\n *Morning: * %d &deg;%s", $weather['temp']['morn'], $this->user->measurement);
        $text .= sprintf("\n *Day: * %d &deg;%s", $weather['temp']['day'], $this->user->measurement);
        $text .= sprintf("\n *Evening: * %d &deg;%s", $weather['temp']['eve'], $this->user->measurement);*/

        /**
         * Send weather text
         * */
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => html_entity_decode($text),
            'parse_mode' => 'HTML',
        ]);
    }
}