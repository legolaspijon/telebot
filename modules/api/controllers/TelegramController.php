<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Users;
use yii\base\Exception;
use yii\web\Controller;
use app\modules\api\helpers\CommandManager;

class TelegramController extends Controller {

    /**
     * Default language
     * @var $defaultCommand string
     * */
    public $defaultLang = 'ru';

    /**
     * last updates
     * @var $update array
     * */
    public $update;

    /**
     * @var $user Users
     * */
    public $user;


    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Telegram send own updates here
     * */
    public function actionWebHook() {
        try {

            $this->update = \Yii::$app->telegram->hook();
//            $update = \Yii::$app->telegram->getUpdates()->result;
//            $this->update = array_pop($update);
            if(is_object($this->update)){
                $this->setUser();
                \Yii::$app->language = $this->user ? $this->user->lang : $this->defaultLang;
            } else {
                throw new \Exception('update is empty');
            }
            $manager = new CommandManager($this->user);
            $manager->createCommand($this->update->message->text);

        } catch(\Exception $e) {
            \Yii::trace($e->getMessage(), 'debug');
        }

        exit;
    }

    /**
     * Set user if not exist
     */
    protected function setUser(){
        $user = Users::findOne(['chat_id' => $this->update->message->chat->id]);
        if(!$user) {
            $lastName = isset($this->update->message->from->last_name) ? $this->update->message->from->last_name : null;

            $user = new Users([
                'lang' => $this->defaultLang,
                'chat_id' => $this->update->message->chat->id,
                'first_name' => $this->update->message->from->first_name,
                'last_name' => $lastName,
            ]);
            $user->save();
        }
        $this->user = $user;
    }
}
