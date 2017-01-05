<?php

namespace app\modules\api\models;
use app\modules\api\helpers\StateStorageHelper;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property integer $chat_id
 * @property string $first_name
 * @property string $last_name
 * @property string $lang
 * @property string $measurement
 * @property string $city
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    static public function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id'], 'integer'],
            [['first_name', 'last_name'], 'string', 'max' => 60],
            [['measurement'], 'in', 'range' => ['F', 'C']],
            [['city'], 'string', 'max' => 255],
            [['lang'], 'in', 'range' => array_keys(\Yii::$app->params['languages'])],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'lang' => 'Lang',
            'city' => 'City',
            'measurement' => 'Measurement'
        ];
    }

    /**
     * Save option
     * @param $option string
     * @param $value string
     * @param $chat_id integer
     * @return bool
     * */
    static public function setOption($option, $value, $chat_id)
    {
        $user = self::findOne(['chat_id' => $chat_id]);
        $user->{$option} = $value;

        return $user->save();
    }
    

    public function beforeSave($insert)
    {
        // \Yii::$app->cache->flush();
        return parent::beforeSave($insert);
    }

    public function getCity(){
        $fc = mb_strtoupper(mb_substr($this->city, 0, 1));
        return $fc.mb_substr($this->city, 1);
    }

    public function getStatestorage(){
        return $this->hasOne(StateStorage::className(), ['user_id' => 'id']);
    }

    static public function getUserByChatId($chatId){
        $user = self::findOne(['chat_id' => $chatId]);

        return $user;
    }
}
