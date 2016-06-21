<?php

Yii::import('modules.articles.models.Article');

class m160602_111014_article extends ExtendedDbMigration
{
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `articles` (
                `id`        int(11) NOT NULL AUTO_INCREMENT,
                `title`     text NOT NULL DEFAULT '' COMMENT 'Заголовок',
                `text`      text NOT NULL DEFAULT '' COMMENT 'Текст',
                `link`      varchar(255) NOT NULL DEFAULT '' COMMENT 'Урл для которого будет открываться статья',
                `visible`   tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
                PRIMARY KEY (`id`),
                KEY `visible` (`visible`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            CREATE TABLE IF NOT EXISTS `articles_lang` (
                `l_id` int(11) NOT NULL AUTO_INCREMENT,
                `articleId` int(11) NOT NULL,
                `lang_id` varchar(6) NOT NULL,
                `l_title` text NOT NULL,
                `l_text` text NOT NULL,
                PRIMARY KEY (`l_id`),
                KEY `articleId` (`articleId`),
                KEY `lang_id` (`lang_id`),
                CONSTRAINT `fk_article_lang` FOREIGN KEY (`articleId`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ");
    }

    public function safeDown()
    {
        $this->deleteAllModels('Article');
        $this->dropTable('articles_lang');
        $this->dropTable('articles');
    }
}
