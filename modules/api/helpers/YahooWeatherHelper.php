<?php

namespace app\modules\api\helpers;


class YahooWeatherHelper {

    protected static $base_url = "http://query.yahooapis.com/v1/public/yql";

    static public function getWeather($city = null){
        $yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="Харьков")';
        $yql_query_url = self::$base_url . "?q=" . urlencode($yql_query) . "&format=json";
        $session = curl_init($yql_query_url);
        curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
        $json = curl_exec($session);
        $phpObj =  json_decode($json, true);

        var_dump($phpObj);
    }

}