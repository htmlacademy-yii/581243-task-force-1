<?php

use yii\db\Migration;

/**
 * Class m200529_653431_add_city_id_to_tasks
 */
class m200529_653431_add_city_id_to_tasks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tasks', 'city_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('tasks', 'city_id');
    }
}
