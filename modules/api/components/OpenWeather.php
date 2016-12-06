<?php

namespace app\modules\api\components;

use yii\base\Component;

class OpenWeather extends Component implements WeatherInterface {

    public $appId;
    public $format;

    const BASE_URL = 'http://api.openweathermap.org/data/2.5';

    public function getWeatherForToday($city, $lang) {
        $forecast = $this->getWeather([
            'appId' => $this->appId,
            'format' => $this->format,
            'q' => $city,
            'lang' => $lang,
            'units' => 'metric'
        ]);

        return $forecast['list'][0];
    }

    public function getWeatherForTomorrow($city, $lang, $units) {
        $forecast = $this->getWeather([
            'appId' => $this->appId,
            'format' => $this->format,
            'q' => $city,
            'lang' => $lang,
            'units' => 'metric'
        ]);

        return $forecast['list'][1];
    }

    public function getWeatherForFiveDays($city, $lang) {
        $forecast = $this->getWeather([
            'appId' => $this->appId,
            'format' => $this->format,
            'q' => $city,
            'lang' => $lang,
            'units' => 'metric'
        ]);

        return $forecast;
    }

    /**
     * Get response from Open Weather API
     * @param $params
     * @return array
     * */
    public function getWeather($params){
        $url = self::BASE_URL . "/forecast/daily?" . http_build_query($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);

        return json_decode($result, true);
    }

}