<?php

Yii::import('application.models.MenuItem');

class Menu extends CActiveRecord
{
    const NONE = 0;
    

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function behaviors()
    {
        return array(
            'manyToMany' => array(
                'class' => 'lib.ar-relation-behavior.EActiveRecordRelationBehavior',
            ),
            'languageBehavior' => array(
                'class'                 => 'application.behaviors.MultilingualBehavior',
                'langClassName'         => 'MenuLang',
                'langTableName'         => 'Menu_lang',
                'langForeignKey'        => 'menuId',
                'localizedAttributes'   => array('name'),
                'languages'             => Yii::app()->params['languages'],
                'defaultLanguage'       => Yii::app()->sourceLanguage,
                'dynamicLangClass'      => true,
            ),
        );
    }

    public function relations()
    {
        return array(
            'items' => array(self::HAS_MANY, 'MenuItem', 'menuId', 'order'=>'items.orderNum ASC'),
        );
    }

    public function attributeLabels()
    {
        return array_merge(
            $this->languageBehavior->languageLabels(array(
                'name' => 'Название'
            )),
            array(
                'visible' => 'Показывать',
            )
        );
    }

    public function rules()
    {
        return array(
            array('name', 'required'),
            array('visible', 'boolean'),

            array('name', 'safe', 'on'=>'search'),
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

    public function search()
    {
        $criteria = new CDbCriteria;

        $alias = $this->getTableAlias();
        $criteria->compare($alias.'.name', $this->name, true);

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

    public static function getMenuTypes()
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'id, name';
        $menus = Menu::model()->findAll($criteria);
        
        $arr = array();
        foreach ($menus as $menu) {
            $arr[$menu->id] = $menu->name;
        }
        return $arr;
    }
}
