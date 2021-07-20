<?php

namespace antonyz89\mdl;

use yii\web\AssetBundle;

class MaterialAsset extends AssetBundle
{
    public $sourcePath = '@antonyz89/mdl/assets';

    public $css = [
        'application.scss',
    ];

    public $js = [
        'scroll/scroll.js',
        'layout/layout.js',
        'alert/alert.js',
        'lib/material-design-lite/material.js',
        'lib/material-design-lite/fix.js',
        'lib/modal/modal.js',

        //'widgets/charts/discreteBarChart.js',
        'widgets/charts/linePlusBarChart.js',
        'widgets/charts/stackedBarChart.js',
        'widgets/employer-form/employer-form.js',
        'widgets/map/maps.js',
        'widgets/line-chart/line-charts-nvd3.js',
        //'widgets/pie-chart/pie-charts-nvd3.js',
        'widgets/table/table.js',
        'widgets/todo/todo.js',
    ];

    public $depends = [
        BowerAsset::class
    ];

    /**
     * js/d3.js
     * js/d3.min.js
     * js/getmdl-select.min.js
     * js/material.js
     * js/material.min.js
     * js/nv.d3.js
     * js/nv.d3.min.js
     * js/layout/layout.js
     * js/layout/layout.min.js
     * js/scroll/scroll.js
     * js/scroll/scroll.min.js
     * js/widgets/charts/discreteBarChart.js
     * js/widgets/charts/discreteBarChart.min.js
     * js/widgets/charts/linePlusBarChart.js
     * js/widgets/charts/linePlusBarChart.min.js
     * js/widgets/charts/stackedBarChart.js
     * js/widgets/charts/stackedBarChart.min.js
     * js/widgets/employer-form/employer-form.js
     * js/widgets/employer-form/employer-form.min.js
     * js/widgets/line-chart/line-charts-nvd3.js
     * js/widgets/line-chart/line-charts-nvd3.min.js
     * js/widgets/map/maps.js
     * js/widgets/map/maps.min.js
     * js/widgets/pie-chart/pie-charts-nvd3.js
     * js/widgets/pie-chart/pie-charts-nvd3.min.js
     * js/widgets/table/table.js
     * js/widgets/table/table.min.js
     * js/widgets/todo/todo.js
     * js/widgets/todo/todo.min.js
     */
}
