<?php

class m130826_130922_contentBlocks extends ExtendedDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `contentblocks` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` text NOT NULL COMMENT 'Заголовок',
                `text` text NOT NULL COMMENT 'Текст',
                `position` int(11) NOT NULL DEFAULT '0' COMMENT 'Место размещения',
                `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
                PRIMARY KEY (`id`),
                KEY `position` (`position`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS `contentblocks_lang` (
                `l_id` int(11) NOT NULL AUTO_INCREMENT,
                `cbId` int(11) NOT NULL,
                `lang_id` varchar(6) NOT NULL,
                `l_title` text NOT NULL,
                `l_text` text NOT NULL,
                PRIMARY KEY (`l_id`),
                KEY `cbId` (`cbId`),
                KEY `lang_id` (`lang_id`),
                CONSTRAINT `fk_cb_lang` FOREIGN KEY (`cbId`) REFERENCES `contentblocks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ");
    }

    public function safeDown()
    {
        $this->dropTable('contentblocks_lang');
        $this->dropTable('contentblocks');
    }
}