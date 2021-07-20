<?php


namespace antonyz89\mdl\widgets\chart;

use antonyz89\mdl\Html;
use antonyz89\mdl\widgets\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class LineChart
 * @package antonyz89\mdl\widgets
 */
class LineChart extends Widget
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->clientOptions = ArrayHelper::merge([
            'maxX' => 13,
            'xStep' => 1,
            'xDrawStep' => 4,
            'rowBgColor' => '#4a4a4a',
            'margin' => 20,
            'xAxis' => 'TIME',
            'yAxis' => 'REVENUE',
            'animationTime' => 400,
        ], $this->clientOptions);
    }


    public function run()
    {
        echo Html::tag('div', '', $this->options);
        $this->registerJs();
    }


    public function registerJs()
    {
        $data = $this->data;

        array_walk($this->clientOptions, static function (&$value, $key) {
            if ($key === 'title') {
                $value = str_replace('{total}', 0, $value);
            }

            $value = "$key: " . Json::encode($value) . ',';
        });

        $data_var = "data_$this->id";

        $script = /** @lang JavaScript */ "

        const $data_var = " . Json::encode($data) . ";

        let lineChartOptions = {
            container: d3.select('#$this->id'),
            data: $data_var,
            nv: nv,
            " . implode("\n", $this->clientOptions) . "
        };

        if (lineChartOptions.container[0][0]) {
            let lineChart = new LineChart(lineChartOptions);
            lineChart.drawChart();
        } else {
            console.error('Container \"#$this->id\" not found.');
        }
";

        $this->view->registerJs($script);

    }
}
