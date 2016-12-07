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

    /**
     * Get current weather
     * @param $city string
     * @param $params array
     * @return array of response
     * */
    public function getWeather($city, $params){

        return $this->buildQuery('/weather?', $city, $params);
    }

    /**
     * Get forecast daily weather
     * @param $city string
     * @param $params array
     * @param $days integer
     * @throws \Exception
     * @return array of response
     * */
    public function getForecast($city, $params, $days = null) {
        if($days <=16 && $days >= 1) {
            $params['cnt'] = $days;
        } else {
            throw new \Exception('Days must by <= 16');
        }

        return $this->buildQuery('/forecast/daily?', $city, $params);
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

    public function getWeatherForTodayOrTomorrow($city, $params, $todayOrTomorrow = 'today') {
        $days = 1; $getDay = 0;

        if($todayOrTomorrow == 'tomorrow') {
            $getDay = 1;
            $days = 2;
        }

        return $this->getForecast($city, $params, $days)['list'][$getDay];
    }

}