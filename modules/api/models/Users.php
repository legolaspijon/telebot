<?php

namespace app\modules\api\models;

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
    public static function tableName()
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
            [['first_name', 'last_name'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 60],
            [['measurement'], 'string', 'max' => 1],
            [['city'], 'string', 'max' => 255],
            [['lang'], 'validateLang'],
        ];
    }

    public function validateLang(){
        if(!in_array($this->lang, ['ru', 'en'])) {
            $this->addError('lang', 'not correct');
            return false;
        }
        return true;
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
     * @return bool|array
     * */
    public static function setOption($option, $value, $chat_id)
    {
        $user = self::findOne(['chat_id' => $chat_id]);
        $user->{$option} = $value;
        if ($user->validate()) {
            return $user->save();
        } else {
            return false;
        }
    }
}
