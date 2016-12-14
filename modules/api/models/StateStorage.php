<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "statestorage".
 *
 * @property integer $id
 * @property string $user
 * @property string $state
 * @property integer $isAnswer
 * @property integer $user_id
 */
class StateStorage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statestorage';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'state', 'isAnswer'], 'safe'],
        ];
    }

    /**
     * Управление ответом
     * */
    static public function isAnswer($id){
        $res = self::findModel($id);
        return $res ? $res->isAnswer : false;
    }

    static public function setIsAnswer($id){
        return self::setOption('isAnswer', true, $id);
    }

    static public function unsetIsAnswer($id){
        return self::setOption('isAnswer', false, $id);
    }
    /**
     * ------------------------------------------------------------------------
     * */

    /**
     * Список выполненых комманд
     * */
    static public function setState($command, $user_id, $start = false){
        $model = self::findModel($user_id);
        if($start) self::setOption('state', null, $user_id);
        if($model && $model->state != null) {
            $state = unserialize($model->state);
            if(end($state) == $command) return false;
            array_push($state, $command);
            return self::setOption('state', serialize($state), $user_id);
        } else {
            return self::setOption('state', serialize([$command]), $user_id);
        }
    }


    static public function getCurrentState($user_id){
        $model = self::findModel($user_id);
        $state = $model->getState();
        return end($state);
    }

    static public function removeLastCommand($user_id){
        $model = self::findModel($user_id);
        $state = $model->getState();
        array_pop($state);
        self::setOption('state', serialize($state), $user_id);
    }

    static public function setOption($option, $value, $user_id){
        $model = self::findModel($user_id);
        $model->{$option} = $value;

        return $model->save();
    }

    public function getState(){
        return unserialize($this->state);
    }

    static public function findModel($user_id){
        return self::find()->where(['user_id' => $user_id])->one();
    }
}
