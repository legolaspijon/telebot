<?php

namespace app\modules\api\models;

use yii\db\ActiveRecord;

class Notifications extends ActiveRecord {

    const TYPE_MB = 1;
    const TYPE_BANKS_COURSE = 2;
    const TYPE_CARDS = 3;
    const TYPE_AUCTION_USD = 4;
    const TYPE_AUCTION_EUR = 5;
    const TYPE_AUCTION_RUB = 6;

    const PERIOD_EVERY_HOUR = 1;
    const PERIOD_EVERY_THREE_HOUR = 2;
    const PERIOD_TO_TIMES_PER_DAY = 3;


    public static function tableName()
    {
        return 'notifications';
    }

    public function rules()
    {
        return [
            [['type', 'hour', 'user_id'], 'required'],
            [['type', 'hour', 'user_id'], 'integer']
        ];
    }

    public function getUser(){
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    static public function getCommands(){
        return [
            self::TYPE_MB => 'mb',
            self::TYPE_BANKS_COURSE => 'bankcourses',
            self::TYPE_CARDS => 'cards',
            self::TYPE_AUCTION_USD => 'usd',
            self::TYPE_AUCTION_EUR => 'eur',
            self::TYPE_AUCTION_RUB => 'rub',
        ];
    }

    static public function getPeriods(){
        return [
            self::PERIOD_EVERY_HOUR => 'Каждый час',
            self::PERIOD_EVERY_THREE_HOUR => 'Каждые 3 часа',
            self::PERIOD_TO_TIMES_PER_DAY => 'Два раза в день (11, 16)'
        ];
    }

    public function getCommand(){
        $types = self::getCommands();
        return $types[$this->type];
    }

    static public function getNotifications($user_id, $type = null){
        $hours = [];
        $notifications = self::find()
            ->select('hour')
            ->where(['user_id' => $user_id, 'type' => $type])
            ->asArray()
            ->all();

        if(empty($notifications)) return '-';
        foreach ($notifications as $notification) {
            $label = self::getPeriods();
            $hours[] = $label[$notification['hour']];
        }

        return $hours;
    }

    static public function getNotifyAsString($user_id, $type, $delimiter){
        $notifyArr = self::getNotifications($user_id, $type);
        return is_array($notifyArr) ? implode($delimiter, $notifyArr) : $notifyArr;
    }

//    public function beforeSave($insert)
//    {
//        if(parent::beforeSave($insert)){
//            $notify = Notifications::find()
//                ->where(['user_id' => $this->user_id, 'type' => $this->type])
//                ->one();
//
//            if(!$notify) return true;
//        }
//
//        return false;
//    }
}