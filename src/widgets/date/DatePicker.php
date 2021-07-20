<?php


namespace antonyz89\mdl\widgets\date;

use kartik\date\DatePicker as DatePickerBase;

class DatePicker extends DatePickerBase
{
    public $addInputCss = 'mdl-textfield__input';
    public $pickerIcon = '<i class="material-icons">calendar_today</i> ';
    public $removeIcon = '<i class="material-icons">close</i> ';
}
