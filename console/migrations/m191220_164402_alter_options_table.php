<?php

use yii\db\Migration;

/**
 * Class m191220_164402_alter_options_table
 */
class m191220_164402_alter_options_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{opinions}}', 'evaluated_user_id', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{opinions}}', 'evaluated_user_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191220_164402_alter_options_table cannot be reverted.\n";

        return false;
    }
    */
}
