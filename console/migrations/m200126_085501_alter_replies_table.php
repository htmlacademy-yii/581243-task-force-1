<?php

use yii\db\Migration;

/**
 * Class m200126_085501_alter_replies_table
 */
class m200126_085501_alter_replies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('replies', 'rejected', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('replies', 'rejected');
    }
}
