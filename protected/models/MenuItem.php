<?php

class MenuItem extends CActiveRecord
{
    const IMAGE_W = 28;
    const IMAGE_H = 28;

    public $_image = null; //UploadedFile[]
    public $_removeImageFlag = false; // bool


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
            'imageBehavior' => array(
                'class' => 'application.behaviors.ImageBehavior',
                'storagePath' => 'menu',
                'imageWidth' => self::IMAGE_W,
                'imageHeight' => self::IMAGE_H,
                'imageField' => 'image',
                'imageLabel' => 'Иконка'
            ),
            'orderBehavior' => array(
                'class' => 'application.behaviors.OrderBehavior',
            ),
            'languageBehavior' => array(
                'class'                 => 'application.behaviors.MultilingualBehavior',
                'langClassName'         => 'MenuItemLang',
                'langTableName'         => 'MenuItem_lang',
                'langForeignKey'        => 'menuItemId',
                //'langField'           => 'lang_id',
                'localizedAttributes'   => array('name'),
                //'localizedPrefix'     => 'l_',
                'languages'             => Yii::app()->params['languages'],
                'defaultLanguage'       => Yii::app()->sourceLanguage,
                //'createScenario'      => 'insert',
                //'localizedRelation'   => 'i18nPost',
                //'multilangRelation'   => 'multilangPost',
                //'forceOverwrite'      => false,
                //'forceDelete'         => true, 
                'dynamicLangClass'      => true, //Set to true if you don't want to create a 'PostLang.php' in your models folder
            ),
        );
    }

    public function relations()
    {
        return array(
            'children' => array(self::HAS_MANY, 'MenuItem', 'parentItemId', 'order'=>'children.orderNum ASC'),
        );
    }

    public function attributeLabels()
    {
        return array_merge(
            $this->imageBehavior->imageLabels(),
            $this->orderBehavior->orderLabels(),
            $this->languageBehavior->languageLabels(array(
                'name' => 'Текст'
            )),
            array(
                'menuId' => 'Меню',
                'parentItemId' => 'Родительский раздел',
                'active' => 'Активный',
                'visible' => 'Показывать',
                'link' => 'Ссылка',
            )
        );
    }

    public function rules()
    {
        return array_merge(
            $this->imageBehavior->imageRules(),
            $this->orderBehavior->orderRules(),
            array(
                array('menuId, name, link', 'required'),
                array('active, visible', 'boolean'),
                array('parentItemId', 'numerical', 'integerOnly'=>true),
                
                array('menuId, name', 'safe', 'on'=>'search'),
            )
        );
    }

    public function scopes()
    {
        $alias = $this->getTableAlias();
        return array(
            'onSite' => array(
                'condition' => $alias.'.visible = 1',
            ),
            'active' => array(
                'condition' => $alias.'.active = 1',
            ),
            'orderDefault' => array(
                'order' => $alias.'.menuId ASC, '.$alias.'.parentItemId ASC, '.$alias.'.orderNum ASC',
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
                    'menuId' => CSort::SORT_ASC,
                    'orderNum' => CSort::SORT_ASC,
                )
            )
        ));
    }


    public function byParent($parent)
    {
        $alias = $this->getTableAlias();
        $this->getDbCriteria()->mergeWith(array(
            'condition' => $alias.'.parentItemId = '.$parent,
        ));
        return $this;
    }

    public function byMenuId($menuId)
    {
        $alias = $this->getTableAlias();
        $this->getDbCriteria()->mergeWith(array(
            'condition' => $alias.'.menuId = '.$menuId,
        ));
        return $this;
    }

    public function getIconUrl()
    {
        return $this->imageBehavior->getImageUrl();
    }

    protected function afterDelete()
    {
        $this->imageBehavior->imageAfterDelete();
        return parent::afterDelete();
    }

    protected function beforeFind()
    {
        // Поддержка многих языков после загрузки модели
        $this->languageBehavior->multilang();
        return parent::beforeFind();
    }

    protected function afterFind()
    {
        $this->imageBehavior->imageAfterFind();
        return parent::afterFind();
    }

    public function beforeSave()
    {
        $this->orderBehavior->orderBeforeSave();
        return parent::beforeSave();
    }
}
