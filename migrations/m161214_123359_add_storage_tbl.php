<?php

use yii\db\Migration;
use yii\db\Schema;

class m161214_123359_add_storage_tbl extends Migration
{
    public function up()
    {
        $this->createTable('statestorage', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER,
            'state' => Schema::TYPE_STRING . '(255) NULL',
            'isAnswer' => Schema::TYPE_BOOLEAN
        ], 'ENGINE=MEMORY');
    }

    public function down()
    {
        $this->dropTable('statestorage');
    }


}
