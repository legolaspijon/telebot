<?php

use yii\db\Migration;

class m161128_102525_add_user_tbl extends Migration
{
    public function up()
    {
        $this->createTable('users', [
            'id' => 'pk',
            'chat_id' => 'INTEGER(11)',
            'first_name' => 'VARCHAR(60) NOT NULL',
            'last_name' => 'VARCHAR(60) NOT NULL',
            'lang' => 'ENUM("ru","en")',
            'city' => 'VARCHAR(255)',
            'measurement' => 'VARCHAR(1)'
        ]);
    }

    public function down()
    {
        $this->dropTable('users');
    }

}
