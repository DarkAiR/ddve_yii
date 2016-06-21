<?php

class DocumentsControllerBehavior extends CBehavior
{
    public $docField = '';
    public $innerField = '_docs';
    public $innerRemoveField = '_removeDocs';

    private static $files = null;

    public function docBeforeSave(&$model, $storagePath)
    {
        if (empty($this->docField))
            throw new Exception('docField have to be filled');

        if (!is_array($model->{$this->docField}))
            $model->{$this->docField} = array();

        $modelName = get_class($model);

        // Отмечаем какие файлы удалить, какие оставить, какие новые
        $files = $this->getInstancesByName($modelName.'['.$this->innerField.']');

        // $existsFiles - файлы, которые не трогать
        // $newFiles - новые файлы
        // $removeFiles - файлы для удаления
        list($existsFiles, $newFiles, $removeFiles) = isset($_POST[$modelName][$this->innerRemoveField])
            ? $this->getFixedFiles($model, $files, $_POST[$modelName][$this->innerRemoveField])
            : $this->getFlexibleFiles($model, $files);

        // Ничего не поменялось, выходим
        if (count($removeFiles)==0 && count($newFiles)==0)
            return;

        // Сохраняем во внутреннем поле, чтобы прошла валидация, но не портились реальные данные объекта
        foreach ($newFiles as $k => $file) {
            $fileName = $this->correctFileName($file->name);
            // Будет переписана старая запись о файле или добавлена новая
            $existsFiles[$k] = array('name'=>$fileName, 'size'=>$file->size);
        }
        ksort($existsFiles);
        $model->{$this->innerField} = $existsFiles;

        // Валидируем внутреннее поле
        if (!$model->validate(array($this->innerField))) {
            return;
        }



        if (!is_dir($storagePath))
            mkdir($storagePath, 0755, true);

        // Remove old files
        foreach ($removeFiles as $fileName) {
            @unlink( $storagePath.$fileName );
        }

        // Create new files
        foreach ($newFiles as $k => $file) {
            $fileName = $this->correctFileName($file->name);
            $file->saveAs( $storagePath.$fileName );
        }

        $model->{$this->docField} = $existsFiles;
    }

    /**
     * @return array(
     *              array existsFiles(array(name,size)),
     *              array newFiles(CUploadedFile),
     *              array removeFiles(name)
     *         )
     */
    private function getFixedFiles(&$model, &$files, &$removeFlags)
    {
        $existsFiles = array();
        $newFiles = array();
        $removeFiles = array();

        foreach ($model->{$this->docField} as $k => $doc) {
            $existsFiles[$k] = $model->{$this->docField}[$k];
        }

        // Идем по текущим файлам, отмечаем, какие должны быть удалены
        $keys = array();
        foreach ($existsFiles as $k => $doc) {
            $isChanged =    isset($files[$k])  &&  $files[$k]->error == UPLOAD_ERR_OK  &&  (
                                $doc['name'] != $this->correctFileName($files[$k]->name) ||
                                $doc['size'] != $files[$k]->size
                            );
            $isRemoved = isset($removeFlags[$k])  &&  $removeFlags[$k] == 1;

            if (!empty($doc['name'])  &&  ($isChanged || $isRemoved)) {
                $removeFiles[] = $doc['name'];
            }

            if ($isRemoved) {
                unset($existsFiles[$k]);
            }
        }

        // Идем по новым файлам, запоминаем в модели, какие были изменения
        foreach ($files as $k => $file) {
            if ($file->error != UPLOAD_ERR_OK)
                continue;
            $newFiles[$k] = $file;
        }

        return array(
            $existsFiles,
            $newFiles,
            $removeFiles
        );
    }

    /**
     * @return array(exists, new, remove)
     */
    private function getFlexibleFiles(&$model, &$files)
    {
        return array(
            $model->{$this->docField},
            array(),
            array()
        );
/*
        $newFiles = array();
        $existsFiles = array();

        foreach ($files as $file) {
            $bFind = false;
            foreach ($model->data as $k=>$v) {
                if ($v['size'] == $file->size && $v['name'] == $this->correctFileName($file->name)) {
                    $existsFiles[] = $v;
                    unset($model->data[$k]);
                    $bFind = true;
                    break;
                }
            }
            if (!$bFind) {
                $newFiles[] = $file;
            }
        }
        return array(
            $existsFiles,
            $newFiles,
            $model->data
        );*/
    }

    private function correctFileName($fileName)
    {
        //$fileName = str_replace(' ', '_', $fileName);
        $fileName = str_replace(':', '.', $fileName);
        return $fileName;
    }

    public function getInstancesByName($name)
    {
        if (self::$files === null)
            $this->prefetchFiles();

        $len = strlen($name);
        $results = array();
        foreach (array_keys(self::$files) as $key)
            if (strncmp($key, $name.'[', $len+1)===0  /*&&  self::$files[$key]->getError() != UPLOAD_ERR_NO_FILE*/)
                $results[] = self::$files[$key];
        return $results;
    }

    protected function prefetchFiles()
    {
        self::$files = array();
        if (!isset($_FILES) || !is_array($_FILES))
            return;

        foreach ($_FILES as $class=>$info)
            $this->collectFilesRecursive($class, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error']);
    }

    protected function collectFilesRecursive($key, $names, $tmp_names, $types, $sizes, $errors)
    {
        if (is_array($names)) {
            foreach ($names as $item=>$name) {
                $this->collectFilesRecursive($key.'['.$item.']', $names[$item], $tmp_names[$item], $types[$item], $sizes[$item], $errors[$item]);
            }
        } else {
            self::$files[$key] = new CUploadedFile($names, $tmp_names, $types, $sizes, $errors);
        }
    }
}