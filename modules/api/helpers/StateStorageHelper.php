<?php

namespace app\modules\api\helpers;


use app\modules\api\models\Users;

class StateStorageHelper {

    static public function isAnswer(){
        return \Yii::$app->session->get('isAnswer', false);
    }

    static public function setIsAnswer(){
        \Yii::$app->session->set('isAnswer', true);
    }

    static public function unsetIsAnswer(){
        \Yii::$app->session->remove('isAnswer');
    }

    static public function getCurrentState(){
        $state = \Yii::$app->session->get('state', [null]);
        return end($state);
    }

    static public function getStateBefore(){
        $state = \Yii::$app->session->get('state', [null]);
        return array_shift($state);
    }

    static public function setStates($currentCommand){
        $state = null;
        $session = \Yii::$app->session;
        if ($session->has('state')) {
            $state = $session->get('state');
            if(end($state) == $currentCommand) return;
            if (count($state) > 1) {
                array_shift($state);
                array_push($state, $currentCommand);
            } else {
                array_push($state, $currentCommand);
            }
            $session->set('state', $state);
            return;
        }
        $session->set('state', [$currentCommand]);
    }

    static public function getUser() {
        return \Yii::$app->session->get('user', false);
    }

    static public function setUser(Users $user) {
        \Yii::$app->session->set('user', serialize($user));
    }
}