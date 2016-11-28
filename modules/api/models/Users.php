<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property integer $caht_id
 * @property string $username
 * @property string $lang
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
            [['username'], 'safe'],
            [['username'], 'string', 'max' => 60],
            [['lang'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'caht_id' => 'Caht ID',
            'username' => 'Username',
            'lang' => 'Lang',
        ];
    }
}
