<?php

use yii\db\Migration;

/**
 * Class m200428_145619_alter_table_user_photo
 */
class m200428_145619_alter_table_user_photo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameTable('user_foto', 'user_photo');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameTable('user_photo', 'user_foto');
    }
}
