<?php

/**
 * @property int id
 * @property string email
 * @property string $hashedPassword
 *
 * @property string $password
 */
class User extends CActiveRecord
{
    /**
     * @static
     * @param string $className
     * @return User
     */
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
        );
    }

    public function relations()
    {
        return array(
            'authItems' => array(self::MANY_MANY, 'AuthItem', 'AuthAssignment(userid, itemname)'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'email' => 'E-mail',
            'password' => 'Пароль',
            'authItems' => 'Права',
            'authItemsStr' => 'Права',
        );
    }

    public function rules()
    {
        return array(
            array('email', 'required'),
            array('email', 'email'),
            array('email', 'unique'),
            array('password', 'PasswordIsSetValidator'),

            array('email', 'safe', 'on' => 'search'),
        );
    }

    public function PasswordIsSetValidator($attribute, $params)
    {
        if (empty($this->hashedPassword))
            $this->addError($attribute, 'Необходимо ввести пароль');
    }

    public function getAuthItemsStr()
    {
        $arr = array();
        foreach ($this->authItems as $a)
            $arr[] = $a->name;
        return implode(', ', $arr);
    }

    public function getPassword()
    {
        return null;
    }

    public function setPassword($value)
    {
        $this->hashedPassword = $this->hashPassword($value);
    }

    /**
     * @param string $password
     * @return string
     */
    public static function hashPassword($password)
    {
        return md5($password . Yii::app()->params['md5Salt']);
    }

    public function emailExists()
    {
        $criteria=new CDbCriteria;
        $criteria->compare('email', $this->email, true);
        $user = new User;
        return $user->count($criteria);
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('email', $this->email, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}
