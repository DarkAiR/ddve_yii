<?php

Yii::import('ext.localConfig.*');

class AdminLocalConfigController extends MAdminController
{
    public $modelName = 'LocalConfigItem';
    public $modelHumanTitle = array('настройку', 'настройки', 'настроек');
    public $allowedActions = 'edit,update';
    public $allowedRoles = 'admin, manager';

    /**
     * @param  User  $model
     * @return array
     */
    public function getEditFormElements($model)
    {
        $res = array(
            'name' => array(
                'type' => 'textField',
                'htmlOptions' => array(
                    'disabled' => true
                )
            ),
            'value' => '',
            'example' => array(
                'type' => 'textField',
                'htmlOptions' => array(
                    'disabled' => true
                )
            ),
            'description' => array(
                'type' => 'textField',
                'htmlOptions' => array(
                    'disabled' => true
                )
            ),
        );

        switch ($model->type) {
            case LocalConfigItem::TYPE_STRING:
            case LocalConfigItem::TYPE_INT:
                $res['value'] = array(
                    'type'=> 'textField',
                );
                break;
            case LocalConfigItem::TYPE_MULTILINESTRING:
                $res['value'] = array(
                    'type'=> 'textArea',
                );
                break;
            case LocalConfigItem::TYPE_BOOL:
                $res['value'] = array(
                    'type'=> 'checkbox',
                );
                break;
//            case LocalConfigItem::TYPE_FIXEDARRAY:
//                $res['value'] = array(
//                    'type'=> 'admin.components.EditArrayWidget',
//                    'fixed' => true,
//                    'example' => $model->example
//                );
//                break;
            case LocalConfigItem::TYPE_DYNAMICARRAY:
                $res['value'] = array(
                    'class' => 'application.components.admin.EditArrayWidget',
                    'example' => $model->getExampleDecode(),
                    'label' => ''
                );
                break;
//            case LocalConfigItem::TYPE_FILE:
//                $validateParams = localConfigValidateHelper::getParams();
//                $res['_file'] = array(
//                    'fileUidAttribute' => 'value',
//                    'removeFileAttribute' => '_file_delete',
//                );
//                if (isset($validateParams[$model->name]['type']) && $validateParams[$model->name]['type'] == 'image') {
//                    $res['_file']['type'] = 'ext.htmlextended.components.HtmlSinglePhotoWidget';
//                    if($model->name == 'favicon')
//                        $res['_file']['width'] = false;
//                } else {
//                    $res['_file']['type'] = 'ext.htmlextended.components.HtmlSingleFileWidget';
//                }
//                break;
//            case LocalConfigItem::TYPE_TWOPOWARRAY:
//                $res['value'] = array(
//                    'type' => 'admin.components.EditArrayWidget',
//                    'numeric' => true,
//                    'example' => $model->example
//                );
//                break;
        }
        return $res;
    }

    public function getTableColumns()
    {
        $attributes = array(
            'module',
            'name',
            array(
                'name' => 'value',
                'value' => function($data, $row) {
                    if (is_array($data->value)) {
                        echo implode('<br/>', $data->value);
                    } else {
                        echo $data->value;
                    }
                }
            ),
            'description',
            $this->getButtonsColumn(),
        );
        return $attributes;
    }

    /**
     * @param  LocalConfigItem $model
     * @return void
     */
    public function beforeSave($model)
    {
        parent::beforeSave($model);

        $value = $model->value;
        if (is_array($value) && in_array($model->type, array(LocalConfigItem::TYPE_DYNAMICARRAY, LocalConfigItem::TYPE_TWOPOWARRAY)))
            foreach($value as $id => $val)
                if (empty($val))
                    unset($value[$id]);
        $model->value = $value;
    }
}
