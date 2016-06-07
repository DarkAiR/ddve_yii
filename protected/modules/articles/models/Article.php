<?php

/**
 * Статья
 */
class Article extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'articles';
    }

    public function behaviors()
    {
        return array(
            'languageBehavior' => array(
                'class'                 => 'application.behaviors.MultilingualBehavior',
                'langClassName'         => 'ArticleLang',
                'langTableName'         => 'articles_lang',
                'langForeignKey'        => 'articleId',
                'localizedAttributes'   => array('title', 'text'),
                'languages'             => Yii::app()->params['languages'],
                'defaultLanguage'       => Yii::app()->sourceLanguage,
                'dynamicLangClass'      => true,
            ),
        );
    }

    public function attributeLabels()
    {
        return array_merge(
            $this->languageBehavior->languageLabels(array(
                'title'     => 'Заголовок',
                'text'      => 'Текст'
            )),
            array(
                'link'      => 'Url',
                'visible'   => 'Показывать',
            )
        );
    }

    public function rules()
    {
        return array(
            array('title, text, link', 'safe'),
            array('visible', 'boolean'),

            array('title, link, visible', 'safe', 'on'=>'search'),
        );
    }

    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'onSite' => array(
                'condition' => $alias.'.visible = 1',
            ),
        );
    }

    public function defaultScope()
    {
        return $this->languageBehavior->localizedCriteria();
    }

    public function byLink($link)
    {
        $link = trim($link, '/');
        
        $alias = $this->getTableAlias();
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'TRIM(BOTH "/" FROM '.$alias.'.link) = "'.$link.'"',
        ));
        return $this;
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $alias = $this->getTableAlias();
        $criteria->compare($alias.'.visible', $this->visible);
        $criteria->compare($alias.'.title', $this->title, true);
        $criteria->compare($alias.'.link', $this->link, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination'=>array(
                'pageSize'=>20,
            ),
            'sort' => array(
                'defaultOrder' => array(
                    'id' => CSort::SORT_ASC,
                )
            )
        ));
    }

    protected function beforeFind()
    {
        // Поддержка многих языков после загрузки модели
        $this->languageBehavior->multilang();
        return parent::beforeFind();
    }
}
