Yii2 - Material Design Lite
===========================
Material Design Lite for Yii2

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist antonyz89/yii2-material-design-lite "*"
```

or add

```
"antonyz89/yii2-material-design-lite": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Before use:

1. Remove **bootstrap css**
2. Remove **Kartik's DialogBootstrap js and css**

```php
# common/config/main.php

[
    'components' => [
        'assetManager' => [
            //'forceCopy' => true,
            'converter' => [
                //'forceConvert' => true,
                'commands' => [
                    'scss' => ['css', 'sass {from} {to} --source-map'],
                ]
            ],
            'bundles' => [
                BootstrapAsset::class => ['css' => []],
                DialogBootstrapAsset::class => ['js' => [], 'css' => []]
            ]
        ]
    ] 
];
```

# FEATURES

* Material Design via Material Design Lite

* Ecmascript 6 (with babel)

* Responsive **dark** and **white** Material Design.

* User experience focused

* Sass

* D3 and NVD3

* MIT License

# CREDITS

* UI components built with [Material Design Lite](http://www.getmdl.io).
* [Material Dashboard Lite](https://github.com/CreativeIT/material-dashboard-lite) for awesome responsive dashboard with dark theme, components, charts and much more. ( Who this library is based )
* [Kartik](https://github.com/kartik-v) enhanced yii2's components

# Support the project
* Star the repo
* Create issue report or feature request