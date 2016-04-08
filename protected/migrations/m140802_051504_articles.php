<?php

class m140802_051504_articles extends ExtendedDbMigration
{
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `articles` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `type` int(11) NOT NULL DEFAULT 0 COMMENT 'Тип статьи, см. Articles',
                `title` text NOT NULL DEFAULT '' COMMENT 'Заголовок',
                `text` text NOT NULL DEFAULT '' COMMENT 'Текст',
                `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Видимость',
                PRIMARY KEY (`id`),
                KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->execute("
            CREATE TABLE IF NOT EXISTS `articles_lang` (
                `l_id` int(11) NOT NULL AUTO_INCREMENT,
                `articleId` int(11) NOT NULL,
                `lang_id` varchar(6) NOT NULL,
                `l_title` text NOT NULL default '',
                `l_text` text NOT NULL default '',
                PRIMARY KEY (`l_id`),
                KEY `menuId` (`articleId`),
                KEY `lang_id` (`lang_id`),
                CONSTRAINT `fk_articles_lang` FOREIGN KEY (`articleId`) REFERENCES `articles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ");

        $article = new Articles;
        $article->multilang();
        $attr = array_merge(
            $this->createLangData('title', 'В разработке', 'content'),                     // Yii::t('content', 'В разработке')
            $this->createLangData('text', 'Раздел находится в разработке', 'content'),     // Yii::t('content', 'Раздел находится в разработке')
            array(
                'type' => Articles::TYPE_UNDER_CONSTRUCTION
            )
        );
        $article->setAttributes($attr);
        $article->save();
    }

    public function safeDown()
    {
        $article = Articles::model()->byType(Articles::TYPE_UNDER_CONSTRUCTION)->delete();
        $this->dropTable('articles_lang');
        $this->dropTable('articles');
    }
}