<?php

/**
 * Статья
 */
class Articles extends CActiveRecord
{
    const TYPE_UNDER_CONSTRUCTION = 0;              // Произольная статья


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function behaviors()
    {
        return array(
            'languageBehavior' => array(
                'class'                 => 'application.behaviors.MultilingualBehavior',
                'langClassName'         => 'ArticlesLang',
                'langTableName'         => 'Articles_lang',
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
                'type' => 'Тип статьи',
                'visible' => 'Показывать',
            )
        );
    }

    public function rules()
    {
        return array(
            array('title, text', 'safe'),
            array('visible', 'boolean'),
            array('type', 'numerical', 'integerOnly'=>true),

            array('title, text, type', 'safe', 'on'=>'search'),
        );
    }

    public function defaultScope()
    {
        return $this->languageBehavior->localizedCriteria();
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

    public function byType($type)
    {
        $alias = $this->getTableAlias();
        $this->getDbCriteria()->mergeWith(array(
            'condition' => $alias.'.type = '.$type,
        ));
        return $this;
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $alias = $this->getTableAlias();
        $criteria->compare($alias.'.title', $this->title, true);
        $criteria->compare($alias.'.text', $this->text, true);
        $criteria->compare($alias.'.type', $this->type);

        return new CActiveDataProvider($this, array(
            'criteria' => $this->languageBehavior->modifySearchCriteria($criteria),
            //'pagination'=>array(
            //    'pageSize'=>20,
            //),
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

    public static function getTypeNames()
    {
        return array(
            self::TYPE_UNDER_CONSTRUCTION => 'В разработке',
        );
    }
}
