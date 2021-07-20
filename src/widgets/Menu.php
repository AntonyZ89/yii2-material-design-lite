<?php

namespace antonyz89\mdl\widgets;

use antonyz89\mdl\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Menu as MenuBase;

class Menu extends MenuBase
{

    public $submenuTemplate = '<div class="mdl-navigation">{items}</div>';
    public $activeCssClass = 'mdl-navigation__link--current';

    protected function renderItems($items)
    {
        $items = array_map(function ($item) {
            $menu = $this->renderItem($item);

            if (!empty($item['items'])) {
                $submenuTemplate = ArrayHelper::getValue($item, 'submenuTemplate', $this->submenuTemplate);
                $submenu = str_replace('{items}', $this->renderItems($item['items']), $submenuTemplate);
                $menu = str_replace('{items}', $submenu, $menu);
            } else {
                $menu = str_replace('{items}', '', $menu);
            }
            return $menu;
        }, $items);

        return implode("\n", $items);
    }

    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        if (isset($item['items'])) {
            $linkTemplate = Html::tag('div',
                Html::a([
                    '{icon}',
                    '{label}',
                    Html::icon('keyboard_arrow_down'),
                ], '{url}', [
                    'class' => 'mdl-navigation__link {class}',
                    '{linkOptions}'
                ]) .
                '{items}'
                , 'sub-navigation');
        } else {
            $linkTemplate = Html::a([
                '{icon}',
                '{label}'
            ], '{url}', [
                'class' => 'mdl-navigation__link {class}',
                '{linkOptions}'
            ]);
        }

        $linkTemplate = preg_replace("/\d=\"([^\"]+)\"/", '$1', $linkTemplate);

        $linkOptions = [];
        $class=[];

        if (isset($item['linkOptions'])) {
            foreach ($item['linkOptions'] as $key => $value) {
                $linkOptions[] = "$key=\"$value\"";
            }
        }

        if ($item['active']) {
            $class[] = $this->activeCssClass;
        }

        $replacements = [
            '{linkOptions}' => implode(' ', $linkOptions),
            '{class}' => implode(' ', $class),
            '{label}' => $item['label'],
            '{icon}' => empty($item['icon']) ? '' : Html::icon($item['icon']),
            '{url}' => isset($item['url']) ? Url::to($item['url']) : 'javascript:void(0);',
        ];

        $template = ArrayHelper::getValue($item, 'template', $linkTemplate);

        return strtr($template, $replacements);
    }

}
