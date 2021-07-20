<?php

namespace antonyz89\mdl\widgets;

use antonyz89\mdl\Html;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class Card extends Widget
{
    /** @var string|null */
    public $title;

    /** @var string|null */
    public $content;

    /** @var array */
    public $options = [];

    public function init()
    {
        parent::init();

        $this->options['class'] = ArrayHelper::getValue($this->options, 'class', 'mdl-cell mdl-cell--12-col');

        $id = ArrayHelper::getValue($this->options, 'id');

        if ($id) {
            $this->setId($id);
        }

        echo Html::beginTag('div', $this->options) . "\n";
        echo Html::beginTag('div', 'mdl-card mdl-shadow--2dp') . "\n";
        $this->renderTitle();
        echo Html::beginTag('div', 'mdl-card__supporting-text') . "\n";
        $this->renderContent();
    }

    public function run()
    {
        echo "\n" . Html::endTag('div');
        echo "\n" . Html::endTag('div');
        echo "\n" . Html::endTag('div');
    }

    public function renderTitle(): void
    {
        if ($this->title) {
            echo Html::tag('div',
                    Html::tag('h2', $this->title, 'mdl-card__title-text')
                    , 'mdl-card__title'
                ) . "\n";
        }
    }

    public function renderContent(): void
    {
        if (is_array($this->content)) {
            echo implode("\n", $this->content);
        } else {
            echo $this->content;
        }
    }
}
