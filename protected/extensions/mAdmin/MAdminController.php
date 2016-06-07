<?php

class MAdminController extends CExtController
{
    public $layout = false;

    /**
     * @var string Name of managed model
     */
    public $modelName = '';

    /**
     * @var array Склонение должно соответствовать словам соответственно: (добавить .., редактирование .., список ..)
     */
    public $modelHumanTitle = array('модель', 'модели', 'моделей');

    /**
     * @var string|string[] Allowed actions. Array or comma separated string. Possible values: add,view,edit,delete
     */
    public $allowedActions = 'add,edit,delete,update';

    /**
     * @var string Allowed roles.
     */
    public $allowedRoles = 'admin, moderator';

    /**
     * @var string One of AdminController->allowedActions
     */
    public $defaultAction = 'list';

    /**
     * @var string
     */
    public $assetsUrl;

    // Шаблоны
    protected $templateList = 'crud/list';

    public function init()
    {
        parent::init();

        // init admin component
        Yii::app()->getAdminComponent();

        $this->assetsUrl = Yii::app()->assetManager->publish(__DIR__ . '/assets');

        $this->viewPath = __DIR__ . '/views';
        
        /** @var $yiiTwigRenderer ETwigViewRenderer */
        $yiiTwigRenderer = Yii::app()->getComponent('viewRenderer');
        
        /** @var $twig_LoaderInterface Twig_Loader_Filesystem */
        $twig_LoaderInterface = $yiiTwigRenderer->getTwig()->getLoader();
        $twig_LoaderInterface->addPath($this->viewPath);
    }

    public function filters()
    {
        return array(
            'accessControl'
        );
    }

    private function getAllowedActions()
    {
        if (is_array($this->allowedActions))
            return $this->allowedActions;

        $res = explode(',', $this->allowedActions);
        array_walk(
            $res,
            function (&$value) {
                $value = trim($value);
            }
        );
        return $res;
    }

    private function getAllowedRoles()
    {
        if (is_array($this->allowedRoles))
            return $this->allowedRoles;

        $res = explode(',', $this->allowedRoles);
        array_walk(
            $res,
            function (&$value) {
                $value = trim($value);
            }
        );
        return $res;
    }

    public function accessRules()
    {
        $allowedActions = array_merge($this->getAllowedActions(), array('index', 'list'));
        return array(
            array(
                'allow',
                'actions' => $allowedActions,
                'roles' => $this->getAllowedRoles()
            ),
            array(
                'deny',
                'users' => array('*')
            ),
        );
    }

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionAdd()
    {
        $this->actionEdit(true);
    }

    /**
     * @param bool $createNew
     */
    public function actionEdit($createNew = false)
    {
        $model = $createNew
            ? new $this->modelName()
            : $this->loadModel();
        if (!$model)
            throw new CHttpException(404);

        if (isset($_POST[$this->modelName])) {
            foreach ($_POST[$this->modelName] as &$postValue) {
                if (is_string($postValue)) {
                    $postValue = trim($postValue);
                    if ($postValue === '') {
                        $postValue = null;
                    }
                }
            }

            $modifiedRelations = $this->setAttributes($model);

            $this->beforeSave($model);

            $validated = $model->validate();
            if ($validated) {
                foreach ($modifiedRelations as $relationName) {
                    $validated = $validated && $model->$relationName->validate();
                }
            }
            if ($validated) {
                $model->save(false);
                foreach ($modifiedRelations as $relationName) {
                    $model->$relationName->save(false);
                }

                $this->afterSave($model);
                $this->redirect(array('/' . $this->getUniqueId()));
            }
        }

        $this->beforeEdit($model);
        $this->render(
            'crud/' . ($createNew ? 'add' : 'edit'),
            array(
                'model' => $model,
                'editFormElements' => $this->getEditFormElements($model),
            )
        );
    }

    /**
     * @param  CActiveRecord $model
     * @return string[]      modified relations
     */
    private function setAttributes($model)
    {
        $modifiedRelations = array();
        $modelName = get_class($model);
        
        $this->beforeSetAttributes($model, $_POST[$modelName]);
        $model->setAttributes($_POST[$modelName]);
        
        foreach ($model->relations() as $relationName => $relationAttributes) {
            // process MANY_MANY relations
            if (isset($_POST[$modelName][$relationName])) {
                $tmp = $_POST[$modelName][$relationName];
                if (!is_array($tmp))
                    $tmp = array($tmp);
                $model->$relationName = $tmp;
            }

            // process HAS_ONE or BELONGS_TO relations
            if (isset($_POST[$relationAttributes[1]])) {
                if (!is_array($model->$relationName)) {
                    $modifiedRelations[] = $relationName;
                    $modifiedRelations += $this->setAttributes($model->$relationName);
                }
            }
        }

        return $modifiedRelations;
    }

    public function actionView()
    {
        $model = $this->loadModel();
        if (!$model)
            throw new CHttpException(404);
        $this->render(
            'crud/view',
            array(
                'model' => $model,
                'editFormElements' => $this->getEditFormElements($model),
            )
        );
    }

    public function actionList()
    {
        // Создаем через ::model(), чтобы не выставлялись значения из метаданных, которые влияют на поиск и портят его
        //$model = new $this->modelName('search');
        $modelName = $this->modelName;
        $model = $modelName::model();
        $model->setScenario('search');

        $this->beforeList($model, $_GET[$this->modelName]);
        if (isset($_GET[$this->modelName])) {
            $model->attributes = $_GET[$this->modelName];
        }

        // Оставляем только search атрибуты
        $validators = $model->getValidators();
        $attributes = array();
        foreach ($validators as $v) {
            if (in_array('search', $v->on))
                $attributes = array_merge($attributes, $v->attributes);
        }
        $columnsArr = $this->getTableColumns();
        $columns = array();
        foreach ($columnsArr as $c) {
            if (!is_array($c)) {
                if (!in_array($c, $attributes))
                    $columns[] = array('name'=>$c, 'filter'=>false);
                else
                    $columns[] = array('name'=>$c);
            } elseif (isset($c['value']) && $c['value'] instanceof Closure) {
                $c['filter'] = false;
                $columns[] = $c;
            } else {
                if (isset($c['class'])) {
                    if (!isset($c['name'])  ||  (isset($c['name'])  &&  !in_array($c['name'], $attributes)))
                        $c['filter'] = false;
                }
                $columns[] = $c;
            }
        }

        $this->render(
            $this->templateList,
            array(
                'model' => $model,
                'columns' => $columns,
                'canAdd' => in_array('add', explode(',', $this->allowedActions)),
            )
        );
    }

    public function actionDelete()
    {
        if (!Yii::app()->request->isPostRequest)
            throw new CHttpException(400);

        // we only allow deletion via POST request
        $model = $this->loadModel();
        if (!$model)
            throw new CHttpException(404);
        $model->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (Yii::app()->request->isAjaxRequest)
            Yii::app()->end();
        
        $this->redirect(array($this->getId()));
    }

    public function actionUpdate()
    {
        Yii::import('ext.mAdmin.components.AdminEditableSaver');
        $es = new AdminEditableSaver($this->modelName);
        $es->update();
    }

    /**
     * internal
     * @return CActiveRecord
     * @throws CHttpException
     */
    public function loadModel()
    {
        $id = Yii::app()->request->getQuery('id');
        $model = $id ? CActiveRecord::model($this->modelName)->findbyPk($id) : null;
        if ($model === null)
            return false;
        return $model;
    }

    /**
     * Получаем многоязыковое поле для редактирования
     */
    protected function getLangField($name, $options)
    {
        // Если язык один, то делаем все как обычно без языка
        if (count(Yii::app()->params['languages'])<=1)
            return array($name => $options);

        // Если языков несколько, то выводим табы
        $res = array();
        $res[] = '<div class="language-row">';

        $tabsHtml = '';
        foreach (Yii::app()->params['languages'] as $lang => $langName) {
            $tabId = ($lang == Yii::app()->sourceLanguage) ? $name : $name.'_'.$lang;
            $active = ($lang == Yii::app()->sourceLanguage) ? 'active' : '';
            $tabsHtml .= '<li role="presentation" class="'.$active.'"><a href="#'.$tabId.'" aria-controls="'.$tabId.'" role="tab" data-toggle="tab">'.$langName.'</a></li>';
        }
        $res[] = '<ul class="nav nav-tabs" role="tablist">'.
                    $tabsHtml.
                 '</ul>'.
                 '<div class="tab-content">';

        foreach (Yii::app()->params['languages'] as $lang => $langName) {
            $tabId = ($lang == Yii::app()->sourceLanguage) ? $name : $name.'_'.$lang;
            $active = ($lang == Yii::app()->sourceLanguage) ? 'active' : '';
            $res[] = '<div role="tabpanel" class="tab-pane '.$active.'" id="'.$tabId.'">';
            $res[$tabId] = $options;
            $res[] = '</div>';
        }

        // Добавляем кнопку перевода
        $res[] = $this->addTranslateButton($name);

        $res[] = '</div>';
        $res[] = '</div>';

        return $res;
    }

    private function addTranslateButton($fieldName)
    {
        $model = $this->loadModel();
        if (!$model)
            return;

        $res = '';

        // Добавляем кнопку перевода
        $translateBtnId = 'translateBtn'.rand();
        $res .= '<button class="btn" id="'.$translateBtnId.'">Перевести</button>';
        $options = CJavaScript::encode(array(
            'modelName' => $this->modelName,
            'modelId' => Yii::app()->request->getQuery('id'),
            'fieldName' => $fieldName,
            'url' => CHtml::normalizeUrl(array('/admin/translate'))
        ));
        AdminComponent::getInstance()->assetsRegistry->registerPackage('translate');
        $js = "$('#{$translateBtnId}').translate({$options});";
        Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$translateBtnId, $js);

        // Проверяем, на какие языки не перевели
        $arr = array();
        foreach (Yii::app()->params['languages'] as $lang => $langName) {
            if ($lang == Yii::app()->sourceLanguage)
                continue;
            $fieldNameTmp = $fieldName.'_'.$lang;
            $fieldValue = trim($model->$fieldNameTmp);
            if (empty($fieldValue))
                $arr[] = $lang;
        }
        if (!empty($arr)) {
            $res .= '&nbsp;&nbsp;&nbsp;<span class="text-danger"><i class="ace-icon fa fa-exclamation-triangle"></i> Не переведено на ['.implode('], [', $arr).']</span>';
        }
        return $res;
    }

    /**
     * Example:
     * <code>
     * return array(
     *     'attributeName1',
     *     array(
     *         'class' => 'alias.to.children.of.CGridColumn',
     *         'property1' => 'value',
     *         'property2' => 'value',
     *     ),
     *     array(
     *         'name' => 'attributeName2',
     *         'filter' => array(1 => 'Value 1', 2 => 'Value 2'),
     *         'sortable' => false,
     *         'value' => '$data->getHumanAttributeText()',
     *     ),
     * );
     * </code>
     * @see CGridView::$columns
     * @return array
     */
    public function getTableColumns()
    {
        $model = CActiveRecord::model($this->modelName);
        $attributes = $model->getAttributes();
        unset($attributes[$model->metaData->tableSchema->primaryKey]);
        $columns = array_keys($attributes);

        $columns[] = $this->getButtonsColumn();

        return $columns;
    }

    /**
     * internal
     * returns columns with buttons such as edit, view, delete
     * @return array
     */
    public function getButtonsColumn()
    {
        $allowedActions = explode(',', $this->allowedActions);
        $allowDelete = in_array('delete', $allowedActions);
        $allowView = in_array('view', $allowedActions);
        $allowEdit = in_array('edit', $allowedActions);

        $template = '';
        if (!$allowEdit && $allowView) {
            $template = '{view}';
        } elseif ($allowEdit) {
            $template = '{update}';
        }
        if ($allowDelete) {
            if (!empty($template)) {
                $template .= '&nbsp;&nbsp;&nbsp;{delete}';
            } else {
                $template = '{delete}';
            }
        }

        return array(
            'class' => 'ext.mAdmin.widgets.AdminButtonColumnWidget',
            'template' => $template,
            'viewButtonOptions' => array('class'=>'view blue'),
            'updateButtonOptions' => array('class'=>'update green'),
            'deleteButtonOptions' => array('class'=>'delete red'),
            'updateButtonUrl' => 'Yii::app()->controller->createUrl("edit",array("id"=>( isset($data->primaryKey) ? $data->primaryKey : (is_array($data)&&isset($data["id"])?$data["id"]:"") )))',
            'deleteButtonUrl' => 'Yii::app()->controller->createUrl("delete",array("id"=>( isset($data->primaryKey) ? $data->primaryKey : (is_array($data)&&isset($data["id"])?$data["id"]:"") )))'
        );
    }

    public function getImageColumn($name, $pathExprMethod)
    {
        return array(
            'class' => 'ext.mAdmin.widgets.AdminImageColumnWidget',
            'header' => '<div style="width:100%; text-align:center"><i class="fa fa-image"></i></div>',
            'imagePathExpression' => '$data->'.$pathExprMethod,
            'usePlaceKitten' => false,
            'imageOptions' => array(
                'style' => 'height:36px !important;'
            )
        );
    }

    public function getVisibleColumn()
    {
        return $this->getBooleanColumn('visible', 'fa fa-eye');
    }

    public function getBooleanColumn($name, $icon=null)
    {
        $label = CActiveRecord::model($this->modelName)->getAttributeLabel($name);
        $header = ($icon === null)
            ? $label
            : '<a href="javascript:void(0)" title="'.$label.'"><div style="width:100%; text-align:center"><i class="'.$icon.'"></i></div></a>';

        return array(
            'class' => 'ext.mAdmin.widgets.AdminEditableColumnWidget',
            'name' => $name,
            'header' => $header,
            'htmlOptions' => array(
                'width' => '32',
            ),
            'headerHtmlOptions' => array(
                'width' => '32',
            ),
            'editable' => array(
                'title' => $label,
                'type' => 'select',
                'source' => array(0=>'Нет', 1=>'Да'),
                'display' => 'js: function(value, sourceData) {
                    var el = $("<div>");
                    var color = (value==0) ? "#c00" : "#080";
                    el.css("color", color).text(sourceData[value]["text"]);
                    $(this).html(el.get(0));
                }',
                'url' => $this->createUrl('update'),
            )
        );
    }

    public function getSelectColumn($name, $data)
    {
        $label = CActiveRecord::model($this->modelName)->getAttributeLabel($name);
        return array(
            'class' => 'ext.mAdmin.widgets.AdminEditableColumnWidget',
            'name' => $name,
            'header' => $label,
            'editable' => array(
                'title' => $label,
                'type' => 'select',
                'source' => $data,
                'url' => $this->createUrl('update'),
            )
        );
    }

    public function getOrderColumn($name = 'orderNum')
    {
        $label = CActiveRecord::model($this->modelName)->getAttributeLabel($name);
        return array(
            'class' => 'ext.mAdmin.widgets.AdminEditableColumnWidget',
            'name' => $name,
            'header' => '<div style="width:100%; text-align:center"><i class="fa fa-arrow-up"></i><i class="fa fa-arrow-down" style="margin-left:-4px"></i></div>',
            'htmlOptions' => array(
                'width' => '32',
            ),
            'headerHtmlOptions' => array(
                'width' => '32',
            ),
            'editable' => array(
                'title' => $label,
                'url' => $this->createUrl('update'),
            )
        );
    }

    /**
     * Example:
     * <code>
     * return array(
     *      'attributeName1' => array(
     *          'type' => 'textField', // One of methods in AdminActiveFormWidget::*Row
     *      ),
     *      array(
     *          'class' => 'yii.class.alias', // i.e. application.extensions.DependedInputWidget
     *          'attribute1' => 'value1',
     *          'attribute2' => 'value2',
     *      ),
     *      '<h1>Raw html</h1>',
     *      'attributeName2' => array(
     *          'class' => 'yii.class.alias2',
     *      ),
     *      'attributeName3' => array(
     *          'type' => 'dropDownList', // Will be called AdminActiveFormWidget::DropDownListRow
     *          'data' => CHtml::listData(Client::model()->findAll(), 'id', 'name'),
     *          'htmlOptions' => array(
     *              'empty' => 'Empty',
     *          ),
     *      ),
     *      'relationName' => array(
     *          'rows' => array( same structure as root array ),
     *      ),
     *  );
     * </code>
     *
     * @param  CActiveRecord $model
     * @return array
     */
    public function getEditFormElements($model)
    {
        return array();
    }

    /**
     * @param CActiveRecord $model
     * @param array         $attributes
     */
    public function beforeSetAttributes($model, &$attributes)
    {
    }

    /**
     * @param CActiveRecord $model
     * @param array         $attributes
     */
    public function beforeList($model, &$attributes)
    {
    }

    /**
     * @param CActiveRecord $model
     */
    public function beforeSave($model)
    {
    }

    /**
     * @param CActiveRecord $model
     */
    public function afterSave($model)
    {
    }

    /**
     * @param CActiveRecord $model
     */
    public function beforeEdit($model)
    {
    }
}
