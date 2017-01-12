<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{

    /**
     * Главная страница
     * */
    public function actionIndex()
    {
        exit;
    }

    public function actionAbout()
    {
        return $this->render('about');
    }


}
