<?php

class m140801_055411_local_config extends ExtendedDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `localconfig` (
                `id`          int(11) NOT NULL AUTO_INCREMENT,
                `name`        varchar(255) NOT NULL DEFAULT '',
                `value`       text NOT NULL,
                `module`      varchar(255) DEFAULT NULL,
                `description` text,
                `example`     text NOT NULL,
                `type`        ENUM('bool', 'int', 'fixedarray', 'dynamicarray', 'string', 'multilinestring', 'file', 'twopowarray'),
                PRIMARY KEY (`id`),
                KEY `name` (`name`),
                KEY `module` (`module`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );
    }

    public function safeDown()
    {
        $this->dropTable('localconfig');
    }
}
