<?php

namespace app\modules\api\commands;

class ShowWeatherTomorrowCommand extends BaseCommand {

    public function execute()
    {
	if($this->user->city){
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
    	    $emojiIcon = json_decode('"' .$emoji[$weather['weather'][0]['icon']] .'"');
    	    $dayLocale = \Yii::t('app', date('l', $weather['dt']));
    	    $text = "\n<b>". \Yii::t('app', "City: {city}", ['city' => $this->user->city]) ."</b>";
    	    $text .= "\n<i>". \Yii::t('app', "Tomorrow {date} {day}", ['date' => date('m/d', $weather['dt']), 'day' => $dayLocale]) ."</i>";
    	    $text .= "\n". $emojiIcon . "{$weather['temp']['day']}...{$weather['temp']['night']}&deg;$unit - {$weather['weather'][0]['description']}";
	    $text .= "\n" . \Yii::t('app', "Wind speed {speed} m/s", ['speed' => $weather['speed']]);
	    $text .= "\n" .json_decode('"' .$emoji['03d'] .'"'). \Yii::t('app', "Clouds {clouds}%", ['clouds' => $weather['clouds']])."\n";

/*        $text .= sprintf("\n *Morning: * %d &deg;%s", $weather['temp']['morn'], $this->user->measurement);
    	    $text .= sprintf("\n *Day: * %d &deg;%s", $weather['temp']['day'], $this->user->measurement);
    	    $text .= sprintf("\n *Evening: * %d &deg;%s", $weather['temp']['eve'], $this->user->measurement);*/
	} else {
	    $text = "Set city at first! Use /city command";
	}


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