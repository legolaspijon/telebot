<?php

namespace app\modules\api\components;

use yii\base\Component;

class OpenWeather extends Component {

    /**
     * API key
     * */
    public $appId;


    /**
     * Format response json|xml
     * */
    public $format;


    const BASE_URL = 'http://api.openweathermap.org/data/2.5';
    const DEFAULT_FORECAST = 16;

    /**
     * Get current weather
     * @param $city string
     * @param $params array
     * @return array of response
     * */
    public function getWeather($city, $params){

        $cache = \Yii::$app->cache;
        $weather = $cache->get("weather_$city");
        if($weather === false) {
            $weather = $this->buildQuery('/weather?', $city, $params);
            $cache->add("weather_$city", $weather, 3600*3);
        }

        return $weather;
    }


    /**
     * Get forecast daily weather
     * @param $city string
     * @param $params array
     * @param $days integer
     * @return array|bool
     * */
    public function getForecast($city, $params, $days = null) {
        $cache = \Yii::$app->cache;
        if($days > self::DEFAULT_FORECAST && $days < 1) {
            \Yii::trace('Days must by <= '. self::DEFAULT_FORECAST);
            return false;
        }

        $params['cnt'] = self::DEFAULT_FORECAST;
        $weather = $cache->get("forecast_$city");
        if($weather === false) {
            $weather = $this->buildQuery('/forecast/daily?', $city, $params);
            $cache->add("forecast_$city", $weather, 3600*3);
        }

        return array_splice($weather['list'], 0, $days);
    }


    /**
     * Build query
     * @param $query string (forecast or current weather)
     * @param $city string
     * @param array
     * @return array
     * */
    public function buildQuery($query, $city, $params) {
        $params['appid'] = $this->appId;
        $params['format'] = $this->format;
        $params['q'] = $city;

        return $this->curl_query(self::BASE_URL . $query . http_build_query($params));
    }
    

    /**
     * Request to Open Weather API
     * @param $url string
     * @return array
     * */
    public function curl_query($url) {
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        
        return json_decode($response, true);
    }


    /**
     * get today weather
     * @param $city string
     * @param $params
     * @return array
     * */
    public function getTomorrowWeather($city, $params) {
        return $this->getForecast($city, $params, 2)[1];
    }

    /**
     * get tomorrow weather
     * @param $city string
     * @param $params
     * @return array
     * */
    public function getTodayWeather($city, $params) {
        return $this->getForecast($city, $params, 1)[0];
    }

}