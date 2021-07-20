<?php


namespace antonyz89\mdl;

use Exception;
use kartik\file\FileInput as FileInputBase;
use yii\helpers\ArrayHelper;

class FileInput extends FileInputBase
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function init()
    {
        $this->field->options['class'] = str_replace('mdl-js-textfield', '', ArrayHelper::getValue($this->field->options, 'class')) . ' is-dirty';
        parent::init();

    }
}
