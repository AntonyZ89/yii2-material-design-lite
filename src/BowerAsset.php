<?php

namespace antonyz89\mdl;

use yii\web\AssetBundle;

class BowerAsset extends AssetBundle
{
    public $sourcePath = '@bower';

    public $js = [
        'd3/d3.min.js',
        'nvd3/build/nv.d3.min.js',
    ];
    public $css = [
        'nvd3/build/nv.d3.min.css'
    ];
}
