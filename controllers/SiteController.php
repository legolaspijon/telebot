<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
	$update = json_decode(file_get_contents('php://input'), true);
	file_put_contents('testing.txt', print_r($update, true));
	exit;

    }
}
