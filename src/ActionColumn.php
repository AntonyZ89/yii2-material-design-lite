<?php


namespace antonyz89\mdl;

use Exception;
use kartik\grid\ActionColumn as ActionColumnBase;
use kartik\grid\GridView;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class ActionColumn extends ActionColumnBase
{
    public $headerOptions = ['style' => 'text-align: center'];
    public $contentOptions = ['style' => 'text-align: center'];
    public $width = false;

    public $buttonOptions = ['class' => Html::BUTTON_ICON_CLASS . ' ' . Html::BUTTON_COLORS['white']];
    public $viewOptions = ['class' => Html::BUTTON_ICON_CLASS . ' ' . Html::BUTTON_COLORS['white']];
    public $updateOptions = ['class' => Html::BUTTON_ICON_CLASS . ' ' . Html::BUTTON_COLORS['teal']];
    public $deleteOptions = ['class' => Html::BUTTON_ICON_CLASS . ' ' . Html::BUTTON_COLORS['red']];

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->initColumnSettings([
            'hiddenFromExport' => true,
            'mergeHeader' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'vAlign' => GridView::ALIGN_BOTTOM,
            'width' => '50px',
        ]);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->_isDropdown = ($this->grid->bootstrap && $this->dropdown);
        if (!isset($this->header)) {
            $this->header = Yii::t('kvgrid', 'Actions');
        }
        $this->parseFormat();
        $this->parseVisibility();
        parent::init();
        $this->initDefaultButtons();
        $this->setPageRows();
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    protected function initDefaultButtons()
    {
        $this->setDefaultButton('view', Yii::t('kvgrid', 'View'), 'visibility');
        $this->setDefaultButton('update', Yii::t('kvgrid', 'Update'), 'edit');
        $this->setDefaultButton('delete', Yii::t('kvgrid', 'Delete'), 'delete');
    }

    /**
     * Sets a default button configuration based on the button name (bit different than [[initDefaultButton]] method)
     *
     * @param string $name button name as written in the [[template]]
     * @param string $title the title of the button
     * @param string $icon the meaningful glyphicon suffix name for the button
     * @throws InvalidConfigException
     */
    protected function setDefaultButton($name, $title, $icon)
    {
        if (isset($this->buttons[$name])) {
            return;
        }
        $this->buttons[$name] = function ($url) use ($name, $title, $icon) {
            $opts = "{$name}Options";
            $options = ['title' => $title, 'aria-label' => $title, 'icon' => $icon, 'data-pjax' => '0'];
            if ($name === 'delete') {
                $item = $this->grid->itemLabelSingle ?? Yii::t('kvgrid', 'item');
                $options['data-method'] = 'post';
                $options['data-confirm'] = Yii::t('kvgrid', 'Are you sure to delete this {item}?', ['item' => $item]);
            }
            $options = array_replace_recursive($options, $this->buttonOptions, $this->$opts);
            $label = $this->renderLabel($options, $title, ['class' => 'material-icons', 'aria-hidden' => 'true']);
            $link = Html::a($label, $url, $options);
            if ($this->_isDropdown) {
                $options['tabindex'] = '-1';
                return "<li>{$link}</li>\n";
            }

            return $link;
        };
    }

    /**
     * @inheritDoc
     */
    protected function renderIcon(&$options, $iconOptions = [])
    {
        $icon = ArrayHelper::remove($options, 'icon');

        if ($icon === false) {
            $icon = '';
        } else {
            if (is_array($icon)) {
                $iconOptions = array_replace_recursive($iconOptions, $icon);
            }
            $tag = ArrayHelper::remove($iconOptions, 'tag', 'i');
            $icon = Html::tag($tag, $icon, $iconOptions);
        }
        return $icon;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];

            if (isset($this->visibleButtons[$name])) {
                $isVisible = $this->visibleButtons[$name] instanceof \Closure
                    ? call_user_func($this->visibleButtons[$name], $model, $key, $index)
                    : $this->visibleButtons[$name];
            } else {
                $isVisible = true;
            }

            if ($isVisible && isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);
                if (is_callable($this->buttons[$name])) {
                    return call_user_func($this->buttons[$name], $url, $model, $key);
                }

                $options = ArrayHelper::merge($this->buttonOptions, $this->buttons[$name]);
                $title = ArrayHelper::getValue($options, 'title', '');
                $label = $this->renderLabel($options, $title, ['class' => 'material-icons', 'aria-hidden' => 'true']);
                return Html::a($label, $url, $options);
            }

            return '';
        }, $this->template);
    }
}
