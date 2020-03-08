<?php

use yii\db\Migration;

/**
 * Class m200228_233200_alter_messages_table
 */
class m200228_233200_alter_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{messages}}', 'read', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{opinions}}', 'read');
    }
}
