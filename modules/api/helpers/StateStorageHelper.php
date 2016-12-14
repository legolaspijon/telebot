<?php

namespace app\modules\api\helpers;


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

    static public function removeLastCommand() {
        $state = \Yii::$app->session->get('state');
        array_pop($state);
        \Yii::$app->session->set('state', $state);
    }

    static public function setStates($currentCommand, $start = false){
        $state = null;
        $session = \Yii::$app->session;
        !$start ?: $session->remove('state');
        if($session->has('state')) {
            $state = $session->get('state');
            if(end($state) == $currentCommand) return;
            array_push($state, $currentCommand);
            self::setState($state);
        } else {
            self::setState([$currentCommand]);
        }
    }

    static public function setState(array $states) {
        \Yii::$app->session->set('state', $states);
    }

    static public function getUser() {
        return unserialize(\Yii::$app->session->get('user', false));
    }

    static public function setUser($user) {
        \Yii::$app->session->set('user', serialize($user));
    }
}