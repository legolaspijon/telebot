<?php

namespace app\modules\api\components;

interface WeatherInterface {
    public function getWeather($params);
}