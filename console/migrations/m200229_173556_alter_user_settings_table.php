<?php

use yii\db\Migration;

/**
 * Class m200229_173556_alter_user_settings_table
 */
class m200229_173556_alter_user_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('user_settings', 'new_messages', $this->boolean());
        $this->alterColumn('user_settings', 'task_action', $this->boolean());
        $this->alterColumn('user_settings', 'new_response', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('opinions', 'new_messages', $this->tinyInteger());
        $this->alterColumn('opinions', 'task_action', $this->tinyInteger());
        $this->alterColumn('opinions', 'new_response', $this->tinyInteger());
    }
}
