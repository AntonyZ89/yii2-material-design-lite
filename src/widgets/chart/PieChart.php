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
class PieChart extends Widget
{
    public $innerRadius = 0.86;
    public $outerRadius = 1.02;

    /**
     * params:
     * `image`: string - centered image
     * `append`: array - custom append
     * ~~~
     * [
     *   //'method' => 'value' (or ['param1', 'param2'])
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

        $this->options['class'] = ArrayHelper::getValue($this->options, 'class', 'pie-chart__container');
        $this->options['id'] = ArrayHelper::getValue($this->options, 'id', $this->getId());
        $this->setId($this->options['id']);

        $this->clientOptions = ArrayHelper::merge([
            'showLabels' => false,
            'donut' => true,
            'growOnHover' => true,
            'padAngle' => .04,
            'cornerRadius' => 0,
            'margin' => [
                'left' => -10,
                'right' => -10,
                'top' => -10,
                'bottom' => -10
            ],
            'arcsRadius' => array_map(function () {
                return ['inner' => $this->innerRadius, 'outer' => $this->outerRadius];
            }, range(1, count($this->clientOptions['data']))),
            'showLegend' => false,
            'titleOffset' => 10,

            'tooltip' => [
                'enabled' => true,
                'hideDelay' => 0,
                'headerEnabled' => false,
                'contentGenerator' => new JsExpression(/** @lang JavaScript */"d => {
                        if (d === null) {
                            return '';
                        }
                        d3.selectAll('.nvtooltip').classed('mdl-tooltip', true);
                        return '{$this->clientOptions['title']}'.replace('{total}', d.data.y);
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
        $data = $this->data;
        $title = ArrayHelper::getValue($this->clientOptions, 'title');

        array_walk($this->clientOptions, static function (&$value, $key) {
            if ($key === 'title') {
                $value = str_replace('{total}', 0, $value);
            }
            $value = ".$key(" . Json::encode($value) . ")";
        });

        $data_var = "data_$this->id";

        $script = /** @lang JavaScript */
            "
            const $data_var = " . Json::encode($data) . ";

            nv.addGraph(() => {
                let pieChart = nv.models.pieChart()
                    .x(d => {
                        return d.key;
                    })
                    .y(d => {
                        return d.y;
                    })
                    .color($colors)
                    " . implode("\n", $this->clientOptions) . ";

                let container = d3.select('#$this->id')
                    .append('div')
                    .append('svg')
                    .datum($data_var)
                    .transition().duration(1200)
                    .call(pieChart);

                let h = 0, i = 0;
                let timer = setInterval(animatePie, 70, $data_var);

                function animatePie(data) {
                    if (i < data.length - 1) {
                        if (data[i].y < data[i].end) {
                            data[i].y++;
                            data[data.length - 1].y--;
                            pieChart.title('$title'.replace('{total}', ++h));
                        } else {
                            i++;
                        }
                    } else {
                        data.splice(data.length - 1, 1);
                        clearInterval(timer);
                        return;
                    }
                    if (container[0][0]) {
                        pieChart.update();
                    } else {
                        clearInterval(timer);
                    }
                }

                $this->_append

                $tooltip

                const color = d3.scale.ordinal().range($colors);

                const legend = d3.select('#$this->id')
                    .append('div')
                    .attr('class', 'legend')
                    .selectAll('.legend__item')
                    .data($data_var)
                    .enter()
                    .append('div')
                    .attr('class', 'legend__item');

                legend.append('div')
                    .attr('class', 'legend__mark pull-left')
                    .style('background-color', d => color(d.key));

                legend.append('div')
                    .attr('class', 'legend__text')
                    .text(d => d.key);

                return pieChart;
            });
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
        $image = ArrayHelper::getValue($this->options, 'image');
        unset($this->options['image']);
        $append = ArrayHelper::getValue($this->options, 'append', []);
        unset($this->options['append']);

        if ($image) {
            $append['.nv-pie .nv-pie'] = [
                'append' => 'image',
                'attr' => [
                    'width' => '30',
                    'height' => '30',
                    'xlink:href' => $image,
                    'transform' => 'translate(-15,-35)'
                ],
            ];
        }

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

            $data = $this->getData();

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

                $this->_tooltip = 'pieChart.tooltip' . implode("\n", $this->_tooltip) . ';';
            }
        }

        return $this->_tooltip;
    }
}
