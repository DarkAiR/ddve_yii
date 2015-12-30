<?php


class DocumentsValidator extends CFileValidator
{
    /**
     * @var boolean the attribute requires a file to be uploaded or not.
     */
    public $allowEmpty = false;

    /**
     * @var mixed a list of file name extensions that are allowed to be uploaded.
     * This can be either an array or a string consisting of file extension names
     * separated by space or comma (e.g. "gif, jpg").
     * Extension names are case-insensitive. Defaults to null, meaning all file name
     * extensions are allowed.
     */
    public $types;

    /**
     * @var string the error message used when the uploaded file has an extension name
     * that is not listed among {@link types}.
     */
    public $typesError;

    /**
     * @var int size of the image in kilo bytes
     */
    public $size;

    /**
     * @var string the size error message
     */
    public $sizeError;

    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     */
    protected function validateAttribute($object, $attribute)
    {
        $data = $object->$attribute;

        $checkEmpty = true;

        foreach ($data as $k=>$item) {
            if (empty($item))
                continue;
            
            $file = $item['name'];

            if (!$file instanceof CUploadedFile) {
                $file = CUploadedFile::getInstance($object, $attribute.'['.$k.']');
                if ($file === null)
                    continue;
                $checkEmpty = false;
            }

            if ($this->types !== null) {
                $types = (is_string($this->types))
                    ? preg_split('/[\s,]+/', strtolower($this->types), -1, PREG_SPLIT_NO_EMPTY)
                    : $this->types;

                if (!in_array(strtolower($file->getExtensionName()), $types)) {
                    $message = $this->typesError !== null ? $this->typesError : Yii::t('yii', 'The file "{file}" cannot be uploaded. Only files with these extensions are allowed: {extensions}.');
                    $this->addError($object, $attribute, $message, array('{file}' => $file->getName(), '{extensions}' => implode(', ', $types)));
                    return;
                }
            }

            $image_size = $this->bytesToKilobytes($file->getSize());

            if ($this->size !== null && $image_size != floor($this->size)) {
                $message = $this->sizeError ? $this->sizeError : Yii::t('yii', 'The file "{file}" is either small or large. Its size should be {limit} kilo bytes.');
                $this->addError($object, $attribute, $message, array('{file}' => $file->getName(), '{limit}' => $this->size));
                return;
            }
        }

        if ($checkEmpty)
            return $this->emptyAttribute($object, $attribute);
    }
    
    /**
     * @param int $bytes the image size in bytes
     * @return int the images size in kilo bytes
     */
    protected function bytesToKilobytes($bytes)
    {
        return floor(number_format($bytes / 1024, 2));
    }

    /**
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     * 
     * @return void
     */
    protected function emptyAttribute($object, $attribute)
    {
        if (!$this->allowEmpty) {
            $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} cannot be blank.');
            $this->addError($object, $attribute, $message);
        }
    }
}

?>