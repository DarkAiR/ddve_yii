<?php

class m160616_091819_articles_coords extends CDbMigration
{
    public function safeUp()
    {
        $this->execute("ALTER TABLE articles ADD COLUMN `coords` varchar(128) NOT NULL DEFAULT '' COMMENT 'Координаты' AFTER link");
    }

    public function safeDown()
    {
        $this->dropColumn('articles', 'coords');
    }
}
