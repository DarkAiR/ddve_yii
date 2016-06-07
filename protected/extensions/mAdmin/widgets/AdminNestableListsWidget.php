<?php

class AdminNestableListsWidget extends CWidget
{
    public $dataProvider = null;
    public $orderNumField = '';
    public $activeField = '';
    public $visibleField = '';
    private $index;


    public function init()
    {
        parent::init();
        AdminComponent::getInstance()->assetsRegistry->registerPackage('bootbox');
        AdminComponent::getInstance()->assetsRegistry->registerPackage('nestable-list');

        $this->registerClientScript();
    }

    public function run()
    {
        if (empty($this->dataProvider))
            return;

        $this->index = 1;
        $data = $this->dataProvider->getData();

        echo <<<EOD
            <div class="dd dd-draghandle">
                <ol class="dd-list">
EOD;
        foreach ($data as $item) {
            $this->renderItem($item);
        }
        echo <<<EOD
                </ol>
            </div>
EOD;
    }

    private function renderItem(&$item)
    {
        $id = isset($item->primaryKey)
            ? $item->primaryKey
            : (isset($item->id) ? $item->id : "");
        $updateButtonUrl = Yii::app()->controller->createUrl("edit", array("id" => $id));
        $deleteButtonUrl = Yii::app()->controller->createUrl("delete", array("id" => $id));

        $orderDataStr = empty($this->orderNumField) ? '' : 'data-order="'.$item->{$this->orderNumField}.'"';

        $visibleHtml = '';
        $itemClass = '';
        if (!empty($this->visibleField)) {
            if ($item->{$this->visibleField}) {
                $visibleHtml = '<i class="normal-icon  ace-icon fa fa-eye green bigger-130"></i>';
                $itemClass = 'item-green';
            } else {
                $visibleHtml = '<i class="normal-icon  ace-icon fa fa-eye-slash light-grey bigger-130"></i>';
            }
        }

        $activeClass = '';
        if (!empty($this->activeField)) {
            if (!$item->{$this->activeField})
                $activeClass = 'btn-light';
        }

        echo <<<EOD
            <li class="dd-item {$itemClass}" data-id="{$this->index}" data-obj-id="{$item->id}" {$orderDataStr}'>
                <div class="dd-handle dd2-handle">
                    {$visibleHtml}
                    <i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>
                </div>
                <div class="dd2-content {$activeClass}">
                    {$item->name}
                    <div class="pull-right action-buttons  dd-nodrag">
                        <a class="blue" href="{$updateButtonUrl}">
                            <i class="ace-icon fa fa-pencil bigger-130"></i>
                        </a>
                        <a class="red  js-delete-btn" href="{$deleteButtonUrl}">
                            <i class="ace-icon fa fa-trash-o bigger-130"></i>
                        </a>
                    </div>
                </div>
EOD;
        $this->index++;
        if (count($item->children)) {
            echo '<ol class="dd-list">';
            foreach ($item->children as $child) {
                $this->renderItem($child);
            }
            echo '</ol>';
        }
        echo '</li>';
    }

    public function registerClientScript()
    {
        $orderUrl = Yii::app()->controller->createUrl("order");

        Yii::app()->clientScript->registerScript(
            __CLASS__,
            <<<EOD
                var neastableListPrevId = null;
                var neastableListNextId = null;
                var neastableListParentId = null;

                $('.dd')
                    .nestable({
                        'handleClass': 'dd-handle'       // change class for disable drag
                    })
                    .on('dragStart', function(ev, item) {
                        var prev = item.prev('li.dd-item');
                        var next = item.next('li.dd-item');
                        var parent = item.parent('ol').parent('li.dd-item');
                        neastableListPrevId = (prev.length > 0) ? prev.data('id') : 0;
                        neastableListNextId = (next.length > 0) ? next.data('id') : 0;
                        neastableListParentId = (parent.length > 0) ? parent.data('id') : 0;
                        return true;
                    })
                    .on('change', function(ev, item) {
                        var prev = item.prev('li.dd-item');
                        var next = item.next('li.dd-item');
                        var parent = item.parent('ol').parent('li.dd-item');
                        var prevId = (prev.length > 0) ? prev.data('id') : 0;
                        var nextId = (next.length > 0) ? next.data('id') : 0;
                        var parentId = (parent.length > 0) ? parent.data('id') : 0;

                        if (neastableListPrevId == prevId  &&  neastableListNextId == nextId  &&  neastableListParentId == parentId)
                            return;

                        var newId = (next.length > 0)
                            ? next.data('order')
                            : (prev.length > 0
                                ? prev.data('order') + 1
                                : 0
                            );
                        var parentObjId = (parent.length > 0) ? parent.data('obj-id') : 0;

                        $.ajax({
                            url: '$orderUrl?id='+item.data('obj-id'),
                            type: 'POST',
                            data: {'order':newId, 'parent':parentObjId},
                            error: function(data) {
                                bootbox.alert('<i class="bigger-130 ace-icon fa fa-exclamation-triangle orange2"></i>&nbsp;&nbsp;&nbsp;Не получилось переместить элемент!', function() {
                                    window.location.reload();
                                });
                            }
                        });    
                        return true;
                    });
                $(function() {
                    $('.dd .js-delete-btn').click(function() {
                        var btn = $(this);
                        bootbox.confirm("Удалить?", function(result) {
                            if (!result)
                                return;

                            var url = btn.attr('href');
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: '',
                                success: function (data) {
                                    window.location.reload(true);
                                },
                                error: function(data) {
                                    bootbox.alert('<i class="bigger-130 ace-icon fa fa-exclamation-triangle orange2"></i>&nbsp;&nbsp;&nbsp;Не получилось удалить элемент!');
                                }
                            });                            
                        }); 
                        return false;
                    });
                });
EOD
        );
    }
}