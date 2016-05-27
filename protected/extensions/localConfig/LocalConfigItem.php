<?php

class LocalConfigItem extends CActiveRecord
{
    const TYPE_BOOL             = 'bool';
    const TYPE_INT              = 'int';
    const TYPE_FIXEDARRAY       = 'fixedarray';
    const TYPE_DYNAMICARRAY     = 'dynamicarray';
    const TYPE_STRING           = 'string';
    const TYPE_MULTILINESTRING  = 'multilinestring';
    const TYPE_FILE             = 'file';
    const TYPE_TWOPOWARRAY      = 'twopowarray'; // Массив, ключами которого являются степени двойки

//    public $_file = null;
//    public $_file_delete = null;

    public $value = null;
    public $example = null;

    /**
     * @static
     * @param  string $className
     * @return LocalConfigItem|CActiveRecord
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'localconfig';
    }

    public function attributeLabels()
    {
        return array(
            'name'          => 'Имя параметра',
            'value'         => 'Значение',
//            '_file'         => 'Значение',
//            '_file_delete'  => 'Удалить и использовать пример',
            'example'       => 'Пример',
            'description'   => 'Описание',
            'module'        => 'Модуль'
        );
    }

    public function rules()
    {
        return array(
            array('value', 'localConfigValueValidator'),

        );
    }

    public function scopes()
    {
        return array(
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => array(
                    'id' => CSort::SORT_ASC,
                )
            )
        ));
    }

    public function localConfigValueValidator($attribute,$params)
    {
        if ($this->type == self::TYPE_INT) {
            $numericValidator = CValidator::createValidator('CNumberValidator', $this, $attribute, array('allowEmpty' => false, 'integerOnly' => true));
            $numericValidator->validate($this);
        }
    }

    public function byModule($name='')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'module = :module',
            'params' => array(
                ':module' => $name,
            )
        ));
        return $this;
    }

    public function byName($name='')
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'name = :name',
            'params' => array(
                ':name' => $name,
            )
        ));
        return $this;
    }

    public function getExampleDecode()
    {
        return json_decode($this->example, true);
    }

    public static function checkReturnValue($type, $value)
    {
        switch ($type) {
//          case self::TYPE_FIXEDARRAY:
            case self::TYPE_DYNAMICARRAY:
//          case self::TYPE_TWOPOWARRAY:
                $this->value = json_decode($this->value, true);
                break;
            case self::TYPE_BOOL:               $value = (bool)$value;      break;
            case self::TYPE_INT:                $value = (int)$value;       break;
            case self::TYPE_STRING:             $value = (string)$value;    break;
            case self::TYPE_MULTILINESTRING:    $value = (string)$value;    break;
//          case self::TYPE_FILE:               $value = (string)$value;    break;
        }
        return $value;
    }

    public static function convertToOriginalType($type, $value)
    {
        switch ($type) {
//          case self::TYPE_FIXEDARRAY:
            case self::TYPE_DYNAMICARRAY:
//          case self::TYPE_TWOPOWARRAY:
                $value = json_encode($value);
                break;
            case self::TYPE_BOOL:               $value = $value ? '1' : '0';    break;
            case self::TYPE_INT:                $value = (string)$value;        break;
            case self::TYPE_STRING:             $value = (string)$value;        break;
            case self::TYPE_MULTILINESTRING:    $value = (string)$value;        break;
//          case self::TYPE_FILE:               $value = (string)$value;        break;
        }
        return $value;
    }

    protected function afterFind()
    {
        parent::afterFind();

        // Возвращаем именно тот тип данных, в котором хранится конфиг
        $this->value = self::checkReturnValue($this->type, $this->value);
    }

    protected function beforeSave()
    {
        $this->value = self::convertToOriginalType($this->type, $this->value);

        // Расстановка степеней двойки в качестве ключей массива
//        if ($this->type == self::TYPE_TWOPOWARRAY) {
//            $res = array();
//            foreach ($this->value as $id => $v)
//                $res[pow(2, $id + 1)] = $v;
//            $this->value = json_encode($res);
//            $this->example = json_encode($this->example);
//        }

        // Удаляем файл
//        if ($this->_file_delete && $this->value != $this->example) {
//            $userFilesManager = Yii::app()->getComponent('userFilesManager');
//            $userFilesManager->deleteFileByUid($this->value);
//            $this->value = $this->example;
//        }

        // Загружаем файл
//        if ($this->_file || $this->_file = CUploadedFile::getInstance($this, '_file')) {
//            $userFilesManager = Yii::app()->getComponent('userFilesManager');
//            $this->value = $userFilesManager->publishFile($this->_file->getTempName(), $this->_file->getExtensionName())->getUID();
//            if (empty($this->value))
//                $this->addError($attribute, 'Файл <'.$this->_file->getTempName.'>не загружен');
//        }

        return parent::beforeSave();
    }

    /**
     * @param  null         $param
     * @return array|string
     */
    public function getPrintable($param = null)
    {
        // Проверяем что $param был передан, он существует и имеет область видимости public, иначе возвращаем пустую строку
        // В случае исключения возвращаем текст сообщение об ошибке
        try {
            $checker = new ReflectionProperty(__CLASS__, $param);
            if (is_null($param) || !isset($this->$param) || !$checker->isPublic())
                return '';
        } catch (ReflectionException $e) {
            return $e->getMessage();
        }


        return StringUtils::getPrintableRepresentation($this->$param);
/*
        if ($this->type != self::TYPE_FILE)
            return StringUtils::getPrintableRepresentation($this->$param);

        $validateParams = localConfigValidateHelper::getParams();
        if (isset($validateParams[$this->name]) && is_array($validateParams[$this->name])) {
            $_params = $validateParams[$this->name];
        }
        $userFilesManager = Yii::app()->getComponent('userFilesManager');
        $fileUrl = $userFilesManager->getUrlByFileUid($this->$param);

        if (isset($_params['type']) && $_params['type'] === 'image')
            return '<img src="' . $fileUrl . '" />';
        return '<a href="' . $fileUrl . '" >' . $this->$param . '</a>';
*/
    }

//    public function isFile()
//    {
//        return $this->type == self::TYPE_FILE ? true : false;
//    }
}
