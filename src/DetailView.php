<?php


namespace antonyz89\mdl;

use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView as DetailViewBase;

class DetailView extends DetailViewBase
{
    public $options = ['class' => 'mdl-data-table mdl-js-data-table'];

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function renderAttribute($attribute, $index)
    {
        if (is_string($this->template)) {
            $captionOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'captionOptions', ['class' => 'mdl-data-table__cell--non-numeric']));
            $contentOptions = Html::renderTagAttributes(ArrayHelper::getValue($attribute, 'contentOptions', ['class' => 'mdl-data-table__cell--non-numeric']));

            return strtr($this->template, [
                '{label}' => $attribute['label'],
                '{value}' => $this->formatter->format($attribute['value'], $attribute['format']),
                '{captionOptions}' => $captionOptions,
                '{contentOptions}' => $contentOptions,
            ]);
        }

        return call_user_func($this->template, $attribute, $index, $this);
    }

}
