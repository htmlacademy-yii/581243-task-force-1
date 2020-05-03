<?php

use yii\db\Migration;

/**
 * Class m200428_141917_alter_table_events
 */
class m200428_141917_alter_table_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('events', 'mesage', 'message');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('events', 'message', 'mesage');
    }
}
