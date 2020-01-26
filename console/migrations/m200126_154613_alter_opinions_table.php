<?php

use yii\db\Migration;

/**
 * Class m200126_154613_alter_opinions_table
 */
class m200126_154613_alter_opinions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('opinions', 'rate', $this->tinyInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('opinions', 'rate', $this->tinyInteger());
    }
}
