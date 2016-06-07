<?php

/**
 * Контентный блок
 */
class ContentBlocks extends CActiveRecord
{
    const POS_NONE                  = 0;

    private static $posNames = array(
    );

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'contentblocks';
    }

    public function behaviors()
    {
        return array(
            'languageBehavior' => array(
                'class'                 => 'application.behaviors.MultilingualBehavior',
                'langClassName'         => 'ContentBlocksLang',
                'langTableName'         => 'contentblocks_lang',
                'langForeignKey'        => 'cbId',
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
                'position'  => 'Место размещения',
                'visible'   => 'Показывать',
            )
        );
    }

    public function rules()
    {
        return array(
            array('title, text', 'safe'),
            array('visible', 'boolean'),
            array('position', 'numerical', 'integerOnly'=>true),

            array('visible', 'safe', 'on'=>'search'),
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

    public function byPosition($pos)
    {
        $alias = $this->getTableAlias();
        $this->getDbCriteria()->mergeWith(array(
            'condition' => $alias.'.position = '.$pos,
        ));
        return $this;
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $alias = $this->getTableAlias();
        $criteria->compare($alias.'.visible', $this->visible);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
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

    public static function getPosNames()
    {
        return self::$posNames;
    }
}
