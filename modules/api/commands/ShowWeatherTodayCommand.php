<?php

namespace app\modules\api\commands;

class ShowWeatherTodayCommand extends BaseCommand {

    public function execute()
    {
        $unit = $this->user->measurement;
        $emoji = \Yii::$app->params['emoji']['weather'];
        $units = \Yii::$app->params['units'][$unit];

        /**
         * Get weather for today
         * */
        $todayWeather = \Yii::$app->weather->getTodayWeather($this->user->city, ['units' => $units, 'lang' => $this->user->lang]);

        /**
         * Get current weather
         * */
        $currentWeather = \Yii::$app->weather->getWeather($this->user->city, ['units' => $units, 'lang' => $this->user->lang]);


        /**
         * Formatting message
         * */
        $fEmoji = json_decode('"' .$emoji[$todayWeather['weather'][0]['icon']] . '"');
        $cEmoji = json_decode('"' .$emoji[$currentWeather['weather'][0]['icon']]. '"');
        $dayLocal = \Yii::t('app', date('l', $todayWeather['dt']));
        $text = "\n<b>". \Yii::t('app', "City: {city}", ['city' => $this->user->city]) . "</b>";
        $text .= "\n<i>". \Yii::t('app', "Today, {date} {day}", ['date' => date('m/d', $todayWeather['dt']), 'day' => $dayLocal]) ."</i>";
        $text .= "\n$fEmoji {$todayWeather['temp']['morn']}...{$todayWeather['temp']['night']}&deg;$unit - {$todayWeather['weather'][0]['description']}";
        $text .= "\n<b>". \Yii::t('app', "Now {cEmoji} {temp}&deg;{unit2} Wind {speed} m/s", [
            'cEmoji' => $cEmoji,
            'temp' => $currentWeather['main']['temp'],
            'unit2' => $unit,
            'speed' => $currentWeather['wind']['speed']
        ]). "</b>";


        /**
         * Send message
         * */
        \Yii::$app->telegram->sendMessage([
            'chat_id' => $this->update->message->chat->id,
            'text' => html_entity_decode($text),
            'parse_mode' => 'HTML',
        ]);
    }
}