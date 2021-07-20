<?php

namespace antonyz89\mdl\widgets\chart;

use antonyz89\mdl\Html;
use antonyz89\mdl\widgets\Widget;
use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * @property-read string $tooltip
 * @property-read array $colors
 */
class BarChart extends Widget
{
    public $innerRadius = 0.86;
    public $outerRadius = 1.02;

    /**
     * params:
     * `image`: string - centered image
     * `append`: array - custom append
     * ~~~
     * [
     *   @usage 'method' => 'value' (or ['param1', 'param2'])
     *   'append' => 'image', // string or array
     *     'attr' => [
     *     'width' => '30',
     *     'height' => '30',
     *     'xlink:href' => 'images/watch_white.svg',
     *     'transform' => 'translate(-15,-35)'
     *   ],
     * ]
     * ~~~
     *
     * @var array
     */
    public $options = [];
    public $clientOptions = [];

    protected $_append;

    protected $_colors;
    protected $_tooltip;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->options['class'] = ArrayHelper::getValue($this->options, 'class', 'discrete-bar-chart__container');
        $this->options['id'] = ArrayHelper::getValue($this->options, 'id', $this->getId());

        $this->setId($this->options['id']);

        $this->clientOptions = ArrayHelper::merge([
            'margin' => [
                'left' => 40,
                'right' => 30,
                'top' => 10,
                'bottom' => 20
            ],
            'showLegend' => true,
            'showValues' => true,
            'rectClass' => 'bar',

            'tooltip' => [
                'enabled' => true,
                'hideDelay' => 0,
                'headerEnabled' => false,
                'contentGenerator' => new JsExpression(/** @lang JavaScript */"d => {
                    if (d === null) {
                        return '';
                    }
                    d3.selectAll('.nvtooltip').classed('mdl-tooltip', true);
                    return d.data.label;
                }"),
            ]
        ], $this->clientOptions);
    }

    public function run()
    {
        echo Html::tag('div', '', $this->options);
        $this->loadAppend();
        $this->registerJs();
    }

    /**
     * @throws Exception
     */
    protected function registerJs(): void
    {
        //BowerAsset::register($view);

        $colors = Json::encode($this->colors);
        $tooltip = $this->tooltip;
        $data = Json::encode($this->data);
        $showLegend = ArrayHelper::getValue($this->clientOptions, 'showLegend', true);

        array_walk($this->clientOptions, static function (&$value, $key) {
            if ($key === 'title') {
                $value = str_replace('{total}', 0, $value);
            }
            $value = ".$key(" . Json::encode($value) . ")";
        });

        $data_var = "data_$this->id";

        $script = /** @lang JavaScript */
            "
let container = d3.select('#$this->id');

if (container[0][0]) {
    let $data_var = $data;
    nv.addGraph(function () {
        let chart = nv.models.discreteBarChart()
            .x(function (d) {
                return d.label
            })
            .y(function (d) {
                return d.value
            })
            .color($colors)
            " . implode("\n", $this->clientOptions) . ";

        $this->_append

        $tooltip

        chart.yAxis
            .showMaxMin(false)
            .ticks(10)
        ;

        container.append('svg')
            .datum($data_var)
            .transition().duration(1200)
            .call(chart);

        nv.utils.windowResize(chart.update);

        " . (
            $showLegend ? /* @lang JavaScript */"
        let color = d3.scale.ordinal().range($colors);
        let legend = container.append('div')
            .attr('class', 'legend')
            .selectAll('.legend__item')
            .data({$data_var}[0].values)
            .enter()
            .append('div')
            .attr('class', 'legend__item');

        legend.append('div')
            .attr('class', 'legend__mark pull-left')
            .style('background-color', d => {
                return color(d.label);
            });

        legend.append('div')
            .attr('class', 'legend__text')
            .text(d => {
                return d.label;
            }); " : null
            ) . "

        return chart;
    });
}
";

        $this->view->registerJs($script);
    }

    /*
     * LOAD
     */

    /**
     * @throws Exception
     */
    protected function loadAppend()
    {
        $append = ArrayHelper::getValue($this->options, 'append', []);
        unset($this->options['append']);

        array_walk($append, function (&$value, $key) {
            $value = "d3.select('#$this->id $key')\n" . $this->parseAppend($value) . ';';
        });

        $this->_append = implode("\n", $append);
    }

    protected function parseAppend($append): string
    {
        $result = [];

        foreach ($append as $method => $value) {
            if (is_array($value)) {
                $has_string_keys = count(array_filter(array_keys($value), 'is_string')) > 0;

                if ($has_string_keys) {
                    foreach ($value as $attr => $val) {
                        $result [] = ".$method(" . Json::encode($attr) . ", " . Json::encode($val) . ")";
                    }
                    continue;
                }
            }

            $result [] = ".$method(" . Json::encode($value) . ")";
        }

        return implode("\n", $result);
    }

    /*
     * GET
     */

    /**
     * @return array
     * @throws Exception
     */
    protected function getColors(): array
    {
        if ($this->_colors === null) {
            $this->_colors = ArrayHelper::getValue($this->clientOptions, 'colors', []);
            unset($this->clientOptions['colors']);

            $data = array_flatten(array_map(static function ($value) {
                return array_map(static function ($value) {
                    return $value['value'];
                }, $value['values']);
            }, $this->getData()));

            $diff = count($data) - count($this->_colors);

            if ($diff < 0) {
                array_splice($this->_colors, $diff);
            } else if ($diff > 0) {
                loop(function () {
                    $this->_colors[] = random_color(); // TODO create random_color function if Material gone be exported
                }, $diff);
            }
        }

        return $this->_colors;
    }

    /**
     * @throws Exception
     */
    protected function getTooltip(): ?string
    {
        if ($this->_tooltip === null) {
            $this->_tooltip = ArrayHelper::getValue($this->clientOptions, 'tooltip');
            unset($this->clientOptions['tooltip']);

            if (!empty($this->_tooltip)) {
                array_walk($this->_tooltip, static function (&$value, $key) {
                    $value = ".$key(" . Json::encode($value) . ")";
                });

                $this->_tooltip = 'chart.tooltip' . implode("\n", $this->_tooltip) . ';';
            }
        }

        return $this->_tooltip;
    }
}
