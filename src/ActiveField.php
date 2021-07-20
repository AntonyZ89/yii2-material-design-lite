<?php


namespace antonyz89\mdl;

use kartik\form\ActiveField as ActiveFieldBase;

class ActiveField extends ActiveFieldBase
{
    public $addClass = 'mdl-textfield__input';
    public $labelOptions = ['class' => 'mdl-textfield__label'];
    public $template = "{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}\n{label}";

    public $options = ['class' => 'mdl-textfield mdl-js-textfield mdl-textfield--floating-label full-size'];
    public $inputOptions = ['class' => 'mdl-textfield__input'];
}
