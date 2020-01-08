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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191221_225514_alter_users_table cannot be reverted.\n";

        return false;
    }
    */
}
