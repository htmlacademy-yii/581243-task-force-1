<?php

use yii\db\Migration;

/**
 * Class m200314_152417_alter_table_users
 */
class m200314_152417_alter_table_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'vk_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('users', 'vk_id');
    }
}
