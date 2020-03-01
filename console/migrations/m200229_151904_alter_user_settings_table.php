<?php

use yii\db\Migration;

/**
 * Class m200229_151904_alter_user_settings_table
 */
class m200229_151904_alter_user_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_settings', 'show_only_client', $this->boolean());
        $this->addColumn('user_settings', 'hide_profile', $this->boolean());
        $this->dropColumn('user_settings', 'profile_access');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user_settings', 'show_only_client');
        $this->dropColumn('user_settings', 'hide_profile');
        $this->addColumn('user_settings', 'profile_access', $this->tinyInteger());
    }
}
