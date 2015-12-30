<?php

class m140801_055412_menu extends ExtendedDbMigration
{
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `Menu` (
                `id` int(11) NOT NULL,
                `name` varchar(255) NOT NULL,
                `visible` tinyint(1) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            CREATE TABLE IF NOT EXISTS `Menu_lang` (
                `l_id` int(11) NOT NULL AUTO_INCREMENT,
                `menuId` int(11) NOT NULL,
                `lang_id` varchar(6) NOT NULL,
                `l_name` varchar(255) NOT NULL,
                PRIMARY KEY (`l_id`),
                KEY `menuId` (`menuId`),
                KEY `lang_id` (`lang_id`),
                CONSTRAINT `fk_menu_lang` FOREIGN KEY (`menuId`) REFERENCES `Menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ");


        $this->execute("
            CREATE TABLE IF NOT EXISTS `MenuItem` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `menuId` int(11) NOT NULL DEFAULT '0',
                `parentItemId` int(11) NOT NULL DEFAULT '0',
                `active` tinyint(1) NOT NULL DEFAULT '1',
                `visible` tinyint(1) NOT NULL DEFAULT '1',
                `name` varchar(100) NOT NULL,
                `image` varchar(100) NOT NULL COMMENT 'Картинка',
                `link` TEXT NOT NULL,
                `orderNum` int(10) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `parentItemId` (`parentItemId`),
                KEY `orderNum` (`orderNum`),
                CONSTRAINT `fk_menu_item` FOREIGN KEY (`menuId`) REFERENCES `Menu` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            CREATE TABLE IF NOT EXISTS `MenuItem_lang` (
                `l_id` int(11) NOT NULL AUTO_INCREMENT,
                `menuItemId` int(11) NOT NULL,
                `lang_id` varchar(6) NOT NULL,
                `l_name` varchar(255) NOT NULL,
                PRIMARY KEY (`l_id`),
                KEY `menuItemId` (`menuItemId`),
                KEY `lang_id` (`lang_id`),
                CONSTRAINT `fk_menu_item_lang` FOREIGN KEY (`menuItemId`) REFERENCES `MenuItem` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ");
    }

    public function safeDown()
    {
        $this->dropTable('MenuItem_lang');
        $this->dropTable('MenuItem');
        $this->dropTable("Menu_lang");
        $this->dropTable("Menu");
    }
}