<?php


namespace antonyz89\mdl\widgets;


use Exception;
use yii\helpers\ArrayHelper;

/**
 * Class Widget
 * @package antonyz89\mdl\widgets
 *
 * @property-read array $data
 */
abstract class Widget extends \yii\base\Widget
{
    public $options = [];
    public $clientOptions = [];

    protected $_data;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        } else {
            $this->setId($this->options['id']);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getData(): array
    {
        if ($this->_data === null) {
            $this->_data = ArrayHelper::getValue($this->clientOptions, 'data', []);
            unset($this->clientOptions['data']);
        }

        return $this->_data;
    }
}
