<?php

class m151224_113955_moderator extends CDbMigration
{
    public function safeUp()
    {
        /** @var $auth CAuthManager */
        $auth = Yii::app()->authManager;

        $existingRoles = $auth->getRoles();
        if (!array_key_exists('moderator', $existingRoles))
            $auth->createRole('moderator', 'Редактирование контента');

        if (!array_key_exists('manager', $existingRoles))
            $auth->createRole('manager', 'Управляет некоторыми настройками сайта');

        $this->execute("UPDATE AuthItem SET description='Полные права' WHERE name='admin'");
    }

    public function safeDown()
    {
    }
}
