<?php

use yii\db\Migration;

class m161128_102525_add_user_tbl extends Migration
{
    public function up()
    {
        $this->createTable('users', [
            'id' => 'pk',
            'chat_id' => 'INTEGER(11)',
            'username' => 'VARCHAR(60) NOT NULL',
            'lang' => 'VARCHAR(5)',
            'city' => 'VARCHAR(255)',
        ]);
    }

    public function down()
    {
        $this->dropTable('users');
    }

}
