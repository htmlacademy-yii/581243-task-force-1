<?php

use yii\db\Migration;

/**
 * Class m191221_225514_alter_users_table
 */
class m191221_225514_alter_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('users', 'created_at', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('users', 'created_at', $this->dateTime()->notNull());
    }
}
