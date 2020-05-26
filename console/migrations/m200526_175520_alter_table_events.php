<?php

use yii\db\Migration;

/**
 * Class m200428_145619_alter_table_user_photo
 */
class m200526_175520_alter_table_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('events', 'subject', 'varchar(255)');
        $this->addColumn('events', 'type', 'integer');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('events', 'subject');
        $this->dropColumn('events', 'type');
    }
}
