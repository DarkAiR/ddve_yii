<?php

/**
 * Статья
 */
class Article extends CActiveRecord
{
    // Размер изображений в геларее
    const IMAGE_W = 640;
    const IMAGE_H = 640;

    public $_images = [];                       // UploadedFile[]
    public $_removeGalleryImageFlags = [];      // bool

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
            'coordsBehavior' => array(
                'class'                 => 'application.behaviors.CoordsBehavior',
                'defaultLat'            => Yii::app()->params['defaultLatitude'],
                'defaultLng'            => Yii::app()->params['defaultLongitude'],
                'defaultZoom'           => Yii::app()->params['defaultZoom']
            ),
            'galleryBehavior' => array(
                'class'                 => 'application.behaviors.GalleryBehavior',
                'storagePath'           => 'articles',
                'imagesField'           => 'images',
            ),
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
            $this->coordsBehavior->coordsLabels(),
            $this->galleryBehavior->galleryLabels(),
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
        return array_merge(
            $this->coordsBehavior->coordsRules(),
            $this->galleryBehavior->galleryRules(),
            array(
                array('title, text, link', 'safe'),
                array('visible', 'boolean'),

                array('title, link, visible', 'safe', 'on'=>'search'),
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
            'onlyLinks' => array(
                'select' => array('link')
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

    public function getGalleryStorePath()
    {
        return $this->galleryBehavior->getStorePath();
    }

    public function getGalleryImageUrl($img)
    {
        return $this->galleryBehavior->getImageUrl($img);
    }

    public function getAbsoluteImagesUrl()
    {
        return $this->galleryBehavior->getAbsoluteImagesUrl();
    }

    public function getLat()
    {
        return $this->coordsBehavior->getLat();
    }

    public function getLng()
    {
        return $this->coordsBehavior->getLng();
    }

    public function getZoom()
    {
        return $this->coordsBehavior->getZoom();
    }

    protected function beforeFind()
    {
        // Поддержка многих языков после загрузки модели
        $this->languageBehavior->multilang();
        return parent::beforeFind();
    }
}
