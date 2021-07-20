<?php


namespace antonyz89\mdl;

use Exception;
use kartik\grid\DataColumn as DataColumnBase;
use yii\helpers\ArrayHelper;

class DataColumn extends DataColumnBase
{
    public $headerOptions = ['class' => 'mdl-data-table__cell--non-numeric'];
    public $contentOptions = ['class' => 'mdl-data-table__cell--non-numeric'];

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return $model->{$this->value};
            }

            return call_user_func($this->value, $model, $key, $index, $this);
        }

        if ($this->attribute !== null) {
            return ArrayHelper::getValue($model, $this->attribute);
        }

        return null;
    }

}
