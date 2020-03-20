<?php

use yii\db\Migration;

/**
 * Class m200315_101906_alter_table_favorites
 */
class m200315_101906_alter_table_favorites extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('favorites', 'created_at', $this->dateTime());
        $this->alterColumn('favorites', 'user_id', $this->integer());
        $this->alterColumn('favorites', 'favorite_user_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('favorites', 'created_at', $this->dateTime()->notNull());
        $this->alterColumn('favorites', 'user_id', $this->integer()->notNull());
        $this->alterColumn('favorites', 'favorite_user_id', $this->integer()->notNull());
    }
}
